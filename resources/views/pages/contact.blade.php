<x-marketing-layout title="Contact" metaDescription="Contact the ExpenseTracker team for support, sales, or partnership inquiries.">
    @include('pages.partials.page-hero', [
        'badge' => 'Contact',
        'title' => 'We would love to hear from you',
        'lead' => 'Questions about your account, pricing, or partnerships? Send us a message.',
    ])

    <section class="site-section pt-0">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-5">
                    <div class="card-panel h-100">
                        <h2 class="h5 fw-semibold mb-3">Get in touch</h2>
                        <ul class="list-unstyled text-secondary small mb-0 d-grid gap-3">
                            <li><strong class="text-body">Email</strong><br>support@expensetracker.test</li>
                            <li><strong class="text-body">Hours</strong><br>Mon–Fri, 9am–6pm (UTC)</li>
                            <li><strong class="text-body">Response time</strong><br>Within 2 business days</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card-panel">
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        <form method="POST" action="{{ route('contact.send') }}" class="row g-3">
                            @csrf
                            <div class="col-md-6">
                                <label class="label-app" for="name">Name</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" class="input-app form-control @error('name') is-invalid @enderror" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="label-app" for="email">Email</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" class="input-app form-control @error('email') is-invalid @enderror" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="label-app" for="subject">Subject</label>
                                <input type="text" name="subject" id="subject" value="{{ old('subject') }}" class="input-app form-control @error('subject') is-invalid @enderror" required>
                                @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="label-app" for="message">Message</label>
                                <textarea name="message" id="message" rows="5" class="input-app form-control @error('message') is-invalid @enderror" required>{{ old('message') }}</textarea>
                                @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary site-btn-primary px-4">Send message</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-marketing-layout>
