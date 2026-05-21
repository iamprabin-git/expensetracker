@props(['items' => []])

@if (count($items) > 0)
    <nav class="site-breadcrumb" aria-label="Breadcrumb">
        <ol class="site-breadcrumb__list">
            @foreach ($items as $item)
                <li class="site-breadcrumb__item">
                    @if (! empty($item['url']))
                        <a href="{{ $item['url'] }}" class="site-breadcrumb__link">{{ $item['label'] }}</a>
                    @else
                        <span class="site-breadcrumb__current" aria-current="page">{{ $item['label'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
