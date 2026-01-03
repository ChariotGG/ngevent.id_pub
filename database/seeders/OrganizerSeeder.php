<?php

namespace Database\Seeders;

use App\Models\Organizer;
use App\Models\OrganizerSocialLink;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrganizerSeeder extends Seeder
{
    public function run(): void
    {
        $organizers = [
            [
                'email' => 'organizer@ngevent.id',
                'organizer' => [
                    'name' => 'Event Organizer Demo',
                    'slug' => 'event-organizer-demo',
                    'bio' => 'Professional event organizer with 10+ years experience in organizing various events across Indonesia.',
                    'city' => 'Jakarta',
                    'province' => 'DKI Jakarta',
                    'bank_name' => 'BCA',
                    'bank_account_number' => '1234567890',
                    'bank_account_name' => 'PT Event Organizer Demo',
                    'is_verified' => true,
                    'verified_at' => now(),
                ],
                'social_links' => [
                    ['platform' => 'instagram', 'url' => 'https://instagram.com/eventorganizerdemo'],
                    ['platform' => 'twitter', 'url' => 'https://twitter.com/eventorgdemo'],
                ],
            ],
            [
                'email' => 'music@ngevent.id',
                'organizer' => [
                    'name' => 'Music Festival Indonesia',
                    'slug' => 'music-festival-indonesia',
                    'bio' => 'Bringing the best music experiences to Indonesia since 2015. We organize concerts, festivals, and intimate gigs.',
                    'city' => 'Bandung',
                    'province' => 'Jawa Barat',
                    'bank_name' => 'Mandiri',
                    'bank_account_number' => '0987654321',
                    'bank_account_name' => 'PT Music Festival Indonesia',
                    'is_verified' => true,
                    'verified_at' => now(),
                ],
                'social_links' => [
                    ['platform' => 'instagram', 'url' => 'https://instagram.com/musicfestid'],
                    ['platform' => 'youtube', 'url' => 'https://youtube.com/@musicfestid'],
                    ['platform' => 'website', 'url' => 'https://musicfestival.id'],
                ],
            ],
        ];

        foreach ($organizers as $data) {
            $user = User::where('email', $data['email'])->first();

            if ($user) {
                $organizer = Organizer::create(array_merge(
                    ['user_id' => $user->id],
                    $data['organizer']
                ));

                foreach ($data['social_links'] as $link) {
                    OrganizerSocialLink::create(array_merge(
                        ['organizer_id' => $organizer->id],
                        $link
                    ));
                }
            }
        }
    }
}
