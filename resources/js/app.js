import './bootstrap';

import * as bootstrap from 'bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap-icons/font/bootstrap-icons.css';

window.bootstrap = bootstrap;

// Keep in sync with the 'max:5120' (KB) rule used by every file upload
// endpoint (Berkas, Pembayaran, Hasil, Ocr controllers) so oversized files
// are caught instantly in the browser instead of failing at nginx's
// client_max_body_size, which for large multipart bodies closes the
// connection instead of returning a readable error page.
const MAX_FILE_SIZE_BYTES = 5 * 1024 * 1024;

// iOS Safari re-encodes HEIC photos picked via <input type="file"> into
// uncompressed PNG before handing them to the page, which can turn a ~1MB
// camera photo into 6-8MB. Downscaling + re-encoding as JPEG client-side
// brings it back under the limit without the user having to do anything.
const MAX_IMAGE_DIMENSION = 2000;

function formatFileSize(bytes) {
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
}

function fileInputFeedback(input) {
    let feedback = input.nextElementSibling;

    if (!feedback || !feedback.classList.contains('js-file-size-feedback')) {
        feedback = document.createElement('div');
        feedback.classList.add('js-file-size-feedback');
        input.insertAdjacentElement('afterend', feedback);
    }

    return feedback;
}

function setFeedback(feedback, { text, tone }) {
    feedback.className = `js-file-size-feedback ${tone === 'error' ? 'invalid-feedback' : 'form-text'} ${tone === 'success' ? 'text-success' : tone === 'muted' ? 'text-muted' : ''}`;
    feedback.textContent = text;
    feedback.style.display = text ? 'block' : '';
}

function replaceInputFile(input, file) {
    const transfer = new DataTransfer();
    transfer.items.add(file);
    input.files = transfer.files;
}

// maxDimension shrinks on retry: a failed toBlob() on a mobile browser
// (blob === null) is most often memory pressure from a large canvas, so
// the fallback is to try again smaller rather than give up immediately.
function compressImage(file, maxDimension = MAX_IMAGE_DIMENSION) {
    return new Promise((resolve) => {
        const objectUrl = URL.createObjectURL(file);
        const img = new Image();

        img.onload = () => {
            const scale = Math.min(1, maxDimension / Math.max(img.width, img.height));
            const canvas = document.createElement('canvas');
            canvas.width = Math.round(img.width * scale);
            canvas.height = Math.round(img.height * scale);
            canvas.getContext('2d').drawImage(img, 0, 0, canvas.width, canvas.height);
            URL.revokeObjectURL(objectUrl);

            const attempt = (quality) => {
                canvas.toBlob((blob) => {
                    if (!blob) {
                        if (maxDimension > 800) {
                            resolve(compressImage(file, Math.round(maxDimension / 2)));
                        } else {
                            resolve(null);
                        }
                        return;
                    }

                    if (blob.size <= MAX_FILE_SIZE_BYTES || quality <= 0.4) {
                        const name = file.name.replace(/\.[^.]+$/, '') + '.jpg';
                        resolve(new File([blob], name, { type: 'image/jpeg' }));
                        return;
                    }

                    attempt(quality - 0.15);
                }, 'image/jpeg', quality);
            };

            attempt(0.85);
        };

        img.onerror = () => {
            URL.revokeObjectURL(objectUrl);
            resolve(null);
        };

        img.src = objectUrl;
    });
}

// Formats that are already lossy-compressed -- if a photo already arrived in
// one of these under a sane size, it's not an iOS HEIC->PNG conversion and
// doesn't need to be re-encoded again.
const ALREADY_EFFICIENT_TYPES = ['image/jpeg', 'image/webp'];
const SKIP_COMPRESSION_BYTES = 800 * 1024;

// Keyed by input: { file, promise }. A form's submit handler re-validates
// every file input right before submitting, which -- if the user hits the
// button while a change-triggered compression is still running -- used to
// kick off a second, concurrent compressImage() on the same large image.
// Two simultaneous canvas encodes of a multi-MB photo can exhaust a mobile
// Safari tab's memory and silently fail. Caching the in-flight promise per
// (input, file) means a second caller just awaits the same run instead of
// starting its own.
const pendingCompression = new WeakMap();

