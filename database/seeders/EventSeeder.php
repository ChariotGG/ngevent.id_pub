<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventDay;
use App\Models\Organizer;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\TicketVariant;
use App\Enums\EventStatus;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $organizers = Organizer::all();
        $categories = Category::all();

        if ($organizers->isEmpty() || $categories->isEmpty()) {
            $this->command->warn('Please run OrganizerSeeder and CategorySeeder first!');
            return;
        }

        $events = [
            // Cosplay & Pop Culture Events
            [
                'organizer_id' => $organizers->first()->id,
                'category_id' => $categories->where('slug', 'cosplay-pop-culture')->first()->id,
                'title' => 'Indonesia Comic Con 2026',
                'description' => "Indonesia Comic Con kembali hadir dengan lineup yang lebih spektakuler!\n\nNikmati pengalaman terbaik dalam dunia pop culture dengan:\n- Guest Star internasional\n- Cosplay Competition dengan hadiah puluhan juta\n- Exclusive Merchandise\n- Meet & Greet dengan artis favorit\n- Gaming Tournament\n- Dan masih banyak lagi!\n\nJangan sampai ketinggalan event terbesar tahun ini!",
                'short_description' => 'Event pop culture terbesar di Indonesia dengan cosplay competition, guest star internasional, dan exclusive merchandise!',
                'start_date' => Carbon::now()->addDays(30),
                'end_date' => Carbon::now()->addDays(32),
                'start_time' => '10:00',
                'end_time' => '21:00',
                'venue_name' => 'Jakarta Convention Center',
                'venue_address' => 'Jl. Gatot Subroto No.1',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'is_featured' => true,
                'is_free' => false,
                'status' => EventStatus::PUBLISHED,
                'published_at' => now(),
                'tickets' => [
                    ['name' => 'Daily Pass', 'description' => 'Akses 1 hari', 'price' => 150000, 'stock' => 1000],
                    ['name' => '3-Day Pass', 'description' => 'Akses 3 hari penuh', 'price' => 350000, 'stock' => 500],
                    ['name' => 'VIP Pass', 'description' => 'Akses VIP + Meet & Greet', 'price' => 750000, 'stock' => 100],
                ],
            ],
            [
                'organizer_id' => $organizers->first()->id,
                'category_id' => $categories->where('slug', 'cosplay-pop-culture')->first()->id,
                'title' => 'Anime Festival Asia Jakarta',
                'description' => "AFA Jakarta 2026 menghadirkan pengalaman anime terlengkap!\n\nHighlights:\n- J-Pop Concert dengan artis Jepang\n- Anime Screening terbaru\n- Cosplay Runway\n- Doujin Market\n- Anime Quiz Competition\n- Workshops & Panels",
                'short_description' => 'Festival anime terbesar di Asia Tenggara hadir di Jakarta!',
                'start_date' => Carbon::now()->addDays(45),
                'end_date' => Carbon::now()->addDays(47),
                'start_time' => '10:00',
                'end_time' => '22:00',
                'venue_name' => 'ICE BSD',
                'venue_address' => 'Jl. BSD Grand Boulevard',
                'city' => 'Tangerang',
                'province' => 'Banten',
                'is_featured' => true,
                'is_free' => false,
                'status' => EventStatus::PUBLISHED,
                'published_at' => now(),
                'tickets' => [
                    ['name' => 'Regular', 'description' => 'Akses area regular', 'price' => 200000, 'stock' => 2000],
                    ['name' => 'Premium', 'description' => 'Akses premium + exclusive merch', 'price' => 500000, 'stock' => 500],
                ],
            ],
            [
                'organizer_id' => $organizers->last()->id,
                'category_id' => $categories->where('slug', 'cosplay-pop-culture')->first()->id,
                'title' => 'Bandung Cosplay Party',
                'description' => "Cosplay party seru di kota Bandung!\n\nAcara meliputi:\n- Cosplay Competition\n- Coswalk\n- Photo Session\n- Games & Activities\n- Food Court\n\nTerbuka untuk semua umur!",
                'short_description' => 'Cosplay party seru dan meriah di Bandung!',
                'start_date' => Carbon::now()->addDays(14),
                'end_date' => Carbon::now()->addDays(14),
                'start_time' => '09:00',
                'end_time' => '18:00',
                'venue_name' => 'Trans Studio Mall Bandung',
                'venue_address' => 'Jl. Gatot Subroto No.289',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'is_featured' => false,
                'is_free' => false,
                'status' => EventStatus::PUBLISHED,
                'published_at' => now(),
                'tickets' => [
                    ['name' => 'Reguler', 'description' => 'Tiket masuk standar', 'price' => 50000, 'stock' => 500],
                    ['name' => 'Cosplayer', 'description' => 'Tiket khusus cosplayer + changing room', 'price' => 75000, 'stock' => 200],
                ],
            ],

            // Music & Concert Events
            [
                'organizer_id' => $organizers->first()->id,
                'category_id' => $categories->where('slug', 'music-concert')->first()->id,
                'title' => 'Java Jazz Festival 2026',
                'description' => "Java Jazz Festival kembali dengan lineup internasional!\n\nFeaturing:\n- International Jazz Artists\n- Local Jazz Heroes\n- Multiple Stages\n- Food & Beverage Area\n- After Party\n\n3 hari penuh musik jazz berkualitas!",
                'short_description' => 'Festival jazz terbesar di Indonesia dengan artis internasional!',
                'start_date' => Carbon::now()->addDays(60),
                'end_date' => Carbon::now()->addDays(62),
                'start_time' => '14:00',
                'end_time' => '23:59',
                'venue_name' => 'JIExpo Kemayoran',
                'venue_address' => 'Jl. Benyamin Suaeb',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'is_featured' => true,
                'is_free' => false,
                'status' => EventStatus::PUBLISHED,
                'published_at' => now(),
                'tickets' => [
                    ['name' => 'Daily Pass', 'description' => 'Akses 1 hari', 'price' => 850000, 'stock' => 3000],
                    ['name' => '3-Day Pass', 'description' => 'Akses 3 hari', 'price' => 2000000, 'stock' => 1000],
                    ['name' => 'VIP 3-Day', 'description' => 'VIP access 3 hari + lounge', 'price' => 5000000, 'stock' => 200],
                ],
            ],
            [
                'organizer_id' => $organizers->last()->id,
                'category_id' => $categories->where('slug', 'music-concert')->first()->id,
                'title' => 'Indie Music Festival Yogyakarta',
                'description' => "Festival musik indie terbesar di Yogyakarta!\n\nLineup:\n- Band-band indie lokal & nasional\n- Acoustic Stage\n- Art Installation\n- Local Food Market\n- Clothing Brand Booth",
                'short_description' => 'Festival musik indie dengan suasana Jogja yang asik!',
                'start_date' => Carbon::now()->addDays(21),
                'end_date' => Carbon::now()->addDays(22),
                'start_time' => '15:00',
                'end_time' => '23:00',
                'venue_name' => 'Mandala Krida',
                'venue_address' => 'Jl. Kenari',
                'city' => 'Yogyakarta',
                'province' => 'DI Yogyakarta',
                'is_featured' => false,
                'is_free' => false,
                'status' => EventStatus::PUBLISHED,
                'published_at' => now(),
                'tickets' => [
                    ['name' => 'Presale', 'description' => 'Harga early bird', 'price' => 100000, 'stock' => 500],
                    ['name' => 'Regular', 'description' => 'Harga normal', 'price' => 150000, 'stock' => 1500],
                ],
            ],
            [
                'organizer_id' => $organizers->first()->id,
                'category_id' => $categories->where('slug', 'music-concert')->first()->id,
                'title' => 'Rock in Solo 2026',
                'description' => "Festival rock terbesar di Jawa Tengah!\n\nLineup:\n- Band rock legendaris Indonesia\n- New wave rock bands\n- Metal stage\n- Mosh pit area\n\nBersiaplah untuk headbang!",
                'short_description' => 'Festival rock dengan band-band legendaris Indonesia!',
                'start_date' => Carbon::now()->addDays(35),
                'end_date' => Carbon::now()->addDays(35),
                'start_time' => '16:00',
                'end_time' => '23:59',
                'venue_name' => 'Stadion Manahan',
                'venue_address' => 'Jl. Adi Sucipto',
                'city' => 'Solo',
                'province' => 'Jawa Tengah',
                'is_featured' => true,
                'is_free' => false,
                'status' => EventStatus::PUBLISHED,
                'published_at' => now(),
                'tickets' => [
                    ['name' => 'Festival', 'description' => 'Standing area', 'price' => 250000, 'stock' => 5000],
                    ['name' => 'VIP', 'description' => 'VIP area + exclusive merch', 'price' => 750000, 'stock' => 500],
                ],
            ],

            // Sports Events
            [
                'organizer_id' => $organizers->last()->id,
                'category_id' => $categories->where('slug', 'sports')->first()->id,
                'title' => 'Jakarta Marathon 2026',
                'description' => "Jakarta Marathon mengajak kamu berlari melintasi kota Jakarta!\n\nKategori:\n- Full Marathon (42K)\n- Half Marathon (21K)\n- 10K Run\n- 5K Fun Run\n\nSetiap peserta mendapat:\n- Running jersey\n- Finisher medal\n- Snack pack\n- e-Certificate",
                'short_description' => 'Marathon tahunan terbesar di Jakarta dengan berbagai kategori!',
                'start_date' => Carbon::now()->addDays(50),
                'end_date' => Carbon::now()->addDays(50),
                'start_time' => '05:00',
                'end_time' => '12:00',
                'venue_name' => 'Monas',
                'venue_address' => 'Jl. Medan Merdeka',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'is_featured' => true,
                'is_free' => false,
                'status' => EventStatus::PUBLISHED,
                'published_at' => now(),
                'tickets' => [
                    ['name' => '5K Fun Run', 'description' => 'Kategori 5 kilometer', 'price' => 150000, 'stock' => 2000],
                    ['name' => '10K Run', 'description' => 'Kategori 10 kilometer', 'price' => 250000, 'stock' => 1500],
                    ['name' => 'Half Marathon', 'description' => 'Kategori 21 kilometer', 'price' => 400000, 'stock' => 1000],
                    ['name' => 'Full Marathon', 'description' => 'Kategori 42 kilometer', 'price' => 600000, 'stock' => 500],
                ],
            ],
            [
                'organizer_id' => $organizers->first()->id,
                'category_id' => $categories->where('slug', 'sports')->first()->id,
                'title' => 'Bali Triathlon 2026',
                'description' => "Triathlon internasional di Pulau Dewata!\n\nKategori:\n- Olympic Distance\n- Sprint Distance\n- Super Sprint\n\nNikmati keindahan Bali sambil berkompetisi!",
                'short_description' => 'Event triathlon internasional di Bali yang menantang!',
                'start_date' => Carbon::now()->addDays(75),
                'end_date' => Carbon::now()->addDays(75),
                'start_time' => '06:00',
                'end_time' => '14:00',
                'venue_name' => 'Sanur Beach',
                'venue_address' => 'Jl. Pantai Sanur',
                'city' => 'Denpasar',
                'province' => 'Bali',
                'is_featured' => false,
                'is_free' => false,
                'status' => EventStatus::PUBLISHED,
                'published_at' => now(),
                'tickets' => [
                    ['name' => 'Super Sprint', 'description' => 'Swim 400m, Bike 10K, Run 2.5K', 'price' => 500000, 'stock' => 300],
                    ['name' => 'Sprint', 'description' => 'Swim 750m, Bike 20K, Run 5K', 'price' => 750000, 'stock' => 200],
                    ['name' => 'Olympic', 'description' => 'Swim 1.5K, Bike 40K, Run 10K', 'price' => 1200000, 'stock' => 100],
                ],
            ],
            [
                'organizer_id' => $organizers->last()->id,
                'category_id' => $categories->where('slug', 'sports')->first()->id,
                'title' => 'Surabaya Basketball Tournament',
                'description' => "Turnamen basket 3x3 terbesar di Jawa Timur!\n\nKategori:\n- Open (umum)\n- U-18\n- U-15\n- Women\n\nTotal hadiah puluhan juta rupiah!",
                'short_description' => 'Turnamen basket 3x3 dengan berbagai kategori usia!',
                'start_date' => Carbon::now()->addDays(28),
                'end_date' => Carbon::now()->addDays(29),
                'start_time' => '08:00',
                'end_time' => '20:00',
                'venue_name' => 'Suncity Mall',
                'venue_address' => 'Jl. HR Muhammad',
                'city' => 'Surabaya',
                'province' => 'Jawa Timur',
                'is_featured' => false,
                'is_free' => false,
                'status' => EventStatus::PUBLISHED,
                'published_at' => now(),
                'tickets' => [
                    ['name' => 'Team Registration', 'description' => 'Registrasi per tim (4 orang)', 'price' => 300000, 'stock' => 32],
                ],
            ],

            // Free Event
            [
                'organizer_id' => $organizers->first()->id,
                'category_id' => $categories->where('slug', 'cosplay-pop-culture')->first()->id,
                'title' => 'Free Cosplay Gathering Jakarta',
                'description' => "Gathering cosplay GRATIS untuk komunitas!\n\nAcara:\n- Photo session bersama\n- Games\n- Sharing session\n- Networking\n\nTerbuka untuk semua cosplayer dan pecinta anime!",
                'short_description' => 'Gathering cosplay gratis untuk komunitas Jakarta!',
                'start_date' => Carbon::now()->addDays(7),
                'end_date' => Carbon::now()->addDays(7),
                'start_time' => '13:00',
                'end_time' => '18:00',
                'venue_name' => 'Taman Menteng',
                'venue_address' => 'Jl. HOS Cokroaminoto',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'is_featured' => false,
                'is_free' => true,
                'status' => EventStatus::PUBLISHED,
                'published_at' => now(),
                'tickets' => [
                    ['name' => 'Free Entry', 'description' => 'Tiket masuk gratis', 'price' => 0, 'stock' => 200],
                ],
            ],
        ];

        foreach ($events as $eventData) {
            $tickets = $eventData['tickets'];
            unset($eventData['tickets']);

            // Create event
            $event = Event::create($eventData);

            // Create event days
            $startDate = Carbon::parse($eventData['start_date']);
            $endDate = Carbon::parse($eventData['end_date']);

            $dayNumber = 1;
            while ($startDate->lte($endDate)) {
                EventDay::create([
                    'event_id' => $event->id,
                    'name' => 'Day ' . $dayNumber,
                    'date' => $startDate->toDateString(),
                ]);
                $startDate->addDay();
                $dayNumber++;
            }

            // Create tickets with variants
            $sortOrder = 1;
            foreach ($tickets as $ticketData) {
                $ticket = Ticket::create([
                    'event_id' => $event->id,
                    'name' => $ticketData['name'],
                    'description' => $ticketData['description'],
                    // 'type' => 'paid', // ← HAPUS BARIS INI
                    'sort_order' => $sortOrder++,
                    'is_active' => true,
                ]);

                TicketVariant::create([
                    'ticket_id' => $ticket->id,
                    'name' => null,
                    'price' => $ticketData['price'],
                    'stock' => $ticketData['stock'],
                    'sold_count' => 0,
                    'reserved_count' => 0,
                    'min_per_order' => 1,
                    'max_per_order' => 5,
                    'is_active' => true,
                ]);
            }

            // Update price range
            $event->update([
                'min_price' => $event->lowest_price,
                'max_price' => $event->highest_price,
            ]);
        }

        $this->command->info('Created ' . count($events) . ' events with tickets!');
    }
}
