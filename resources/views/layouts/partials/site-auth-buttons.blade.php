@php $mobile = $mobile ?? false; @endphp

<div @class(['site-header__auth-group', 'site-header__auth-group--mobile' => $mobile])>
    @auth
        <a href="{{ auth()->user()->isAdmin() ? url('/admin') : route('dashboard') }}" class="btn site-btn-primary {{ $mobile ? 'w-100' : 'btn-sm' }}" @if($mobile) data-site-header-link @endif>
            Dashboard
        </a>
    @else
        <a href="{{ route('login') }}" class="btn site-btn-outline {{ $mobile ? 'w-100' : 'btn-sm' }}" @if($mobile) data-site-header-link @endif>Log in</a>
        <a href="{{ route('register') }}" class="btn site-btn-primary {{ $mobile ? 'w-100' : 'btn-sm' }}" @if($mobile) data-site-header-link @endif>Sign up free</a>
    @endauth
</div>
