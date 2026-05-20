@php
    $contactInfo = collect($page->sectionList())->firstWhere('type', 'contact_info');
    $contactItems = collect($contactInfo['items'] ?? []);

    if ($company?->email && ! $contactItems->contains(fn ($i) => ($i['title'] ?? '') === 'Email')) {
        $contactItems->prepend(['title' => 'Email', 'text' => $company->email]);
    }
    if ($company?->phone && ! $contactItems->contains(fn ($i) => ($i['title'] ?? '') === 'Phone')) {
        $contactItems->push(['title' => 'Phone', 'text' => $company->phone]);
    }
    if ($company?->support_hours && ! $contactItems->contains(fn ($i) => ($i['title'] ?? '') === 'Hours')) {
        $contactItems->push(['title' => 'Hours', 'text' => $company->support_hours]);
    }
    if ($company?->formattedAddress() && ! $contactItems->contains(fn ($i) => ($i['title'] ?? '') === 'Address')) {
        $contactItems->push(['title' => 'Address', 'text' => $company->formattedAddress()]);
    }
@endphp

<x-marketing-layout :title="$page->title" :metaDescription="$page->meta_description">
    @include('pages.partials.cms-hero', ['page' => $page])

    <section class="site-section pt-0">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-5">
                    <div class="card-panel h-100">
                        <h2 class="h5 fw-semibold mb-3">{{ $page->extra('sidebar_title', 'Get in touch') }}</h2>
                        <ul class="list-unstyled text-secondary small mb-0 d-grid gap-3">
                            @foreach ($contactItems as $item)
                                <li>
                                    @if (! empty($item['title']))
                                        <strong class="text-body">{{ $item['title'] }}</strong><br>
                                    @endif
                                    @if (($item['title'] ?? '') === 'Email' && ! empty($item['text']))
                                        <a href="mailto:{{ $item['text'] }}">{{ $item['text'] }}</a>
                                    @else
                                        {!! nl2br(e($item['text'] ?? '')) !!}
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="card-panel">
                        <h2 class="h5 fw-semibold mb-3">{{ $page->extra('form_title', 'Send a message') }}</h2>
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
