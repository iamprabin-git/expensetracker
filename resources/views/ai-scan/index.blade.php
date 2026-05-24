@php
    $maxMb = round($maxUploadKb / 1024, 1);
@endphp

@push('scripts')
    @vite(['resources/js/ai-scan-entry.js'])
@endpush

<x-user-layout>
    <x-slot name="header">AI Scan</x-slot>
    <x-slot name="subheader">Upload a receipt or bill — AI extracts amount, date, and type so you can save a transaction in seconds.</x-slot>
    <x-slot name="headerActions">
        <x-ui.button variant="outline" size="sm" href="{{ route('transactions.create') }}">Manual entry</x-ui.button>
    </x-slot>

    <div
        id="ai-scan-app"
        class="ai-scan"
        data-analyze-url="{{ route('ai-scan.analyze') }}"
        data-store-url="{{ route('ai-scan.store') }}"
        data-max-bytes="{{ $maxUploadKb * 1024 }}"
        data-max-mb="{{ $maxMb }}"
        data-ai-enabled="{{ ($aiScanReady ?? false) ? '1' : '0' }}"
        data-accepted-types="{{ implode(',', $acceptedMimes) }}"
    >
        <div class="grid grid-cols-12 gap-3 md:gap-4">
            <div class="col-span-12 md:col-span-6">
                <div class="card-panel h-full">
                    <div class="flex flex-wrap items-start justify-between gap-3 border-b border-border pb-4 mb-4">
                        <div>
                            <h2 class="text-lg font-semibold mb-1">Upload receipt</h2>
                            <p class="text-sm text-muted-foreground mb-0">Add a photo, then scan with AI to extract details.</p>
                        </div>
                        <span id="ai-scan-scanned-badge" class="ai-scan-badge ai-scan-badge--success hidden">Scanned</span>
                    </div>

                    @unless ($aiScanReady)
                        <x-ui.alert variant="destructive" class="mb-4">
                            @if (! $aiConfigured)
                                <p class="mb-0 text-sm"><strong>AI not configured.</strong> Add <code>GEMINI_API_KEY</code> or <code>OPENAI_API_KEY</code> to <code>.env</code>.</p>
                            @else
                                <p class="mb-0 text-sm"><strong>AI Scan is not ready.</strong> Contact your administrator if this persists.</p>
                            @endif
                        </x-ui.alert>
                    @else
                        <div class="rounded-lg border border-border bg-muted/30 px-3 py-2 text-sm mb-4">
                            <span class="text-muted-foreground">Provider</span>
                            <span class="font-medium text-foreground">{{ ucfirst($aiProvider ?? 'auto') }}</span>
                            <span class="text-muted-foreground mx-1">·</span>
                            <code class="text-xs text-muted-foreground">{{ config('ai.scan_model') }}</code>
                        </div>
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
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-10 text-muted-foreground">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.163-5.163a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v8.25A1.5 1.5 0 0 0 3.75 15.75Zm13.5-9.75a2.25 2.25 0 0 1 2.25 2.25v.75" />
                                </svg>
                            </span>
                            <p class="text-sm font-medium text-foreground mb-1">Drag &amp; drop your receipt</p>
                            <p class="text-sm text-muted-foreground mb-0">or use the buttons below</p>
                        </div>

                        <div id="ai-scan-preview-wrap" class="ai-scan-preview-wrap hidden w-full" aria-hidden="true">
                            <img id="ai-scan-preview" src="" alt="Receipt preview" class="ai-scan-preview">
                        </div>
                    </div>

                    <div id="ai-scan-file-meta" class="ai-scan-file-meta hidden mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 shrink-0 text-primary" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9z" />
                        </svg>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-foreground mb-0 truncate" id="ai-scan-file-name">—</p>
                            <p class="text-xs text-muted-foreground mb-0" id="ai-scan-file-size">—</p>
                        </div>
                    </div>

                    <div id="ai-scan-upload-actions" class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-3" data-ai-scan-no-drop>
                        <x-ui.button tag="label" for="ai-scan-file" id="ai-scan-choose-btn" class="w-full cursor-pointer">
                            Choose image
                        </x-ui.button>
                        <x-ui.button type="button" id="ai-scan-camera-btn" variant="outline" class="w-full">
                            Take photo
                        </x-ui.button>
                    </div>
                    <x-ui.button type="button" id="ai-scan-paste-btn" variant="outline" class="w-full mb-4" data-ai-scan-no-drop>
                        Paste from clipboard
                    </x-ui.button>

                    <div id="ai-scan-preview-actions" class="flex flex-wrap gap-2 hidden mb-4" data-ai-scan-no-drop>
                        <x-ui.button tag="label" for="ai-scan-file" id="ai-scan-change-btn" size="sm" variant="outline" class="cursor-pointer">Change</x-ui.button>
                        <x-ui.button type="button" id="ai-scan-retake-btn" size="sm" variant="outline">Retake</x-ui.button>
                        <x-ui.button type="button" id="ai-scan-remove-btn" size="sm" variant="destructive">Remove</x-ui.button>
                    </div>

                    <div class="flex items-center gap-2 mb-4" data-ai-scan-no-drop>
                        <x-ui.checkbox id="ai-scan-auto-scan" @disabled(!$aiScanReady) />
                        <label for="ai-scan-auto-scan" class="text-sm font-medium cursor-pointer mb-0">Scan automatically after upload</label>
                    </div>

                    <x-ui.button type="button" id="ai-scan-btn" class="w-full" :disabled="true" data-ai-scan-no-drop>
                        <span class="ai-scan-btn-label">Scan with AI</span>
                        <span class="ai-scan-btn-loading hidden ms-2 inline-block size-4 animate-spin rounded-full border-2 border-primary-foreground/30 border-t-primary-foreground" role="status" aria-hidden="true"></span>
                    </x-ui.button>

                    <p class="text-xs text-muted-foreground text-center mt-3 mb-0">
                        JPG, PNG, or WebP · max {{ $maxMb }} MB · receipt is saved with the transaction
                    </p>
                </div>
            </div>

            <div class="col-span-12 md:col-span-6">
                <div class="card-panel h-full">
                    <div class="border-b border-border pb-4 mb-4">
                        <h2 class="text-lg font-semibold mb-1">Review &amp; save</h2>
                        <p class="text-sm text-muted-foreground mb-0">Confirm extracted fields, then save. The image is attached to the transaction.</p>
                    </div>

                    <form
                        method="POST"
                        action="{{ route('ai-scan.store') }}"
                        id="ai-scan-form"
                        enctype="multipart/form-data"
                    >
                        @csrf
                        <input type="file" name="receipt_image" id="scan_receipt_image" class="hidden" accept="image/jpeg,image/png,image/webp">

                        <div class="rounded-lg border border-border bg-muted/30 px-3 py-2 mb-4 flex flex-wrap items-center gap-2" id="ai-scan-confidence-wrap" style="display: none;">
                            <span class="ai-scan-badge ai-scan-badge--muted" id="ai-scan-confidence"></span>
                            <span class="text-sm text-muted-foreground" id="ai-scan-currency"></span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <x-ui.label for="scan_type">Type</x-ui.label>
                                <x-ui.select name="type" id="scan_type" required>
                                    @foreach (\App\Enums\TransactionType::cases() as $type)
                                        <option value="{{ $type->value }}">{{ $type->label() }}</option>
                                    @endforeach
                                </x-ui.select>
                            </div>

                            <div class="space-y-2">
                                <x-ui.label for="scan_transaction_date">Date</x-ui.label>
                                <x-ui.input
                                    type="date"
                                    name="transaction_date"
                                    id="scan_transaction_date"
                                    value="{{ now()->format('Y-m-d') }}"
                                    required
                                />
                            </div>

                            <div class="space-y-2 sm:col-span-2">
                                <x-ui.label for="scan_title">Title</x-ui.label>
                                <x-ui.input
                                    type="text"
                                    name="title"
                                    id="scan_title"
                                    placeholder="e.g. Grocery store"
                                    required
                                />
                            </div>

                            <div class="space-y-2">
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

                            <div class="space-y-2">
                                <x-ui.label for="scan_category_id">Category</x-ui.label>
                                <x-ui.select name="category_id" id="scan_category_id">
                                    <option value="">No category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }} ({{ $category->type->label() }})</option>
                                    @endforeach
                                </x-ui.select>
                                <p class="text-xs text-muted-foreground mb-0" id="scan_category_hint"></p>
                            </div>

                            <div class="space-y-2 sm:col-span-2">
                                <x-ui.label for="scan_description">Description</x-ui.label>
                                <x-ui.textarea
                                    name="description"
                                    id="scan_description"
                                    rows="3"
                                    placeholder="Optional notes from the bill"
                                ></x-ui.textarea>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2 mt-6 pt-4 border-t border-border">
                            <x-ui.button type="submit" id="ai-scan-save" disabled>Save transaction</x-ui.button>
                            <x-ui.button variant="outline" href="{{ route('transactions.index') }}">View transactions</x-ui.button>
                        </div>
                    </form>
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
