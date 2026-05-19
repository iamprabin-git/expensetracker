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
    <form method="POST" action="{{ $action }}" class="row g-3">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="col-12 col-md-6">
            <label class="label-app" for="type">Reminder type</label>
            <select name="type" id="type" x-model="type" @change="setTitleFromType()" class="input-app form-select @error('type') is-invalid @enderror" required>
                @foreach (ReminderType::cases() as $typeOption)
                    <option value="{{ $typeOption->value }}" @selected($selectedType === $typeOption->value)>{{ $typeOption->label() }}</option>
                @endforeach
            </select>
            @error('type')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <div class="col-12 col-md-6">
            <label class="label-app" for="frequency">Repeat</label>
            <select name="frequency" id="frequency" class="input-app form-select @error('frequency') is-invalid @enderror" required>
                @foreach (ReminderFrequency::cases() as $freq)
                    <option value="{{ $freq->value }}" @selected(old('frequency', $reminder?->frequency?->value ?? ReminderFrequency::Monthly->value) === $freq->value)>
                        {{ $freq->label() }}
                    </option>
                @endforeach
            </select>
            @error('frequency')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <div class="col-12">
            <label class="label-app" for="title">Title</label>
            <input type="text" name="title" id="title" x-model="title" value="{{ old('title', $reminder?->title ?? '') }}" class="input-app form-control @error('title') is-invalid @enderror" required>
            @error('title')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <div class="col-12 col-md-6" x-show="['creditor_payment','bill_due','subscription'].includes(type)" x-cloak>
            <label class="label-app" for="payee_name">Creditor / payee name</label>
            <input type="text" name="payee_name" id="payee_name" value="{{ old('payee_name', $reminder?->payee_name ?? '') }}" class="input-app form-control @error('payee_name') is-invalid @enderror" placeholder="e.g. Bank, landlord, utility">
            @error('payee_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <div class="col-12 col-md-6">
            <label class="label-app" for="amount">Amount (optional)</label>
            <input type="number" step="0.01" min="0" name="amount" id="amount" value="{{ old('amount', $reminder?->amount) }}" class="input-app form-control @error('amount') is-invalid @enderror" placeholder="Expected salary or payment">
            @error('amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <div class="col-12 col-md-6">
            <label class="label-app" for="remind_date">Remind on date</label>
            <input type="date" name="remind_date" id="remind_date" value="{{ old('remind_date', $remindAt->format('Y-m-d')) }}" class="input-app form-control @error('remind_date') is-invalid @enderror" required>
            @error('remind_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <div class="col-12 col-md-6">
            <label class="label-app" for="remind_time">Remind at time</label>
            <input type="time" name="remind_time" id="remind_time" value="{{ old('remind_time', $remindAt->format('H:i')) }}" class="input-app form-control @error('remind_time') is-invalid @enderror" required>
            @error('remind_time')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <div class="col-12">
            <label class="label-app" for="notes">Notes (optional)</label>
            <textarea name="notes" id="notes" rows="3" class="input-app form-control @error('notes') is-invalid @enderror" placeholder="Account number, reference, or extra details">{{ old('notes', $reminder?->notes) }}</textarea>
            @error('notes')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <div class="col-12">
            <div class="form-check">
                <input type="hidden" name="notify_email" value="0">
                <input type="checkbox" name="notify_email" id="notify_email" value="1" class="form-check-input" @checked(old('notify_email', $reminder?->notify_email ?? true))>
                <label class="form-check-label" for="notify_email">Send email reminder when due</label>
            </div>
        </div>

        @if ($isEdit)
            <div class="col-12">
                <div class="form-check">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1" class="form-check-input" @checked(old('is_active', $reminder->is_active))>
                    <label class="form-check-label" for="is_active">Reminder is active</label>
                </div>
            </div>
        @endif

        <div class="col-12 d-flex flex-wrap gap-2">
            <button type="submit" class="btn-primary-app">{{ $isEdit ? 'Update' : 'Create' }} reminder</button>
            <a href="{{ route('reminders.index') }}" class="btn-secondary-app">Cancel</a>
        </div>
    </form>
</div>
