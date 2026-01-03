<?php

namespace Database\Seeders;

use App\Enums\VoucherType;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        $vouchers = [
            [
                'code' => 'WELCOME10',
                'name' => 'Welcome Discount',
                'description' => 'Diskon 10% untuk pengguna baru',
                'type' => VoucherType::PERCENTAGE,
                'value' => 10,
                'max_discount' => 50000,
                'usage_limit' => 1000,
                'usage_limit_per_user' => 1,
                'expires_at' => now()->addMonths(3),
            ],
            [
                'code' => 'NGEVENT50K',
                'name' => 'Discount Rp 50.000',
                'description' => 'Potongan langsung Rp 50.000',
                'type' => VoucherType::FIXED,
                'value' => 50000,
                'min_purchase' => 200000,
                'usage_limit' => 500,
                'expires_at' => now()->addMonth(),
            ],
            [
                'code' => 'MUSIC25',
                'name' => 'Music Festival Discount',
                'description' => 'Diskon 25% untuk event musik',
                'type' => VoucherType::PERCENTAGE,
                'value' => 25,
                'max_discount' => 100000,
                'min_purchase' => 100000,
                'category_id' => 2, // Music & Concert
                'usage_limit' => 200,
                'expires_at' => now()->addWeeks(2),
            ],
            [
                'code' => 'FREESHIP',
                'name' => 'Free Service Fee',
                'description' => 'Gratis biaya layanan',
                'type' => VoucherType::FIXED,
                'value' => 5000,
                'usage_limit' => null, // Unlimited
                'expires_at' => now()->addYear(),
            ],
        ];

        foreach ($vouchers as $voucher) {
            Voucher::create(array_merge(
                ['created_by' => $admin?->id],
                $voucher
            ));
        }
    }
}
