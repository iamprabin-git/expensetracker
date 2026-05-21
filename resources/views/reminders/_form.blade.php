@php
    use App\Enums\ReminderFrequency;
    use App\Enums\ReminderType;

    $reminder = $reminder ?? null;
    $isEdit = $reminder !== null;
    $action = $isEdit ? route('reminders.update', $reminder) : route('reminders.store');
    $selectedType = old('type', $reminder?->type?->value ?? ReminderType::Salary->value);
    $defaultTitle = ReminderType::tryFrom($selectedType)?->defaultTitle() ?? '';
    $remindAt = $reminder?->next_remind_at ?? now()->addDay();
    if (auth()->user()->timezone) {
        $remindAt = $remindAt->timezone(auth()->user()->timezone);
    }
@endphp

<div class="card-panel" x-data="{
    type: @js($selectedType),
    defaultTitles: @js(collect(ReminderType::cases())->mapWithKeys(fn ($t) => [$t->value => $t->defaultTitle()])->all()),
    title: @js(old('title', $reminder?->title ?? $defaultTitle)),
    setTitleFromType() {
        if (!this.title || Object.values(this.defaultTitles).includes(this.title)) {
            this.title = this.defaultTitles[this.type] ?? '';
        }
    }
}">
    <form method="POST" action="{{ $action }}" class="grid grid-cols-12 gap-3">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="col-span-12 md:col-span-6">
            <label class="label-app" for="type">Reminder type</label>
            <select name="type" id="type" x-model="type" @change="setTitleFromType()" class="input-app flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs @error('type') is-invalid @enderror" required>
                @foreach (ReminderType::cases() as $typeOption)
                    <option value="{{ $typeOption->value }}" @selected($selectedType === $typeOption->value)>{{ $typeOption->label() }}</option>
                @endforeach
            </select>
            @error('type')<div class="mt-1 text-sm text-destructive">{{ $message }}</div>@enderror
        </div>

        <div class="col-span-12 md:col-span-6">
            <label class="label-app" for="frequency">Repeat</label>
            <select name="frequency" id="frequency" class="input-app flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs @error('frequency') is-invalid @enderror" required>
                @foreach (ReminderFrequency::cases() as $freq)
                    <option value="{{ $freq->value }}" @selected(old('frequency', $reminder?->frequency?->value ?? ReminderFrequency::Monthly->value) === $freq->value)>
                        {{ $freq->label() }}
                    </option>
                @endforeach
            </select>
            @error('frequency')<div class="mt-1 text-sm text-destructive">{{ $message }}</div>@enderror
        </div>

        <div class="col-span-12">
            <label class="label-app" for="title">Title</label>
            <input type="text" name="title" id="title" x-model="title" value="{{ old('title', $reminder?->title ?? '') }}" class="input-app flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs @error('title') is-invalid @enderror" required>
            @error('title')<div class="mt-1 text-sm text-destructive">{{ $message }}</div>@enderror
        </div>

        <div class="col-span-12 md:col-span-6" x-show="['creditor_payment','bill_due','subscription'].includes(type)" x-cloak>
            <label class="label-app" for="payee_name">Creditor / payee name</label>
            <input type="text" name="payee_name" id="payee_name" value="{{ old('payee_name', $reminder?->payee_name ?? '') }}" class="input-app flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs @error('payee_name') is-invalid @enderror" placeholder="e.g. Bank, landlord, utility">
            @error('payee_name')<div class="mt-1 text-sm text-destructive">{{ $message }}</div>@enderror
        </div>

        <div class="col-span-12 md:col-span-6">
            <label class="label-app" for="amount">Amount (optional)</label>
            <input type="number" step="0.01" min="0" name="amount" id="amount" value="{{ old('amount', $reminder?->amount) }}" class="input-app flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs @error('amount') is-invalid @enderror" placeholder="Expected salary or payment">
            @error('amount')<div class="mt-1 text-sm text-destructive">{{ $message }}</div>@enderror
        </div>

        <div class="col-span-12 md:col-span-6">
            <label class="label-app" for="remind_date">Remind on date</label>
            <input type="date" name="remind_date" id="remind_date" value="{{ old('remind_date', $remindAt->format('Y-m-d')) }}" class="input-app flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs @error('remind_date') is-invalid @enderror" required>
            @error('remind_date')<div class="mt-1 text-sm text-destructive">{{ $message }}</div>@enderror
        </div>

        <div class="col-span-12 md:col-span-6">
            <label class="label-app" for="remind_time">Remind at time</label>
            <input type="time" name="remind_time" id="remind_time" value="{{ old('remind_time', $remindAt->format('H:i')) }}" class="input-app flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs @error('remind_time') is-invalid @enderror" required>
            @error('remind_time')<div class="mt-1 text-sm text-destructive">{{ $message }}</div>@enderror
        </div>

        <div class="col-span-12">
            <label class="label-app" for="notes">Notes (optional)</label>
            <textarea name="notes" id="notes" rows="3" class="input-app flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs @error('notes') is-invalid @enderror" placeholder="Account number, reference, or extra details">{{ old('notes', $reminder?->notes) }}</textarea>
            @error('notes')<div class="mt-1 text-sm text-destructive">{{ $message }}</div>@enderror
        </div>

        <div class="col-span-12">
            <div class="form-check">
                <input type="hidden" name="notify_email" value="0">
                <input type="checkbox" name="notify_email" id="notify_email" value="1" class="size-4 rounded border border-input text-primary" @checked(old('notify_email', $reminder?->notify_email ?? true))>
                <label class="text-sm font-medium leading-none" for="notify_email">Send email reminder when due</label>
            </div>
        </div>

        @if ($isEdit)
            <div class="col-span-12">
                <div class="form-check">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1" class="size-4 rounded border border-input text-primary" @checked(old('is_active', $reminder->is_active))>
                    <label class="text-sm font-medium leading-none" for="is_active">Reminder is active</label>
                </div>
            </div>
        @endif

        <div class="col-span-12 flex flex-wrap gap-2">
            <x-ui.button type="submit">{{ $isEdit ? 'Update' : 'Create' }} reminder</x-ui.button>
            <x-ui.button variant="outline" href="{{ route('reminders.index') }}">Cancel</x-ui.button>
        </div>
    </form>
</div>
