@php
    $mobile = $mobile ?? false;

    $marketingLinks = [
        ['route' => 'home', 'label' => 'Home', 'patterns' => ['home']],
        ['route' => 'features', 'label' => 'Features', 'patterns' => ['features']],
        ['route' => 'pricing', 'label' => 'Pricing', 'patterns' => ['pricing']],
        ['route' => 'about', 'label' => 'About', 'patterns' => ['about']],
        ['route' => 'faq', 'label' => 'FAQ', 'patterns' => ['faq']],
        ['route' => 'contact', 'label' => 'Contact', 'patterns' => ['contact']],
    ];

    $appLinks = [
        ['route' => 'dashboard', 'label' => 'Dashboard', 'patterns' => ['dashboard']],
        ['route' => 'transactions.index', 'label' => 'Transactions', 'patterns' => ['transactions.*']],
        ['route' => 'categories.index', 'label' => 'Categories', 'patterns' => ['categories.*']],
        ['route' => 'settings.index', 'label' => 'Settings', 'patterns' => ['settings.*', 'profile.*']],
    ];

    $links = ($context ?? 'marketing') === 'app' ? $appLinks : $marketingLinks;
@endphp

@foreach ($links as $link)
    <li class="site-header__menu-item">
        <a
            href="{{ route($link['route']) }}"
            @class(['site-nav-link', 'active' => request()->routeIs($link['patterns'])])
            @if ($mobile) data-site-header-link @endif
        >{{ $link['label'] }}</a>
    </li>
@endforeach

@if (($context ?? 'marketing') === 'app' && $mobile)
    <li class="site-header__menu-item">
        <a href="{{ route('home') }}" class="site-nav-link" data-site-header-link>Website</a>
    </li>
@endif
