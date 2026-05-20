@php
    $maxMb = round($maxUploadKb / 1024, 1);
@endphp

<x-user-layout>
    <x-slot name="header">AI Scan</x-slot>
    <x-slot name="subheader">Upload a bill or receipt image — AI extracts amount, date, and type for income or expense.</x-slot>

    <div
        id="ai-scan-app"
        class="ai-scan-app"
        data-analyze-url="{{ route('ai-scan.analyze') }}"
        data-store-url="{{ route('ai-scan.store') }}"
        data-max-bytes="{{ $maxUploadKb * 1024 }}"
        data-max-mb="{{ $maxMb }}"
        data-ai-enabled="{{ ($aiScanReady ?? false) ? '1' : '0' }}"
        data-accepted-types="{{ implode(',', $acceptedMimes) }}"
    >
        <div class="row g-4">
            <div class="col-12 col-lg-5">
                <div class="card-panel h-100">
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                        <h2 class="h5 fw-semibold mb-0">1. Upload image</h2>
                        <span id="ai-scan-scanned-badge" class="badge text-bg-success d-none">Scanned</span>
                    </div>

                    @unless ($aiScanReady)
                        <div class="alert alert-warning mb-3">
                            @if (! $aiConfigured)
                                <strong>AI not configured on server.</strong> Your administrator must add <code>GEMINI_API_KEY</code> or <code>OPENAI_API_KEY</code> to <code>.env</code>.
                            @else
                                <strong>AI Scan is not ready.</strong> Contact your administrator if this persists.
                            @endif
                        </div>
                    @else
                        <p class="small text-secondary mb-3">
                            AI: <strong>{{ ucfirst($aiProvider ?? 'auto') }}</strong> · model <code>{{ config('ai.scan_model') }}</code>
                        </p>
                    @endunless

                    {{-- File inputs outside dropzone; visually hidden (not display:none) so pickers open reliably --}}
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

                    <div id="ai-scan-dropzone" class="ai-scan-dropzone mb-3" tabindex="0" role="region" aria-label="Receipt image upload area">
                        <div id="ai-scan-placeholder" class="ai-scan-placeholder text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="48" height="48" class="text-secondary mb-2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.163-5.163a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v8.25A1.5 1.5 0 0 0 3.75 15.75Zm13.5-9.75a2.25 2.25 0 0 1 2.25 2.25v.75" />
                            </svg>
                            <p class="mb-1 fw-medium">Drag & drop your receipt here</p>
                            <p class="small text-secondary mb-0">or use the buttons below</p>
                        </div>

                        <div id="ai-scan-preview-wrap" class="ai-scan-preview-wrap d-none w-100" aria-hidden="true">
                            <img id="ai-scan-preview" src="" alt="" class="ai-scan-preview">
                        </div>
                    </div>

                    <div id="ai-scan-file-meta" class="ai-scan-file-meta d-none mb-3">
                        <div class="d-flex align-items-start gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="20" height="20" class="text-primary flex-shrink-0 mt-1" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75h19.5M2.25 15.75v1.5c0 .621.504 1.125 1.125 1.125h16.5c.621 0 1.125-.504 1.125-1.125v-1.5m-13.5-9.75L12 3l4.5 4.5m-9 0h9" />
                            </svg>
                            <div class="min-w-0 flex-grow-1">
                                <p class="mb-0 fw-medium text-truncate" id="ai-scan-file-name">—</p>
                                <p class="small text-secondary mb-0" id="ai-scan-file-size">—</p>
                            </div>
                        </div>
                    </div>

                    <div id="ai-scan-upload-actions" class="d-grid gap-2 mb-3" data-ai-scan-no-drop>
                        <div class="row g-2">
                            <div class="col-6">
                                <label for="ai-scan-file" id="ai-scan-choose-btn" class="btn btn-outline-primary w-100 mb-0 ai-scan-file-label" role="button">
                                    <span class="d-inline-flex align-items-center justify-content-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="18" height="18" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" /></svg>
                                        Choose image
                                    </span>
                                </label>
                            </div>
                            <div class="col-6">
                                <button type="button" id="ai-scan-camera-btn" class="btn btn-outline-primary w-100">
                                    <span class="d-inline-flex align-items-center justify-content-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="18" height="18" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2 8.798 2 10c0 .628.224 1.205.595 1.659.371.454.89.777 1.477.977C4.69 12.96 5.5 13.5 6.5 13.5c.5 0 1-.1 1.5-.3m0 0a2.25 2.25 0 1 0 3.75 0m-3.75 0h3.75M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                        Take photo
                                    </span>
                                </button>
                            </div>
                        </div>
                        <button type="button" id="ai-scan-paste-btn" class="btn btn-outline-secondary w-100">
                            Paste from clipboard
                        </button>
                    </div>

                    <div id="ai-scan-preview-actions" class="d-none d-flex flex-wrap gap-2 mb-3" data-ai-scan-no-drop>
                        <label for="ai-scan-file" id="ai-scan-change-btn" class="btn btn-sm btn-outline-primary mb-0 ai-scan-file-label" role="button">Change image</label>
                        <button type="button" id="ai-scan-retake-btn" class="btn btn-sm btn-outline-primary">Retake photo</button>
                        <button type="button" id="ai-scan-remove-btn" class="btn btn-sm btn-outline-danger">Remove</button>
                    </div>

                    <div class="form-check mb-3" data-ai-scan-no-drop>
                        <input class="form-check-input" type="checkbox" id="ai-scan-auto-scan" @disabled(!$aiScanReady)>
                        <label class="form-check-label small" for="ai-scan-auto-scan">
                            Scan automatically after upload
                        </label>
                    </div>

                    <button type="button" id="ai-scan-btn" class="btn-primary-app w-100" disabled data-ai-scan-no-drop>
                        <span class="ai-scan-btn-label">Scan with AI</span>
                        <span class="ai-scan-btn-loading d-none spinner-border spinner-border-sm ms-2" role="status" aria-hidden="true"></span>
                    </button>

                    <div id="ai-scan-error" class="alert alert-danger mt-3 mb-0 d-none" role="alert"></div>
                    <div id="ai-scan-success" class="alert alert-success mt-3 mb-0 d-none" role="status"></div>

                    <p class="small text-secondary mt-3 mb-0">
                        JPG, PNG, or WebP · max {{ $maxMb }} MB · receipt is saved with the transaction
                    </p>
                </div>
            </div>

            <div class="col-12 col-lg-7">
                <div class="card-panel">
                    <h2 class="h5 fw-semibold mb-3">2. Review & save</h2>
                    <p class="text-secondary small mb-4">After scanning, check the fields below and save. The uploaded image is attached to the transaction.</p>

                    <form
                        method="POST"
                        action="{{ route('ai-scan.store') }}"
                        id="ai-scan-form"
                        class="row g-3"
                        enctype="multipart/form-data"
                    >
                        @csrf
                        <input type="file" name="receipt_image" id="scan_receipt_image" class="d-none" accept="image/jpeg,image/png,image/webp">

                        <div class="col-12" id="ai-scan-confidence-wrap" style="display: none;">
                            <span class="badge text-bg-secondary" id="ai-scan-confidence"></span>
                            <span class="small text-secondary ms-2" id="ai-scan-currency"></span>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="label-app" for="scan_type">Type</label>
                            <select name="type" id="scan_type" class="input-app form-select" required>
                                @foreach (\App\Enums\TransactionType::cases() as $type)
                                    <option value="{{ $type->value }}">{{ $type->label() }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="label-app" for="scan_transaction_date">Date</label>
                            <input type="date" name="transaction_date" id="scan_transaction_date" value="{{ now()->format('Y-m-d') }}" class="input-app form-control" required>
                        </div>

                        <div class="col-12">
                            <label class="label-app" for="scan_title">Title</label>
                            <input type="text" name="title" id="scan_title" class="input-app form-control" placeholder="e.g. Grocery store" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="label-app" for="scan_amount">Amount</label>
                            <input type="number" step="0.01" min="0.01" name="amount" id="scan_amount" class="input-app form-control" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="label-app" for="scan_category_id">Category</label>
                            <select name="category_id" id="scan_category_id" class="input-app form-select">
                                <option value="">No category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }} ({{ $category->type->label() }})</option>
                                @endforeach
                            </select>
                            <p class="small text-secondary mt-1 mb-0" id="scan_category_hint"></p>
                        </div>

                        <div class="col-12">
                            <label class="label-app" for="scan_description">Description</label>
                            <textarea name="description" id="scan_description" rows="3" class="input-app form-control" placeholder="Optional notes from the bill"></textarea>
                        </div>

                        <div class="col-12 d-flex flex-wrap gap-2">
                            <button type="submit" class="btn-primary-app" id="ai-scan-save" disabled>Save transaction</button>
                            <a href="{{ route('transactions.create') }}" class="btn-secondary-app">Manual entry</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Desktop / webcam capture (Take photo on PC) --}}
        <div class="modal fade" id="ai-scan-camera-modal" tabindex="-1" aria-labelledby="ai-scan-camera-modal-title" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ai-scan-camera-modal-title">Take a photo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0 bg-dark">
                        <video id="ai-scan-camera-video" class="ai-scan-camera-video w-100" playsinline autoplay muted></video>
                        <canvas id="ai-scan-camera-canvas" class="d-none" aria-hidden="true"></canvas>
                        <p id="ai-scan-camera-modal-error" class="text-danger small px-3 py-2 mb-0 d-none"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="ai-scan-camera-capture-btn">Capture photo</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-user-layout>
