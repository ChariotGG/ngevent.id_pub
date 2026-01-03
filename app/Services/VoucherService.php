<?php

namespace App\Services;

use App\Enums\VoucherType;
use App\Exceptions\VoucherInvalidException;
use App\Models\Event;
use App\Models\Order;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VoucherService
{
    public function create(array $data, ?User $createdBy = null): Voucher
    {
        return Voucher::create([
            'code' => $data['code'] ?? $this->generateCode(),
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'type' => $data['type'] ?? VoucherType::FIXED,
            'value' => $data['value'],
            'min_purchase' => $data['min_purchase'] ?? null,
            'max_discount' => $data['max_discount'] ?? null,
            'usage_limit' => $data['usage_limit'] ?? null,
            'usage_limit_per_user' => $data['usage_limit_per_user'] ?? null,
            'event_id' => $data['event_id'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'starts_at' => $data['starts_at'] ?? null,
            'expires_at' => $data['expires_at'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'created_by' => $createdBy?->id,
        ]);
    }

    public function update(Voucher $voucher, array $data): Voucher
    {
        $voucher->update(collect($data)->only([
            'name', 'description', 'type', 'value', 'min_purchase', 'max_discount',
            'usage_limit', 'usage_limit_per_user', 'event_id', 'category_id',
            'starts_at', 'expires_at', 'is_active',
        ])->toArray());

        return $voucher->fresh();
    }

    public function delete(Voucher $voucher): bool
    {
        if ($voucher->usage_count > 0) {
            return false;
        }

        return $voucher->delete();
    }

    public function validate(string $code, User $user, Event $event, int $subtotal): Voucher
    {
        $voucher = Voucher::where('code', strtoupper($code))->first();

        if (!$voucher) {
            throw new VoucherInvalidException($code, VoucherInvalidException::REASON_NOT_FOUND);
        }

        if (!$voucher->is_active) {
            throw new VoucherInvalidException($code, VoucherInvalidException::REASON_INACTIVE);
        }

        if ($voucher->isExpired()) {
            throw new VoucherInvalidException($code, VoucherInvalidException::REASON_EXPIRED);
        }

        if ($voucher->starts_at && $voucher->starts_at->isFuture()) {
            throw new VoucherInvalidException($code, VoucherInvalidException::REASON_INACTIVE, 'Voucher belum aktif');
        }

        if ($voucher->hasReachedLimit()) {
            throw new VoucherInvalidException($code, VoucherInvalidException::REASON_USAGE_LIMIT);
        }

        if ($voucher->hasUserReachedLimit($user->id)) {
            throw new VoucherInvalidException($code, VoucherInvalidException::REASON_USER_LIMIT);
        }

        if (!$voucher->isApplicableToEvent($event)) {
            throw new VoucherInvalidException($code, VoucherInvalidException::REASON_NOT_APPLICABLE);
        }

        if (!$voucher->isApplicableToAmount($subtotal)) {
            throw new VoucherInvalidException(
                $code,
                VoucherInvalidException::REASON_MIN_PURCHASE,
                'Minimum pembelian ' . $voucher->formatted_min_purchase
            );
        }

        return $voucher;
    }

    public function calculateDiscount(Voucher $voucher, int $subtotal): int
    {
        return $voucher->calculateDiscount($subtotal);
    }

    public function recordUsage(Voucher $voucher, User $user, Order $order, int $discountAmount): VoucherUsage
    {
        $voucher->incrementUsage();

        return VoucherUsage::create([
            'voucher_id' => $voucher->id,
            'user_id' => $user->id,
            'order_id' => $order->id,
            'discount_amount' => $discountAmount,
        ]);
    }

    public function releaseUsage(Order $order): void
    {
        $usage = VoucherUsage::where('order_id', $order->id)->first();

        if ($usage) {
            $usage->voucher->decrementUsage();
            $usage->delete();
        }
    }

    public function getAll(array $filters = []): LengthAwarePaginator
    {
        $query = Voucher::query()->with(['event', 'category', 'createdBy']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('code', 'ilike', "%{$search}%")
                    ->orWhere('name', 'ilike', "%{$search}%");
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (!empty($filters['event_id'])) {
            $query->where('event_id', $filters['event_id']);
        }

        return $query->latest()->paginate(20);
    }

    public function getActiveVouchers(): Collection
    {
        return Voucher::query()
            ->active()
            ->available()
            ->get();
    }

    public function getVoucherStats(Voucher $voucher): array
    {
        $usages = $voucher->usages()->with('order')->get();

        return [
            'total_usage' => $voucher->usage_count,
            'remaining_usage' => $voucher->remaining_usage,
            'total_discount_given' => $usages->sum('discount_amount'),
            'unique_users' => $usages->pluck('user_id')->unique()->count(),
        ];
    }

    protected function generateCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (Voucher::where('code', $code)->exists());

        return $code;
    }
}
