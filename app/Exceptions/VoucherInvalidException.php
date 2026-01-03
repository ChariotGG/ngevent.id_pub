<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class VoucherInvalidException extends Exception
{
    public const REASON_NOT_FOUND = 'not_found';
    public const REASON_EXPIRED = 'expired';
    public const REASON_USAGE_LIMIT = 'usage_limit';
    public const REASON_USER_LIMIT = 'user_limit';
    public const REASON_MIN_PURCHASE = 'min_purchase';
    public const REASON_NOT_APPLICABLE = 'not_applicable';
    public const REASON_INACTIVE = 'inactive';

    public function __construct(
        public string $code,
        public string $reason = self::REASON_NOT_FOUND,
        string $message = null
    ) {
        $defaultMessages = [
            self::REASON_NOT_FOUND => 'Kode voucher tidak ditemukan',
            self::REASON_EXPIRED => 'Voucher sudah kedaluwarsa',
            self::REASON_USAGE_LIMIT => 'Voucher sudah mencapai batas penggunaan',
            self::REASON_USER_LIMIT => 'Anda sudah mencapai batas penggunaan voucher ini',
            self::REASON_MIN_PURCHASE => 'Minimum pembelian belum tercapai',
            self::REASON_NOT_APPLICABLE => 'Voucher tidak berlaku untuk event ini',
            self::REASON_INACTIVE => 'Voucher tidak aktif',
        ];

        parent::__construct($message ?? ($defaultMessages[$reason] ?? 'Voucher tidak valid'));
    }

    public function render(Request $request): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'voucher_invalid',
                'message' => $this->getMessage(),
                'reason' => $this->reason,
                'code' => $this->code,
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return back()->withErrors([
            'voucher' => $this->getMessage()
        ])->withInput();
    }

    public function report(): bool
    {
        // Don't report to error tracking - this is expected user behavior
        return false;
    }
}
