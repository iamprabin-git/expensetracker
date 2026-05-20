<x-marketing-layout :title="$page->title" :metaDescription="$page->meta_description">
    @include('pages.partials.cms-hero', ['page' => $page])
    @include('pages.partials.cms-sections', ['page' => $page, 'reviews' => $reviews])
</x-marketing-layout>
