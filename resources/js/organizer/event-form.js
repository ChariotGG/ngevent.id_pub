/**
 * Organizer Event Form JS
 * Image upload (Dropzone), Date picker (Flatpickr), Form validation
 */

import Dropzone from 'dropzone';
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';
import { showError, showAlert, showLoading, closeLoading } from '../utils/alert';
import api from '../utils/api';

// Disable Dropzone auto-discover
Dropzone.autoDiscover = false;

/**
 * Initialize event form
 */
document.addEventListener('DOMContentLoaded', () => {
    initImageUpload();
    initDateTimePickers();
    initFormValidation();
    initCategorySelect();
});

/**
 * Image Upload with Dropzone
 */
function initImageUpload() {
    const dropzoneElement = document.getElementById('eventImagesDropzone');
    if (!dropzoneElement) return;

    const myDropzone = new Dropzone(dropzoneElement, {
        url: '/organizer/events/upload-image', // Temporary upload endpoint
        paramName: 'image',
        maxFilesize: 5, // MB
        maxFiles: 5,
        acceptedFiles: 'image/jpeg,image/png,image/jpg,image/webp',
        addRemoveLinks: true,
        dictDefaultMessage: `
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <p class="mt-1 text-sm text-gray-400">
                    Klik atau drag gambar ke sini
                </p>
                <p class="text-xs text-gray-500 mt-1">
                    PNG, JPG, WEBP up to 5MB (Max 5 images)
                </p>
            </div>
        `,
        dictRemoveFile: 'Hapus',
        dictCancelUpload: 'Batal',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        init: function() {
            this.on('success', function(file, response) {
                console.log('Upload success:', response);
                // Store uploaded image ID
                file.imageId = response.id;

                // Add hidden input with image ID
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'images[]';
                input.value = response.id;
                input.id = 'image-' + response.id;
                document.getElementById('eventForm').appendChild(input);
            });

            this.on('removedfile', function(file) {
                if (file.imageId) {
                    // Remove hidden input
                    const input = document.getElementById('image-' + file.imageId);
                    if (input) input.remove();

                    // Delete from server
                    api.delete('/organizer/events/delete-image/' + file.imageId)
                        .catch(err => console.error('Delete error:', err));
                }
            });

            this.on('error', function(file, message) {
                showError(message.message || 'Upload gagal');
                this.removeFile(file);
            });

            // Load existing images (for edit mode)
            const existingImages = document.getElementById('existingImages');
            if (existingImages) {
                try {
                    const images = JSON.parse(existingImages.value);
                    images.forEach(img => {
                        const mockFile = {
                            name: img.name,
                            size: img.size,
                            imageId: img.id
                        };
                        this.emit('addedfile', mockFile);
                        this.emit('thumbnail', mockFile, img.url);
                        this.emit('complete', mockFile);
                    });
                } catch (e) {
                    console.error('Parse existing images error:', e);
                }
            }
        }
    });

    // Custom styling for Dropzone
    dropzoneElement.classList.add('border-2', 'border-dashed', 'border-gray-700', 'rounded-lg', 'p-8', 'bg-gray-900', 'hover:border-primary', 'transition-colors');
}

/**
 * Date & Time Pickers
 */
function initDateTimePickers() {
    // Start Date & Time
    const startDateInput = document.getElementById('startDate');
    if (startDateInput) {
        flatpickr(startDateInput, {
            enableTime: true,
            dateFormat: 'Y-m-d H:i',
            time_24hr: true,
            minDate: 'today',
            onChange: function(selectedDates) {
                // Update end date min
                if (endDatePicker && selectedDates[0]) {
                    endDatePicker.set('minDate', selectedDates[0]);
                }
            }
        });
    }

    // End Date & Time
    const endDateInput = document.getElementById('endDate');
    let endDatePicker;
    if (endDateInput) {
        endDatePicker = flatpickr(endDateInput, {
            enableTime: true,
            dateFormat: 'Y-m-d H:i',
            time_24hr: true,
            minDate: 'today',
        });
    }

    // Sale Start Date
    const saleStartInput = document.getElementById('saleStartDate');
    if (saleStartInput) {
        flatpickr(saleStartInput, {
            enableTime: true,
            dateFormat: 'Y-m-d H:i',
            time_24hr: true,
            minDate: 'today',
        });
    }

    // Sale End Date
    const saleEndInput = document.getElementById('saleEndDate');
    if (saleEndInput) {
        flatpickr(saleEndInput, {
            enableTime: true,
            dateFormat: 'Y-m-d H:i',
            time_24hr: true,
            minDate: 'today',
        });
    }
}

/**
 * Form Validation
 */
function initFormValidation() {
    const form = document.getElementById('eventForm');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Validate
        if (!validateForm()) {
            return;
        }

        try {
            showLoading('Menyimpan event...');

            const formData = new FormData(form);
            const eventId = form.dataset.eventId;

            let response;
            if (eventId) {
                // Update
                response = await api.post(`/organizer/events/${eventId}`, formData);
            } else {
                // Create
                response = await api.post('/organizer/events', formData);
            }

            closeLoading();
            showAlert('Event berhasil disimpan!', 'Berhasil');

            // Redirect to event list
            setTimeout(() => {
                window.location.href = '/organizer/events';
            }, 1500);

        } catch (error) {
            closeLoading();
            console.error('Save error:', error);
        }
    });
}

/**
 * Validate form
 */
function validateForm() {
    let isValid = true;
    const errors = [];

    // Title
    const title = document.getElementById('title');
    if (!title?.value.trim()) {
        errors.push('Judul event harus diisi');
        isValid = false;
    }

    // Category
    const category = document.getElementById('category_id');
    if (!category?.value) {
        errors.push('Kategori harus dipilih');
        isValid = false;
    }

    // Start Date
    const startDate = document.getElementById('startDate');
    if (!startDate?.value) {
        errors.push('Tanggal mulai harus diisi');
        isValid = false;
    }

    // End Date
    const endDate = document.getElementById('endDate');
    if (!endDate?.value) {
        errors.push('Tanggal selesai harus diisi');
        isValid = false;
    }

    // Validate end date after start date
    if (startDate?.value && endDate?.value) {
        if (new Date(endDate.value) < new Date(startDate.value)) {
            errors.push('Tanggal selesai harus setelah tanggal mulai');
            isValid = false;
        }
    }

    // Location
    const location = document.getElementById('location');
    if (!location?.value.trim()) {
        errors.push('Lokasi harus diisi');
        isValid = false;
    }

    // Description
    const description = document.getElementById('description');
    if (!description?.value.trim()) {
        errors.push('Deskripsi harus diisi');
        isValid = false;
    }

    if (!isValid) {
        showError(errors.join('\n'), 'Validasi Gagal');
    }

    return isValid;
}

/**
 * Initialize category select
 */
function initCategorySelect() {
    // If using Select2 or similar library, init here
    // For now, just native select
}

console.log('📝 Event Form loaded');
