@php
    $maxMb = round($maxUploadKb / 1024, 1);
@endphp

@push('styles')
    @vite(['resources/css/ai-scan.css'])
@endpush
@push('scripts')
    @vite(['resources/js/ai-scan-entry.js'])
@endpush

<x-user-layout>
    <x-slot name="header">AI Scan</x-slot>
    <x-slot name="subheader">Upload a bill or receipt image — AI extracts amount, date, and type for income or expense.</x-slot>

    <div
        id="ai-scan-app"
        class="ai-scan-page"
        data-analyze-url="{{ route('ai-scan.analyze') }}"
        data-store-url="{{ route('ai-scan.store') }}"
        data-max-bytes="{{ $maxUploadKb * 1024 }}"
        data-max-mb="{{ $maxMb }}"
        data-ai-enabled="{{ ($aiScanReady ?? false) ? '1' : '0' }}"
        data-accepted-types="{{ implode(',', $acceptedMimes) }}"
    >
        <div class="ai-scan-page__layout">
            {{-- Left: upload & scan --}}
            <div class="ai-scan-page__left">
                <div class="ai-scan-panel">
                    <header class="ai-scan-panel__header">
                        <div class="ai-scan-panel__heading">
                            <span class="ai-scan-panel__step" aria-hidden="true">1</span>
                            <div>
                                <h2 class="ai-scan-panel__title">Upload receipt</h2>
                                <p class="ai-scan-panel__subtitle">Add a photo, then scan with AI to extract details.</p>
                            </div>
                        </div>
                        <span id="ai-scan-scanned-badge" class="ai-scan-badge ai-scan-badge--success hidden">Scanned</span>
                    </header>

                    <div class="ai-scan-panel__body">
                        @unless ($aiScanReady)
                            <x-ui.alert variant="destructive" class="mb-4">
                                @if (! $aiConfigured)
                                    <p class="mb-0 text-sm"><strong>AI not configured.</strong> Add <code>GEMINI_API_KEY</code> or <code>OPENAI_API_KEY</code> to <code>.env</code>.</p>
                                @else
                                    <p class="mb-0 text-sm"><strong>AI Scan is not ready.</strong> Contact your administrator if this persists.</p>
                                @endif
                            </x-ui.alert>
                        @else
                            <p class="ai-scan-provider mb-4">
                                <span class="ai-scan-provider__label">Provider</span>
                                <strong>{{ ucfirst($aiProvider ?? 'auto') }}</strong>
                                <span class="text-muted-foreground">·</span>
                                <code class="text-xs">{{ config('ai.scan_model') }}</code>
                            </p>
                        @endunless

                        <input
                            type="file"
                            id="ai-scan-file"
                            accept="image/jpeg,image/png,image/webp,image/*,.jpg,.jpeg,.png,.webp"
                            class="ai-scan-file-input"
                            tabindex="-1"
                            aria-hidden="true"
                        >
                        <input
                            type="file"
                            id="ai-scan-camera"
                            accept="image/*"
                            capture="environment"
                            class="ai-scan-file-input"
                            tabindex="-1"
                            aria-hidden="true"
                        >

                        <div id="ai-scan-dropzone" class="ai-scan-dropzone mb-4" tabindex="0" role="region" aria-label="Receipt image upload area">
                            <div id="ai-scan-placeholder" class="ai-scan-placeholder">
                                <span class="ai-scan-placeholder__icon" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.163-5.163a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v8.25A1.5 1.5 0 0 0 3.75 15.75Zm13.5-9.75a2.25 2.25 0 0 1 2.25 2.25v.75" />
                                    </svg>
                                </span>
                                <p class="ai-scan-placeholder__title">Drag & drop your receipt</p>
                                <p class="ai-scan-placeholder__hint">or use the buttons below</p>
                            </div>

                            <div id="ai-scan-preview-wrap" class="ai-scan-preview-wrap hidden w-full" aria-hidden="true">
                                <img id="ai-scan-preview" src="" alt="Receipt preview" class="ai-scan-preview">
                            </div>
                        </div>

                        <div id="ai-scan-file-meta" class="ai-scan-file-meta hidden mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ai-scan-file-meta__icon" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75h19.5M2.25 15.75v1.5c0 .621.504 1.125 1.125 1.125h16.5c.621 0 1.125-.504 1.125-1.125v-1.5m-13.5-9.75L12 3l4.5 4.5m-9 0h9" />
                            </svg>
                            <div class="min-w-0 flex-1">
                                <p class="ai-scan-file-meta__name" id="ai-scan-file-name">—</p>
                                <p class="ai-scan-file-meta__size" id="ai-scan-file-size">—</p>
                            </div>
                        </div>

                        <div id="ai-scan-upload-actions" class="ai-scan-actions mb-4" data-ai-scan-no-drop>
                            <div class="ai-scan-actions__row">
                                <label for="ai-scan-file" id="ai-scan-choose-btn" class="ai-scan-file-label ai-scan-file-label--primary" role="button">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="18" height="18" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" /></svg>
                                    Choose image
                                </label>
                                <button type="button" id="ai-scan-camera-btn" class="ai-scan-file-label">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="18" height="18" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2 8.798 2 10c0 .628.224 1.205.595 1.659.371.454.89.777 1.477.977C4.69 12.96 5.5 13.5 6.5 13.5c.5 0 1-.1 1.5-.3m0 0a2.25 2.25 0 1 0 3.75 0m-3.75 0h3.75M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                    Take photo
                                </button>
                            </div>
                            <button type="button" id="ai-scan-paste-btn" class="ai-scan-file-label ai-scan-file-label--muted w-full">
                                Paste from clipboard
                            </button>
                        </div>

                        <div id="ai-scan-preview-actions" class="ai-scan-actions ai-scan-actions--inline hidden mb-4" data-ai-scan-no-drop>
                            <label for="ai-scan-file" id="ai-scan-change-btn" class="ai-scan-file-label ai-scan-file-label--sm" role="button">Change</label>
                            <button type="button" id="ai-scan-retake-btn" class="ai-scan-file-label ai-scan-file-label--sm">Retake</button>
                            <button type="button" id="ai-scan-remove-btn" class="ai-scan-file-label ai-scan-file-label--sm ai-scan-file-label--danger">Remove</button>
                        </div>

                        <div class="ai-scan-auto-row mb-4" data-ai-scan-no-drop>
                            <x-ui.checkbox id="ai-scan-auto-scan" @disabled(!$aiScanReady) />
                            <label for="ai-scan-auto-scan">Scan automatically after upload</label>
                        </div>

                        <x-ui.button type="button" id="ai-scan-btn" class="w-full" :disabled="true" data-ai-scan-no-drop>
                            <span class="ai-scan-btn-label">Scan with AI</span>
                            <span class="ai-scan-btn-loading hidden ms-2 inline-block size-4 animate-spin rounded-full border-2 border-primary-foreground/30 border-t-primary-foreground" role="status" aria-hidden="true"></span>
                        </x-ui.button>

                        <p class="ai-scan-footnote">
                            JPG, PNG, or WebP · max {{ $maxMb }} MB · receipt is saved with the transaction
                        </p>
                    </div>
                </div>
            </div>

            {{-- Right: review & save --}}
            <div class="ai-scan-page__right">
                <div class="ai-scan-panel ai-scan-panel--form">
                    <header class="ai-scan-panel__header">
                        <div class="ai-scan-panel__heading">
                            <span class="ai-scan-panel__step ai-scan-panel__step--secondary" aria-hidden="true">2</span>
                            <div>
                                <h2 class="ai-scan-panel__title">Review & save</h2>
                                <p class="ai-scan-panel__subtitle">Confirm extracted fields, then save. The image is attached to the transaction.</p>
                            </div>
                        </div>
                    </header>

                    <div class="ai-scan-panel__body">
                        <form
                            method="POST"
                            action="{{ route('ai-scan.store') }}"
                            id="ai-scan-form"
                            class="ai-scan-form"
                            enctype="multipart/form-data"
                        >
                            @csrf
                            <input type="file" name="receipt_image" id="scan_receipt_image" class="hidden" accept="image/jpeg,image/png,image/webp">

                            <div class="ai-scan-confidence" id="ai-scan-confidence-wrap" style="display: none;">
                                <span class="ai-scan-badge ai-scan-badge--muted" id="ai-scan-confidence"></span>
                                <span class="text-sm text-muted-foreground" id="ai-scan-currency"></span>
                            </div>

                            <div class="ai-scan-form__grid">
                                <div class="ai-scan-form__field">
                                    <x-ui.label for="scan_type">Type</x-ui.label>
                                    <x-ui.select name="type" id="scan_type" required>
                                        @foreach (\App\Enums\TransactionType::cases() as $type)
                                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                                        @endforeach
                                    </x-ui.select>
                                </div>

                                <div class="ai-scan-form__field">
                                    <x-ui.label for="scan_transaction_date">Date</x-ui.label>
                                    <x-ui.input
                                        type="date"
                                        name="transaction_date"
                                        id="scan_transaction_date"
                                        value="{{ now()->format('Y-m-d') }}"
                                        required
                                    />
                                </div>

                                <div class="ai-scan-form__field ai-scan-form__field--full">
                                    <x-ui.label for="scan_title">Title</x-ui.label>
                                    <x-ui.input
                                        type="text"
                                        name="title"
                                        id="scan_title"
                                        placeholder="e.g. Grocery store"
                                        required
                                    />
                                </div>

                                <div class="ai-scan-form__field">
                                    <x-ui.label for="scan_amount">Amount</x-ui.label>
                                    <x-ui.input
                                        type="number"
                                        step="0.01"
                                        min="0.01"
                                        name="amount"
                                        id="scan_amount"
                                        required
                                    />
                                </div>

                                <div class="ai-scan-form__field">
                                    <x-ui.label for="scan_category_id">Category</x-ui.label>
                                    <x-ui.select name="category_id" id="scan_category_id">
                                        <option value="">No category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }} ({{ $category->type->label() }})</option>
                                        @endforeach
                                    </x-ui.select>
                                    <p class="ai-scan-form__hint" id="scan_category_hint"></p>
                                </div>

                                <div class="ai-scan-form__field ai-scan-form__field--full">
                                    <x-ui.label for="scan_description">Description</x-ui.label>
                                    <x-ui.textarea
                                        name="description"
                                        id="scan_description"
                                        rows="3"
                                        placeholder="Optional notes from the bill"
                                    ></x-ui.textarea>
                                </div>
                            </div>

                            <div class="ai-scan-form__actions">
                                <x-ui.button type="submit" id="ai-scan-save" disabled>Save transaction</x-ui.button>
                                <x-ui.button variant="outline" href="{{ route('transactions.create') }}">Manual entry</x-ui.button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="ai-scan-camera-modal" tabindex="-1" aria-labelledby="ai-scan-camera-modal-title" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ai-scan-camera-modal-title">Take a photo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0 bg-dark">
                        <video id="ai-scan-camera-video" class="ai-scan-camera-video w-full" playsinline autoplay muted></video>
                        <canvas id="ai-scan-camera-canvas" class="hidden" aria-hidden="true"></canvas>
                        <p id="ai-scan-camera-modal-error" class="text-destructive small px-3 py-2 mb-0 hidden"></p>
                    </div>
                    <div class="modal-footer">
                        <x-ui.button variant="outline" type="button" data-bs-dismiss="modal">Cancel</x-ui.button>
                        <x-ui.button type="button" id="ai-scan-camera-capture-btn">Capture photo</x-ui.button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-user-layout>
