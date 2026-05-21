<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Review;
use App\Services\SiteContentService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function __construct(
        private readonly SiteContentService $siteContent,
    ) {}

    public function home(): View
    {
        $reviews = $this->approvedReviewsForCarousel();

        return view('pages.home', [
            'page' => $this->siteContent->get('home'),
            'reviews' => $reviews,
        ]);
    }

    public function features(): View
    {
        return view('pages.features', [
            'page' => $this->siteContent->get('features'),
        ]);
    }

    public function pricing(): View
    {
        return view('pages.pricing', [
            'page' => $this->siteContent->get('pricing'),
        ]);
    }

    public function about(): View
    {
        return view('pages.about', [
            'page' => $this->siteContent->get('about'),
        ]);
    }

    public function faq(): View
    {
        return view('pages.faq', [
            'page' => $this->siteContent->get('faq'),
        ]);
    }

    public function contact(): View
    {
        return view('pages.contact', [
            'page' => $this->siteContent->get('contact'),
        ]);
    }

    public function sendContact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        ContactMessage::query()->create($validated);

        $page = $this->siteContent->get('contact');

        return redirect()
            ->route('contact')
            ->with('success', $page->extra('success_message', 'Message received! Our team will review it and reply to your email soon.'));
    }

    public function privacy(): View
    {
        return view('pages.privacy', [
            'page' => $this->siteContent->get('privacy'),
        ]);
    }

    public function terms(): View
    {
        return view('pages.terms', [
            'page' => $this->siteContent->get('terms'),
        ]);
    }

    public function show(string $slug): View
    {
        $page = $this->siteContent->get($slug);

        if ($page->isSystem()) {
            abort(404);
        }

        $reviews = collect();

        if ($page->hasReviewsSection()) {
            $reviews = $this->approvedReviewsForCarousel();
        }

        return view('pages.show', [
            'page' => $page,
            'reviews' => $reviews,
        ]);
    }

    /** @return Collection<int, Review> */
    private function approvedReviewsForCarousel()
    {
        return Review::query()
            ->approved()
            ->with(['user:id,name,avatar_path,google_id,google_avatar'])
            ->latest('approved_at')
            ->limit(6)
            ->get(['id', 'user_id', 'display_name', 'rating', 'content', 'approved_at']);
    }
}
