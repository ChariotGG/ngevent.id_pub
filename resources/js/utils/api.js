/**
 * API Utility
 * Wrapper for Axios with error handling
 */

import { showError, showLoading, closeLoading } from './alert';

const api = {
    /**
     * GET request
     */
    async get(url, config = {}) {
        try {
            const response = await window.axios.get(url, config);
            return response.data;
        } catch (error) {
            this.handleError(error);
            throw error;
        }
    },

    /**
     * POST request
     */
    async post(url, data = {}, config = {}) {
        try {
            const response = await window.axios.post(url, data, config);
            return response.data;
        } catch (error) {
            this.handleError(error);
            throw error;
        }
    },

    /**
     * PUT request
     */
    async put(url, data = {}, config = {}) {
        try {
            const response = await window.axios.put(url, data, config);
            return response.data;
        } catch (error) {
            this.handleError(error);
            throw error;
        }
    },

    /**
     * DELETE request
     */
    async delete(url, config = {}) {
        try {
            const response = await window.axios.delete(url, config);
            return response.data;
        } catch (error) {
            this.handleError(error);
            throw error;
        }
    },

    /**
     * Handle API errors
     */
    handleError(error) {
        if (error.response) {
            // Server responded with error status
            const { status, data } = error.response;

            if (status === 401) {
                showError('Sesi Anda telah berakhir. Silakan login kembali.', 'Unauthorized');
                setTimeout(() => {
                    window.location.href = '/login';
                }, 2000);
            } else if (status === 403) {
                showError('Anda tidak memiliki akses ke resource ini.', 'Forbidden');
            } else if (status === 404) {
                showError('Data yang Anda cari tidak ditemukan.', 'Not Found');
            } else if (status === 422) {
                // Validation errors
                const errors = data.errors || {};
                const firstError = Object.values(errors)[0];
                showError(firstError ? firstError[0] : 'Validasi gagal', 'Validation Error');
            } else if (status === 500) {
                showError('Terjadi kesalahan pada server. Silakan coba lagi.', 'Server Error');
            } else {
                showError(data.message || 'Terjadi kesalahan', 'Error');
            }
        } else if (error.request) {
            // Request made but no response
            showError('Tidak dapat terhubung ke server. Periksa koneksi internet Anda.', 'Connection Error');
        } else {
            // Something else happened
            showError('Terjadi kesalahan: ' + error.message, 'Error');
        }
    },

    /**
     * Upload file with progress
     */
    async upload(url, formData, onProgress = null) {
        try {
            const config = {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            };

            if (onProgress) {
                config.onUploadProgress = (progressEvent) => {
                    const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                    onProgress(percentCompleted);
                };
            }

            const response = await window.axios.post(url, formData, config);
            return response.data;
        } catch (error) {
            this.handleError(error);
            throw error;
        }
    }
};

export default api;
