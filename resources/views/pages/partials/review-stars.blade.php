@props(['rating', 'size' => 'sm'])

@php
    $sizeClass = $size === 'lg' ? 'size-5' : 'size-4';
@endphp

<div {{ $attributes->merge(['class' => 'flex shrink-0 gap-0.5 text-amber-500 dark:text-amber-400']) }} aria-label="{{ $rating }} out of 5 stars">
    @for ($i = 1; $i <= 5; $i++)
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" @class([$sizeClass, $i <= $rating ? '' : 'opacity-25'])>
            <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.052 2.52c-.192.46-.597.788-1.065.98l-2.52 1.052a.92.92 0 00-.53 1.567l1.919 1.566c.365.298.526.78.433 1.24l-.6 2.47a.92.92 0 001.33 1.003l2.194-1.337a.92.92 0 011.124 0l2.194 1.337a.92.92 0 001.33-1.003l-.6-2.47a.92.92 0 00.433-1.24l1.919-1.566a.92.92 0 00-.53-1.567l-2.52-1.052a.92.92 0 00-1.065-.98l-1.052-2.52z" clip-rule="evenodd" />
        </svg>
    @endfor
</div>
