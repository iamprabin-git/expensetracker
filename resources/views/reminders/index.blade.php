<x-user-layout>
    <x-slot name="header">Reminders</x-slot>
    <x-slot name="subheader">Get email alerts for salary, creditor payments, bills, and other financial tasks.</x-slot>
    <x-slot name="headerActions">
        <a href="{{ route('reminders.create') }}" class="btn-primary-app">New reminder</a>
    </x-slot>

    <div class="card-panel mb-4 small text-secondary">
        Reminders are checked every hour. Email is sent to your account address when a reminder is due. Use your timezone from Settings for accurate times.
    </div>

    <div class="row g-3">
        @forelse ($reminders as $reminder)
            <div class="col-12 col-lg-6">
                <div class="card-panel h-100 {{ $reminder->is_active ? '' : 'opacity-75' }}">
                    <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                        <div>
                            <span class="badge {{ $reminder->type->badgeClass() }} mb-2">{{ $reminder->type->label() }}</span>
                            <h3 class="h6 fw-semibold mb-1">{{ $reminder->title }}</h3>
                            @if ($reminder->payee_name)
                                <p class="small text-secondary mb-1">Payee: {{ $reminder->payee_name }}</p>
                            @endif
                            @if ($reminder->amount !== null)
                                <p class="small mb-1">Amount: <strong>{{ $reminder->formattedAmount(auth()->user()) }}</strong></p>
                            @endif
                        </div>
                        <div class="text-end">
                            @if ($reminder->is_active)
                                <span class="badge text-bg-success">Active</span>
                            @else
                                <span class="badge text-bg-secondary">Paused</span>
                            @endif
                        </div>
                    </div>

                    <ul class="list-unstyled small text-secondary mb-3">
                        <li><strong>Next:</strong> {{ $reminder->next_remind_at->timezone(auth()->user()->timezone ?? config('app.timezone'))->format('M d, Y g:i A') }}</li>
                        <li><strong>Repeats:</strong> {{ $reminder->frequency->label() }}</li>
                        <li>
                            <strong>Email:</strong>
                            {{ $reminder->notify_email ? 'On' : 'Off' }}
                            @if ($reminder->last_sent_at)
                                · Last sent {{ $reminder->last_sent_at->diffForHumans() }}
                            @endif
                        </li>
                    </ul>

                    @if ($reminder->notes)
                        <p class="small text-secondary mb-3">{{ Str::limit($reminder->notes, 120) }}</p>
                    @endif

                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('reminders.edit', $reminder) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="POST" action="{{ route('reminders.toggle', $reminder) }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                {{ $reminder->is_active ? 'Pause' : 'Activate' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('reminders.destroy', $reminder) }}" onsubmit="return confirm('Delete this reminder?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card-panel text-center text-secondary py-5">
                    <p class="mb-3">No reminders yet. Create one for salary day, paying a creditor, or any recurring task.</p>
                    <a href="{{ route('reminders.create') }}" class="btn-primary-app">Create your first reminder</a>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-3">{{ $reminders->links() }}</div>
</x-user-layout>
