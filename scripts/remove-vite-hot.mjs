import { unlinkSync } from 'node:fs';
import { join } from 'node:path';

const hot = join('public', 'hot');

try {
    unlinkSync(hot);
    console.log('Removed public/hot (use built assets in public/build).');
} catch {
    // already absent
}
