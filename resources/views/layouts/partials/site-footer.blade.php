<footer class="site-footer">
    <div class="site-footer__top">
        <div class="container">
            <div class="row g-4 g-xl-5 align-items-start">
                {{-- Brand column --}}
                <div class="col-12 col-lg-4">
                    <a href="{{ route('home') }}" class="site-brand site-brand--footer">
                        <span class="site-brand__icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="22" height="22">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797-2.101c6.27 1.645 10.53 4.978 12.453 7.75M2.25 9.75v10.5m0-10.5c0-3.75 3.75-6.75 8.25-6.75s8.25 3 8.25 6.75m-16.5 0v10.5" />
                            </svg>
                        </span>
                        <span>Expense<span class="site-brand__accent">Tracker</span></span>
                    </a>
                    <p class="site-footer__lead">
                        Professional income and expense tracking with a clean dashboard, smart categories, and secure multi-user access.
                    </p>
                    <div class="site-footer__socials">
                        <a href="#" class="site-footer__social" aria-label="Twitter"><span aria-hidden="true">𝕏</span></a>
                        <a href="#" class="site-footer__social" aria-label="LinkedIn"><span aria-hidden="true">in</span></a>
                        <a href="#" class="site-footer__social" aria-label="GitHub"><span aria-hidden="true">GH</span></a>
                    </div>
                </div>

                {{-- Desktop link columns --}}
                <div class="col-6 col-md-4 col-lg-2 d-none d-md-block">
                    <h3 class="site-footer__title">Product</h3>
                    <ul class="site-footer__links">
                        <li><a href="{{ route('features') }}">Features</a></li>
                        <li><a href="{{ route('pricing') }}">Pricing</a></li>
                        <li><a href="{{ route('faq') }}">FAQ</a></li>
                        <li><a href="{{ route('register') }}">Get started</a></li>
                    </ul>
                </div>
                <div class="col-6 col-md-4 col-lg-2 d-none d-md-block">
                    <h3 class="site-footer__title">Company</h3>
                    <ul class="site-footer__links">
                        <li><a href="{{ route('about') }}">About</a></li>
                        <li><a href="{{ route('contact') }}">Contact</a></li>
                        <li><a href="{{ route('filament.admin.auth.login') }}">Admin login</a></li>
                    </ul>
                </div>
                <div class="col-12 col-md-4 col-lg-2 d-none d-md-block">
                    <h3 class="site-footer__title">Legal</h3>
                    <ul class="site-footer__links">
                        <li><a href="{{ route('privacy') }}">Privacy</a></li>
                        <li><a href="{{ route('terms') }}">Terms</a></li>
                    </ul>
                </div>

                {{-- Newsletter --}}
                <div class="col-12 col-lg-4">
                    <h3 class="site-footer__title">Stay updated</h3>
                    <p class="site-footer__text mb-3">Tips and product news. Unsubscribe anytime.</p>
                    <form class="site-footer__newsletter" onsubmit="return false;">
                        <input type="email" class="site-footer__input form-control" placeholder="Email address" aria-label="Email">
                        <button type="button" class="btn site-footer__submit">Subscribe</button>
                    </form>
                </div>
            </div>

            {{-- Mobile accordion (custom — no Bootstrap collapse) --}}
            <div class="site-footer__accordion d-md-none mt-2" data-site-footer-accordion>
                <div class="site-footer__accordion-item" data-site-footer-item>
                    <button
                        class="site-footer__accordion-btn collapsed"
                        type="button"
                        data-site-footer-toggle
                        aria-expanded="false"
                        aria-controls="footerProduct"
                    >Product</button>
                    <div id="footerProduct" class="site-footer__panel" data-site-footer-panel>
                        <ul class="site-footer__links site-footer__links--accordion">
                            <li><a href="{{ route('features') }}">Features</a></li>
                            <li><a href="{{ route('pricing') }}">Pricing</a></li>
                            <li><a href="{{ route('faq') }}">FAQ</a></li>
                            <li><a href="{{ route('register') }}">Get started</a></li>
                        </ul>
                    </div>
                </div>
                <div class="site-footer__accordion-item" data-site-footer-item>
                    <button
                        class="site-footer__accordion-btn collapsed"
                        type="button"
                        data-site-footer-toggle
                        aria-expanded="false"
                        aria-controls="footerCompany"
                    >Company</button>
                    <div id="footerCompany" class="site-footer__panel" data-site-footer-panel>
                        <ul class="site-footer__links site-footer__links--accordion">
                            <li><a href="{{ route('about') }}">About</a></li>
                            <li><a href="{{ route('contact') }}">Contact</a></li>
                            <li><a href="{{ route('filament.admin.auth.login') }}">Admin login</a></li>
                        </ul>
                    </div>
                </div>
                <div class="site-footer__accordion-item" data-site-footer-item>
                    <button
                        class="site-footer__accordion-btn collapsed"
                        type="button"
                        data-site-footer-toggle
                        aria-expanded="false"
                        aria-controls="footerLegal"
                    >Legal</button>
                    <div id="footerLegal" class="site-footer__panel" data-site-footer-panel>
                        <ul class="site-footer__links site-footer__links--accordion">
                            <li><a href="{{ route('privacy') }}">Privacy</a></li>
                            <li><a href="{{ route('terms') }}">Terms</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="site-footer__bottom">
        <div class="container">
            <div class="site-footer__bottom-inner">
                <p class="site-footer__copy mb-0">&copy; {{ date('Y') }} {{ config('app.name', 'ExpenseTracker') }}. All rights reserved.</p>
                <div class="site-footer__bottom-links">
                    <a href="{{ route('privacy') }}">Privacy</a>
                    <span aria-hidden="true">·</span>
                    <a href="{{ route('terms') }}">Terms</a>
                    <span aria-hidden="true">·</span>
                    <a href="{{ route('contact') }}">Support</a>
                    <span aria-hidden="true">·</span>
                    <a href="{{ route('filament.admin.auth.login') }}">Admin login</a>
                </div>
            </div>
        </div>
    </div>
</footer>
