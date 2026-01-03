<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Cosplay & Pop Culture',
                'slug' => 'cosplay-pop-culture',
                'description' => 'Event cosplay, anime expo, comic con, dan budaya pop lainnya',
                'icon' => 'sparkles',
                'color' => '#FF6B6B',
                'sort_order' => 1,
            ],
            [
                'name' => 'Music & Concert',
                'slug' => 'music-concert',
                'description' => 'Konser musik, festival, live performance, dan pertunjukan musik',
                'icon' => 'musical-note',
                'color' => '#4ECDC4',
                'sort_order' => 2,
            ],
            [
                'name' => 'Sports',
                'slug' => 'sports',
                'description' => 'Event olahraga, marathon, turnamen, dan kompetisi',
                'icon' => 'trophy',
                'color' => '#45B7D1',
                'sort_order' => 3,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
