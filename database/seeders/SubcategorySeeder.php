<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SubcategorySeeder extends Seeder
{
    public function run(): void
    {
        $subcategories = [
            'cosplay-pop-culture' => [
                'Anime Expo', 'Comic Con', 'Cosplay Competition', 'Figure Exhibition',
                'Fan Meeting', 'Manga Festival', 'Gaming Convention', 'K-Pop Event',
                'J-Pop Event', 'Tokusatsu', 'Idol Festival', 'Art Exhibition',
            ],
            'music-concert' => [
                'Live Band', 'DJ Performance', 'Orchestra', 'Indie', 'Pop',
                'Rock', 'Jazz', 'EDM', 'Mini Gig', 'Music Festival',
                'Meet & Greet', 'VIP Access', 'All Ages', '17+', '21+',
                'Seating', 'Standing', 'Acoustic', 'Hip Hop', 'R&B',
            ],
            'sports' => [
                'Marathon', 'Fun Run', 'Tournament', 'Championship', 'Exhibition Match',
                'Esports', 'Fitness Event', 'Cycling', 'Swimming', 'Football',
                'Basketball', 'Badminton', 'Tennis', 'Volleyball', 'Triathlon',
                'Color Run', 'Night Run', 'Trail Run',
            ],
        ];

        foreach ($subcategories as $categorySlug => $subs) {
            $category = Category::where('slug', $categorySlug)->first();

            if ($category) {
                foreach ($subs as $name) {
                    Subcategory::create([
                        'category_id' => $category->id,
                        'name' => $name,
                        'slug' => Str::slug($name),
                    ]);
                }
            }
        }
    }
}
