/**
 * Final-pass: convert (width<=…) media queries in emitted CSS for older mobile browsers.
 */
export function legacyMediaQueriesBundle() {
    const pattern =
        /\(width>=([^)]+)\)|\(width<=([^)]+)\)|\(width>([^)]+)\)|\(width<([^)]+)\)|\(height>=([^)]+)\)|\(height<=([^)]+)\)|\(height>([^)]+)\)|\(height<([^)]+)\)/gi;

    const replace = (match) => {
        const m = match.match(
            /^\((width|height)([<>]=?)([^)]+)\)$/i,
        );
        if (!m) {
            return match;
        }

        const [, axis, op, value] = m;
        const prop = axis.toLowerCase() === 'height' ? 'height' : 'width';

        return {
            '>=': `(min-${prop}: ${value})`,
            '<=': `(max-${prop}: ${value})`,
            '>': `(min-${prop}: ${value})`,
            '<': `(max-${prop}: ${value})`,
        }[op] ?? match;
    };

    return {
        name: 'legacy-media-queries-bundle',
        apply: 'build',
        generateBundle(_, bundle) {
            for (const chunk of Object.values(bundle)) {
                if (chunk.type !== 'asset' || !chunk.fileName.endsWith('.css')) {
                    continue;
                }

                const source =
                    typeof chunk.source === 'string'
                        ? chunk.source
                        : new TextDecoder().decode(chunk.source);

                chunk.source = source.replace(pattern, (full) => replace(full));
            }
        },
    };
}
