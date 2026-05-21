<x-user-layout>
    <x-slot name="header">Reminders</x-slot>
    <x-slot name="subheader">Get email alerts for salary, creditor payments, bills, and other financial tasks.</x-slot>
    <x-slot name="headerActions">
        <x-ui.button href="{{ route('reminders.create') }}">New reminder</x-ui.button>
    </x-slot>

    <div class="card-panel mb-4 small text-muted-foreground">
        Reminders are checked every hour. Email is sent to your account address when a reminder is due. Use your timezone from Settings for accurate times.
    </div>

    <div class="grid grid-cols-12 gap-3">
        @forelse ($reminders as $reminder)
            <div class="col-span-12 lg:col-span-6">
                <div class="card-panel h-full {{ $reminder->is_active ? '' : 'opacity-75' }}">
                    <div class="flex align-items-start justify-between gap-2 mb-2">
                        <div>
                            <span class="badge {{ $reminder->type->badgeClass() }} mb-2">{{ $reminder->type->label() }}</span>
                            <h3 class="h6 font-semibold mb-1">{{ $reminder->title }}</h3>
                            @if ($reminder->payee_name)
                                <p class="text-sm text-muted-foreground mb-1">Payee: {{ $reminder->payee_name }}</p>
                            @endif
                            @if ($reminder->amount !== null)
                                <p class="text-sm mb-1">Amount: <strong>{{ $reminder->formattedAmount(auth()->user()) }}</strong></p>
                            @endif
                        </div>
                        <div class="text-right">
                            @if ($reminder->is_active)
                                <span class="badge text-bg-success">Active</span>
                            @else
                                <span class="badge text-bg-secondary">Paused</span>
                            @endif
                        </div>
                    </div>

                    <ul class="list-none small text-muted-foreground mb-3">
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
                        <p class="text-sm text-muted-foreground mb-3">{{ Str::limit($reminder->notes, 120) }}</p>
                    @endif

                    <div class="flex flex-wrap gap-2">
                        <x-ui.button variant="outline" size="sm" href="{{ route('reminders.edit', $reminder) }}">Edit</x-ui.button>
                        <form method="POST" action="{{ route('reminders.toggle', $reminder) }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                {{ $reminder->is_active ? 'Pause' : 'Activate' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('reminders.destroy', $reminder) }}" onsubmit="return confirm('Delete this reminder?')">
                            @csrf
                            @method('DELETE')
                            <x-ui.button type="submit" variant="destructive" size="sm">Delete</x-ui.button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-12">
                <div class="card-panel text-center text-muted-foreground py-5">
                    <p class="mb-3">No reminders yet. Create one for salary day, paying a creditor, or any recurring task.</p>
                    <x-ui.button href="{{ route('reminders.create') }}">Create your first reminder</x-ui.button>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-3">{{ $reminders->links() }}</div>
</x-user-layout>