function validateFileInput(input) {
    const file = input.files?.[0];
    const feedback = fileInputFeedback(input);

    if (!file) {
        pendingCompression.delete(input);
        input.classList.remove('is-invalid');
        setFeedback(feedback, { text: '' });
        return true;
    }

    const cached = pendingCompression.get(input);

    if (cached && cached.file === file) {
        return cached.promise;
    }

    if (!file.type.startsWith('image/')) {
        pendingCompression.delete(input);

        if (file.size > MAX_FILE_SIZE_BYTES) {
            input.classList.add('is-invalid');
            setFeedback(feedback, { text: `Ukuran file ${formatFileSize(file.size)} melebihi batas maksimal 5 MB.`, tone: 'error' });
            return false;
        }

        input.classList.remove('is-invalid');
        setFeedback(feedback, { text: '' });
        return true;
    }

    const alreadyEfficient = ALREADY_EFFICIENT_TYPES.includes(file.type) && file.size <= SKIP_COMPRESSION_BYTES;

    if (alreadyEfficient) {
        pendingCompression.delete(input);
        input.classList.remove('is-invalid');
        setFeedback(feedback, { text: '' });
        return true;
    }

    // Always normalize other images (PNG in particular): iOS Safari silently
    // re-encodes HEIC photos picked via <input type="file"> into uncompressed
    // PNG, which can turn a ~400KB camera photo into several MB even while
    // staying under the 5MB hard limit -- so this can't be gated on size alone.
    const form = input.closest('form');
    const submitButton = form?.querySelector('button[type="submit"], input[type="submit"]');

    input.classList.remove('is-invalid');
    input.disabled = true;
    if (submitButton) submitButton.disabled = true;
    setFeedback(feedback, { text: `Mengompres gambar (${formatFileSize(file.size)})...`, tone: 'muted' });

    const promise = compressImage(file).then((compressed) => {
        input.disabled = false;
        if (submitButton) submitButton.disabled = false;

        // The user picked a different file while this run was in flight --
        // that newer selection has already started its own validation, so
        // don't clobber it with this stale result.
        if (input.files?.[0] !== file) {
            return true;
        }

        pendingCompression.delete(input);

        const finalFile = compressed && compressed.size < file.size ? compressed : file;

        if (finalFile.size > MAX_FILE_SIZE_BYTES) {
            input.classList.add('is-invalid');
            setFeedback(feedback, { text: `Ukuran file ${formatFileSize(file.size)} melebihi batas maksimal 5 MB dan gagal dikompres otomatis. Silakan gunakan file lain.`, tone: 'error' });
            return false;
        }

        if (finalFile !== file) {
            replaceInputFile(input, finalFile);
            setFeedback(feedback, { text: `Gambar otomatis dioptimalkan dari ${formatFileSize(file.size)} menjadi ${formatFileSize(finalFile.size)}.`, tone: 'success' });
        } else {
            setFeedback(feedback, { text: '' });
        }

        return true;
    });

    pendingCompression.set(input, { file, promise });
    return promise;
}

document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.querySelector('.sidebar');
    const sidebarToggle = document.querySelector('#sidebarToggle');

    sidebarToggle?.addEventListener('click', () => {
        sidebar?.classList.toggle('show');
    });

    document.querySelectorAll('input[type="file"]').forEach((input) => {
        input.addEventListener('change', () => validateFileInput(input));
    });

    function showLoadingState(form) {
        const submitButton = form.querySelector('button[type="submit"], input[type="submit"]');

        if (!submitButton || submitButton.disabled) {
            return;
        }

        submitButton.dataset.originalHtml = submitButton.innerHTML;
        submitButton.disabled = true;
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memproses...';
    }

    document.querySelectorAll('form').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (event.defaultPrevented || form.dataset.noLoading !== undefined) {
                return;
            }

            const fileInputs = Array.from(form.querySelectorAll('input[type="file"]'));

            if (fileInputs.length === 0) {
                showLoadingState(form);
                return;
            }

            event.preventDefault();

            Promise.all(fileInputs.map(validateFileInput)).then((results) => {
                if (results.some((valid) => !valid)) {
                    fileInputs.find((input) => input.classList.contains('is-invalid'))?.focus();
                    return;
                }

                showLoadingState(form);
                form.submit();
            });
        });
    });
});
