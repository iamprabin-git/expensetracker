<x-marketing-layout :title="$page->title" :metaDescription="$page->meta_description">
    @include('pages.partials.cms-hero', ['page' => $page])

    @if ($page->body_html)
        <section class="site-section pt-0">
            <div class="mx-auto w-full max-w-6xl px-4 site-legal" style="max-width: 48rem;">
                {!! \App\Support\SafeHtml::clean($page->body_html) !!}
            </div>
        </section>
    @endif
</x-marketing-layout>
