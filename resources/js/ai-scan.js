/**
 * AI Scan — receipt image upload, preview, analyze, save with receipt attachment.
 */
export function initAiScan() {
    const root = document.getElementById('ai-scan-app');

    if (!root) {
        return;
    }

    const config = {
        analyzeUrl: root.dataset.analyzeUrl,
        storeUrl: root.dataset.storeUrl,
        csrf: document.querySelector('meta[name="csrf-token"]')?.content ?? '',
        maxBytes: parseInt(root.dataset.maxBytes ?? '5242880', 10),
        maxMb: parseFloat(root.dataset.maxMb ?? '5'),
        aiEnabled: root.dataset.aiEnabled === '1',
        acceptedTypes: (root.dataset.acceptedTypes ?? 'image/jpeg,image/png,image/webp').split(',').map((t) => t.trim()),
    };

    const els = {
        dropzone: document.getElementById('ai-scan-dropzone'),
        fileInput: document.getElementById('ai-scan-file'),
        cameraInput: document.getElementById('ai-scan-camera'),
        receiptInput: document.getElementById('scan_receipt_image'),
        preview: document.getElementById('ai-scan-preview'),
        previewWrap: document.getElementById('ai-scan-preview-wrap'),
        placeholder: document.getElementById('ai-scan-placeholder'),
        fileMeta: document.getElementById('ai-scan-file-meta'),
        fileName: document.getElementById('ai-scan-file-name'),
        fileSize: document.getElementById('ai-scan-file-size'),
        uploadActions: document.getElementById('ai-scan-upload-actions'),
        previewActions: document.getElementById('ai-scan-preview-actions'),
        scanBtn: document.getElementById('ai-scan-btn'),
        chooseLabel: document.getElementById('ai-scan-choose-btn'),
        cameraBtn: document.getElementById('ai-scan-camera-btn'),
        retakeBtn: document.getElementById('ai-scan-retake-btn'),
        removeBtn: document.getElementById('ai-scan-remove-btn'),
        pasteBtn: document.getElementById('ai-scan-paste-btn'),
        autoScan: document.getElementById('ai-scan-auto-scan'),
        errorBox: document.getElementById('ai-scan-error'),
        successBox: document.getElementById('ai-scan-success'),
        saveForm: document.getElementById('ai-scan-form'),
        saveBtn: document.getElementById('ai-scan-save'),
        btnLabel: document.querySelector('.ai-scan-btn-label'),
        btnLoading: document.querySelector('.ai-scan-btn-loading'),
        scannedBadge: document.getElementById('ai-scan-scanned-badge'),
        cameraModalEl: document.getElementById('ai-scan-camera-modal'),
        cameraVideo: document.getElementById('ai-scan-camera-video'),
        cameraCanvas: document.getElementById('ai-scan-camera-canvas'),
        cameraCaptureBtn: document.getElementById('ai-scan-camera-capture-btn'),
        cameraModalError: document.getElementById('ai-scan-camera-modal-error'),
    };

    let selectedFile = null;
    let previewObjectUrl = null;
    let hasScanned = false;
    let cameraStream = null;
    let cameraModal = null;

    if (els.cameraModalEl && window.bootstrap?.Modal) {
        cameraModal = new window.bootstrap.Modal(els.cameraModalEl);
        els.cameraModalEl.addEventListener('hidden.bs.modal', stopCameraStream);
    }

    function formatSize(bytes) {
        if (bytes < 1024) {
            return `${bytes} B`;
        }
        if (bytes < 1024 * 1024) {
            return `${(bytes / 1024).toFixed(1)} KB`;
        }

        return `${(bytes / (1024 * 1024)).toFixed(2)} MB`;
    }

    function showError(message) {
        if (!els.errorBox) {
            return;
        }
        els.errorBox.textContent = message;
        els.errorBox.classList.remove('d-none');
        els.successBox?.classList.add('d-none');
    }

    function showSuccess(message) {
        if (!els.successBox) {
            return;
        }
        els.successBox.textContent = message;
        els.successBox.classList.remove('d-none');
        els.errorBox?.classList.add('d-none');
    }

    function clearMessages() {
        els.errorBox?.classList.add('d-none');
        els.successBox?.classList.add('d-none');
    }

    function isMobileDevice() {
        return /Android|iPhone|iPad|iPod|Mobile|webOS|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)
            || (navigator.maxTouchPoints > 1 && window.innerWidth < 1024);
    }

    function canUseWebCamera() {
        return Boolean(navigator.mediaDevices?.getUserMedia) && window.isSecureContext;
    }

    function resetFileInput(input) {
        if (input) {
            input.value = '';
        }
    }

    function triggerFileInput(input) {
        if (!input) {
            return;
        }
        resetFileInput(input);
        input.click();
    }

    function isImageFile(file) {
        if (!file) {
            return false;
        }

        const type = (file.type || '').toLowerCase();
        if (type.startsWith('image/')) {
            return true;
        }

        return /\.(jpe?g|png|webp|gif|bmp|heic|heif)$/i.test(file.name || '');
    }

    function setLoading(loading) {
        if (!els.scanBtn) {
            return;
        }
        els.scanBtn.disabled = loading || !selectedFile || !config.aiEnabled;
        els.btnLabel?.classList.toggle('d-none', loading);
        els.btnLoading?.classList.toggle('d-none', !loading);
        els.scanBtn?.setAttribute('aria-busy', loading ? 'true' : 'false');
    }

    function revokePreviewUrl() {
        if (previewObjectUrl) {
            URL.revokeObjectURL(previewObjectUrl);
            previewObjectUrl = null;
        }
    }

    function syncReceiptInput(file) {
        if (!els.receiptInput || !file) {
            return;
        }
        const dt = new DataTransfer();
        dt.items.add(file);
        els.receiptInput.files = dt.files;
    }

    function clearReceiptInput() {
        if (els.receiptInput) {
            els.receiptInput.value = '';
        }
    }

    function canSaveWithoutScan() {
        return !config.aiEnabled && Boolean(selectedFile);
    }

    function updateSaveButtonState() {
        if (!els.saveBtn) {
            return;
        }
        const canSave = selectedFile && (hasScanned || canSaveWithoutScan());
        els.saveBtn.disabled = !canSave;
    }

    function updateUiHasFile(hasFile) {
        els.placeholder?.classList.toggle('d-none', hasFile);
        els.previewWrap?.classList.toggle('d-none', !hasFile);
        if (els.previewWrap) {
            els.previewWrap.setAttribute('aria-hidden', hasFile ? 'false' : 'true');
        }
        els.fileMeta?.classList.toggle('d-none', !hasFile);
        els.uploadActions?.classList.toggle('d-none', hasFile);
        els.previewActions?.classList.toggle('d-none', !hasFile);
        els.dropzone?.classList.toggle('ai-scan-dropzone--has-file', hasFile);

        if (els.scanBtn) {
            els.scanBtn.disabled = !hasFile || !config.aiEnabled;
        }

        if (!hasFile) {
            hasScanned = false;
            els.scannedBadge?.classList.add('d-none');
        }

        updateSaveButtonState();
    }

    function validateFile(file) {
        if (!file) {
            return 'No file selected.';
        }

        if (!isImageFile(file)) {
            return 'Please choose a photo or image file (JPG, PNG, or WebP).';
        }

        if (file.size > config.maxBytes) {
            return `Image is too large. Maximum size is ${config.maxMb} MB.`;
        }

        if (file.size < 100) {
            return 'Image file is too small or empty.';
        }

        return null;
    }

    function normalizeCameraFile(file) {
        if (!file) {
            return file;
        }

        const type = (file.type || '').toLowerCase();
        if (type && type !== 'application/octet-stream') {
            return file;
        }

        const ext = (file.name || '').split('.').pop()?.toLowerCase() || 'jpg';
        const mime = ext === 'png' ? 'image/png' : ext === 'webp' ? 'image/webp' : 'image/jpeg';
        const name = file.name && file.name !== 'image.jpg' ? file.name : `receipt-${Date.now()}.${ext === 'png' ? 'png' : ext === 'webp' ? 'webp' : 'jpg'}`;

        return new File([file], name, { type: mime, lastModified: file.lastModified || Date.now() });
    }

    function handleFile(file, { autoScan = false, source = 'upload' } = {}) {
        const normalized = source === 'camera' ? normalizeCameraFile(file) : file;
        const error = validateFile(normalized);

        if (error) {
            showError(error);
            return;
        }

        selectedFile = normalized;
        hasScanned = false;
        clearMessages();

        revokePreviewUrl();
        previewObjectUrl = URL.createObjectURL(normalized);

        if (els.preview) {
            els.preview.src = previewObjectUrl;
            els.preview.alt = `Preview: ${normalized.name}`;
        }

        if (els.fileName) {
            els.fileName.textContent = normalized.name;
        }
        if (els.fileSize) {
            els.fileSize.textContent = formatSize(normalized.size);
        }

        syncReceiptInput(normalized);
        updateUiHasFile(true);

        if (autoScan && config.aiEnabled && els.autoScan?.checked) {
            runScan();
        } else if (config.aiEnabled) {
            showSuccess('Image ready. Click “Scan with AI” to extract details.');
        } else {
            showSuccess('Image attached. Fill in the form and save, or add GEMINI_API_KEY / OPENAI_API_KEY to enable AI scan.');
            updateSaveButtonState();
        }
    }

    function clearFile() {
        selectedFile = null;
        hasScanned = false;
        revokePreviewUrl();

        if (els.preview) {
            els.preview.removeAttribute('src');
        }

        resetFileInput(els.fileInput);
        resetFileInput(els.cameraInput);
        clearReceiptInput();
        updateUiHasFile(false);
        clearMessages();
    }

    function openGalleryPicker() {
        triggerFileInput(els.fileInput);
    }

    function openNativeCameraPicker() {
        triggerFileInput(els.cameraInput);
    }

    function showCameraModalError(message) {
        if (!els.cameraModalError) {
            return;
        }
        if (message) {
            els.cameraModalError.textContent = message;
            els.cameraModalError.classList.remove('d-none');
        } else {
            els.cameraModalError.textContent = '';
            els.cameraModalError.classList.add('d-none');
        }
    }

    function stopCameraStream() {
        if (cameraStream) {
            cameraStream.getTracks().forEach((track) => track.stop());
            cameraStream = null;
        }
        if (els.cameraVideo) {
            els.cameraVideo.srcObject = null;
        }
        showCameraModalError('');
    }

    async function startWebCamera() {
        if (!canUseWebCamera()) {
            throw new Error('Camera needs HTTPS or localhost. Use Choose image, or open this site over a secure connection.');
        }

        stopCameraStream();

        const constraints = {
            video: {
                facingMode: { ideal: 'environment' },
                width: { ideal: 1920 },
                height: { ideal: 1080 },
            },
            audio: false,
        };

        try {
            cameraStream = await navigator.mediaDevices.getUserMedia(constraints);
        } catch {
            cameraStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
        }

        if (els.cameraVideo) {
            els.cameraVideo.srcObject = cameraStream;
            await els.cameraVideo.play();
        }
    }

    async function openWebCameraModal() {
        if (!cameraModal) {
            openNativeCameraPicker();
            return;
        }

        showCameraModalError('');
        cameraModal.show();

        try {
            await startWebCamera();
        } catch (err) {
            showCameraModalError(err.message ?? 'Could not access the camera.');
        }
    }

    function captureFromWebCamera() {
        const video = els.cameraVideo;
        const canvas = els.cameraCanvas;

        if (!video || !canvas || !video.videoWidth) {
            showCameraModalError('Camera is not ready. Allow camera access and try again.');
            return;
        }

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0);

        canvas.toBlob(
            (blob) => {
                if (!blob) {
                    showCameraModalError('Could not capture photo. Try again.');
                    return;
                }

                const file = new File([blob], `receipt-${Date.now()}.jpg`, {
                    type: 'image/jpeg',
                    lastModified: Date.now(),
                });

                cameraModal?.hide();
                handleFile(file, {
                    autoScan: els.autoScan?.checked ?? false,
                    source: 'camera',
                });
            },
            'image/jpeg',
            0.92,
        );
    }

    function openTakePhoto() {
        clearMessages();

        if (isMobileDevice()) {
            openNativeCameraPicker();
            return;
        }

        if (canUseWebCamera()) {
            openWebCameraModal();
            return;
        }

        openNativeCameraPicker();
    }

    async function runScan() {
        if (!selectedFile || !config.aiEnabled) {
            return;
        }

        clearMessages();
        setLoading(true);

        const formData = new FormData();
        formData.append('image', selectedFile, selectedFile.name);

        try {
            const response = await fetch(config.analyzeUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': config.csrf,
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData,
            });

            let payload = {};
            try {
                payload = await response.json();
            } catch {
                throw new Error(response.status === 419
                    ? 'Session expired. Refresh the page and try again.'
                    : 'Scan failed. Please try again.');
            }

            if (!response.ok) {
                const validationErrors = payload.errors
                    ? Object.values(payload.errors).flat().join(' ')
                    : '';
                throw new Error(validationErrors || payload.message || 'Scan failed.');
            }

            const data = payload.data;

            const setVal = (id, value) => {
                const el = document.getElementById(id);
                if (el && value !== undefined && value !== null) {
                    el.value = value;
                }
            };

            setVal('scan_type', data.type);
            setVal('scan_title', data.title);
            setVal('scan_amount', data.amount);
            setVal('scan_transaction_date', data.transaction_date);
            setVal('scan_description', data.description ?? '');

            if (data.category_id) {
                setVal('scan_category_id', data.category_id);
            }

            const hintEl = document.getElementById('scan_category_hint');
            if (hintEl) {
                hintEl.textContent = data.category_hint ? `AI suggested: ${data.category_hint}` : '';
            }

            const confWrap = document.getElementById('ai-scan-confidence-wrap');
            const confBadge = document.getElementById('ai-scan-confidence');
            const currencyEl = document.getElementById('ai-scan-currency');

            if (confWrap && confBadge) {
                confWrap.style.display = '';
                confBadge.textContent = `Confidence: ${data.confidence}`;
                confBadge.className = 'badge ' + (
                    data.confidence === 'high' ? 'text-bg-success' :
                    data.confidence === 'low' ? 'text-bg-warning' : 'text-bg-info'
                );
            }
            if (currencyEl) {
                currencyEl.textContent = data.currency ? `Currency on bill: ${data.currency}` : '';
            }

            hasScanned = true;
            els.scannedBadge?.classList.remove('d-none');
            updateSaveButtonState();

            showSuccess('Scan complete. Review the fields and save your transaction.');
            document.getElementById('scan_title')?.focus();
        } catch (err) {
            showError(err.message ?? 'Could not scan image.');
        } finally {
            setLoading(false);
        }
    }

    els.dropzone?.addEventListener('click', (e) => {
        if (e.target.closest('[data-ai-scan-no-drop]')) {
            return;
        }
        if (!selectedFile) {
            openGalleryPicker();
        }
    });

    els.dropzone?.addEventListener('keydown', (e) => {
        if ((e.key === 'Enter' || e.key === ' ') && !e.target.closest('[data-ai-scan-no-drop]')) {
            e.preventDefault();
            if (!selectedFile) {
                openGalleryPicker();
            }
        }
    });

    els.chooseLabel?.addEventListener('click', (e) => {
        e.stopPropagation();
    });

    els.cameraBtn?.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        openTakePhoto();
    });

    els.retakeBtn?.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        openTakePhoto();
    });

    els.removeBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        clearFile();
    });

    els.pasteBtn?.addEventListener('click', async (e) => {
        e.stopPropagation();
        if (!navigator.clipboard?.read) {
            showError('Paste is not supported in this browser. Use Choose image instead.');
            return;
        }
        try {
            const items = await navigator.clipboard.read();
            for (const item of items) {
                for (const type of item.types) {
                    if (type.startsWith('image/')) {
                        const blob = await item.getType(type);
                        const ext = type.split('/')[1]?.replace('jpeg', 'jpg') ?? 'png';
                        const file = new File([blob], `pasted-receipt.${ext}`, { type });
                        handleFile(file, { autoScan: els.autoScan?.checked ?? false });
                        return;
                    }
                }
            }
            showError('No image found in clipboard. Copy a receipt screenshot first.');
        } catch {
            showError('Could not paste from clipboard. Allow clipboard access or use Choose image.');
        }
    });

    els.fileInput?.addEventListener('change', () => {
        const file = els.fileInput.files?.[0];
        if (file) {
            handleFile(file, { autoScan: els.autoScan?.checked ?? false });
        }
    });

    els.cameraInput?.addEventListener('change', () => {
        const file = els.cameraInput.files?.[0];
        if (file) {
            handleFile(file, { autoScan: els.autoScan?.checked ?? false, source: 'camera' });
        }
    });

    els.cameraCaptureBtn?.addEventListener('click', captureFromWebCamera);

    ['dragenter', 'dragover'].forEach((eventName) => {
        els.dropzone?.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            els.dropzone.classList.add('ai-scan-dropzone--active');
        });
    });

    ['dragleave', 'drop'].forEach((eventName) => {
        els.dropzone?.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            els.dropzone.classList.remove('ai-scan-dropzone--active');
        });
    });

    els.dropzone?.addEventListener('drop', (e) => {
        const file = e.dataTransfer?.files?.[0];
        if (file) {
            handleFile(file, { autoScan: els.autoScan?.checked ?? false });
        }
    });

    document.addEventListener('paste', (e) => {
        if (!e.clipboardData?.files?.length || !document.getElementById('ai-scan-app')) {
            return;
        }
        const file = [...e.clipboardData.files].find((f) => f.type.startsWith('image/'));
        if (file) {
            handleFile(file, { autoScan: els.autoScan?.checked ?? false });
        }
    });

    els.scanBtn?.addEventListener('click', runScan);

    els.saveForm?.addEventListener('submit', (e) => {
        if (!selectedFile) {
            e.preventDefault();
            showError('Upload a receipt image first.');
            return;
        }
        if (config.aiEnabled && !hasScanned) {
            e.preventDefault();
            showError('Scan the image first before saving.');
            return;
        }
        syncReceiptInput(selectedFile);
    });

    updateUiHasFile(false);
}
