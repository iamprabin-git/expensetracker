<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'display_name' => ['required', 'string', 'max:80'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'content' => ['required', 'string', 'min:20', 'max:2000'],
        ]);

        $userId = $request->user()?->id;

        if ($userId) {
            $hasPending = Review::query()
                ->where('user_id', $userId)
                ->where('is_approved', false)
                ->exists();

            if ($hasPending) {
                return back()->with('info', 'You already have a review awaiting approval.');
            }
        }

        Review::query()->create([
            'user_id' => $userId,
            'display_name' => $validated['display_name'],
            'rating' => $validated['rating'],
            'content' => $validated['content'],
            'is_approved' => false,
        ]);

        return back()->with('success', 'Thank you! Your review was submitted and will appear on the site after admin approval.');
    }
}
