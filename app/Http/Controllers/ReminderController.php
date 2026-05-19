<?php

namespace App\Http\Controllers;

use App\Enums\ReminderFrequency;
use App\Enums\ReminderType;
use App\Models\Reminder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ReminderController extends Controller
{
    public function index(Request $request): View
    {
        $reminders = Reminder::query()
            ->where('user_id', $request->user()->id)
            ->orderBy('is_active', 'desc')
            ->orderBy('next_remind_at')
            ->paginate(12);

        return view('reminders.index', compact('reminders'));
    }

    public function create(): View
    {
        return view('reminders.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateReminder($request);

        $request->user()->reminders()->create($validated);

        return redirect()
            ->route('reminders.index')
            ->with('success', 'Reminder created. You will receive an email when it is due.');
    }

    public function edit(Request $request, Reminder $reminder): View
    {
        $this->authorizeReminder($request, $reminder);

        return view('reminders.edit', compact('reminder'));
    }

    public function update(Request $request, Reminder $reminder): RedirectResponse
    {
        $this->authorizeReminder($request, $reminder);

        $reminder->update($this->validateReminder($request));

        return redirect()
            ->route('reminders.index')
            ->with('success', 'Reminder updated.');
    }

    public function destroy(Request $request, Reminder $reminder): RedirectResponse
    {
        $this->authorizeReminder($request, $reminder);

        $reminder->delete();

        return redirect()
            ->route('reminders.index')
            ->with('success', 'Reminder deleted.');
    }

    public function toggle(Request $request, Reminder $reminder): RedirectResponse
    {
        $this->authorizeReminder($request, $reminder);

        $reminder->update(['is_active' => ! $reminder->is_active]);

        return back()->with('success', $reminder->is_active ? 'Reminder activated.' : 'Reminder paused.');
    }

    protected function validateReminder(Request $request): array
    {
        $validated = $request->validate([
            'type' => ['required', Rule::enum(ReminderType::class)],
            'title' => ['required', 'string', 'max:255'],
            'payee_name' => ['nullable', 'string', 'max:255'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'frequency' => ['required', Rule::enum(ReminderFrequency::class)],
            'remind_date' => ['required', 'date'],
            'remind_time' => ['required', 'date_format:H:i'],
            'notify_email' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $user = $request->user();

        return [
            'type' => $validated['type'],
            'title' => $validated['title'],
            'payee_name' => $validated['payee_name'] ?? null,
            'amount' => $validated['amount'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'frequency' => $validated['frequency'],
            'next_remind_at' => Reminder::buildNextRemindAt(
                $validated['remind_date'],
                $validated['remind_time'],
                ReminderFrequency::from($validated['frequency']),
                $user->timezone,
            ),
            'notify_email' => $request->boolean('notify_email', true),
            'is_active' => $request->boolean('is_active', true),
        ];
    }

    protected function authorizeReminder(Request $request, Reminder $reminder): void
    {
        abort_unless($reminder->user_id === $request->user()->id, 403);
    }
}
