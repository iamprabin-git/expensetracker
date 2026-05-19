@php
    $links = [
        ['route' => 'home', 'label' => 'Home', 'patterns' => ['home']],
        ['route' => 'features', 'label' => 'Features', 'patterns' => ['features']],
        ['route' => 'pricing', 'label' => 'Pricing', 'patterns' => ['pricing']],
        ['route' => 'about', 'label' => 'About', 'patterns' => ['about']],
        ['route' => 'faq', 'label' => 'FAQ', 'patterns' => ['faq']],
        ['route' => 'contact', 'label' => 'Contact', 'patterns' => ['contact']],
    ];
@endphp

@foreach ($links as $link)
    <li class="nav-item">
        <a @class([
            'nav-link site-nav-link',
            'active' => request()->routeIs($link['patterns']),
        ]) href="{{ route($link['route']) }}">
            {{ $link['label'] }}
        </a>
    </li>
@endforeach
