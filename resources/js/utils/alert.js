/**
 * Alert Utility
 * Wrapper for SweetAlert2 with ngevent.id branding
 */

import Swal from 'sweetalert2';

/**
 * Show success alert
 */
export function showAlert(message, title = 'Berhasil!') {
    return Swal.fire({
        title: title,
        text: message,
        icon: 'success',
        confirmButtonText: 'OK',
        confirmButtonColor: '#FF8FC7',
        background: '#1A1A1A',
        color: '#FAFAFA',
    });
}

/**
 * Show error alert
 */
export function showError(message, title = 'Error!') {
    return Swal.fire({
        title: title,
        text: message,
        icon: 'error',
        confirmButtonText: 'OK',
        confirmButtonColor: '#EF4444',
        background: '#1A1A1A',
        color: '#FAFAFA',
    });
}

/**
 * Show warning alert
 */
export function showWarning(message, title = 'Peringatan!') {
    return Swal.fire({
        title: title,
        text: message,
        icon: 'warning',
        confirmButtonText: 'OK',
        confirmButtonColor: '#F59E0B',
        background: '#1A1A1A',
        color: '#FAFAFA',
    });
}

/**
 * Show confirmation dialog
 */
export function showConfirm(message, title = 'Konfirmasi') {
    return Swal.fire({
        title: title,
        text: message,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#FF8FC7',
        cancelButtonColor: '#6B7280',
        background: '#1A1A1A',
        color: '#FAFAFA',
    });
}

/**
 * Show loading spinner
 */
export function showLoading(message = 'Memproses...') {
    return Swal.fire({
        title: message,
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: false,
        showConfirmButton: false,
        background: '#1A1A1A',
        color: '#FAFAFA',
        didOpen: () => {
            Swal.showLoading();
        }
    });
}

/**
 * Close loading spinner
 */
export function closeLoading() {
    Swal.close();
}

/**
 * Toast notification
 */
export function showToast(message, type = 'success') {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        background: '#1A1A1A',
        color: '#FAFAFA',
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    return Toast.fire({
        icon: type,
        title: message
    });
}

export default {
    showAlert,
    showError,
    showWarning,
    showConfirm,
    showLoading,
    closeLoading,
    showToast
};
