<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Event;
use App\Models\Organizer;
use App\Models\TicketType;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Categories
        $categories = [
            ['name' => 'Música', 'slug' => 'musica', 'icon' => '🎵', 'description' => 'Conciertos y festivales de música'],
            ['name' => 'Teatro', 'slug' => 'teatro', 'icon' => '🎭', 'description' => 'Obras de teatro y espectáculos en vivo'],
            ['name' => 'Deportes', 'slug' => 'deportes', 'icon' => '⚽', 'description' => 'Eventos deportivos'],
            ['name' => 'Arte & Cultura', 'slug' => 'arte-cultura', 'icon' => '🎨', 'description' => 'Exposiciones y eventos culturales'],
            ['name' => 'Gastronomía', 'slug' => 'gastronomia', 'icon' => '🍷', 'description' => 'Ferias y eventos gastronómicos'],
        ];
        foreach ($categories as $cat) {
            Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }

        // Venues
        $venues = [
            ['name' => 'Estadio Monumental', 'address' => 'Av. Figueroa Alcorta 7597', 'city' => 'Buenos Aires', 'state' => 'CABA', 'country' => 'Argentina', 'capacity' => 84567],
            ['name' => 'Teatro Colón', 'address' => 'Cerrito 628', 'city' => 'Buenos Aires', 'state' => 'CABA', 'country' => 'Argentina', 'capacity' => 2478],
            ['name' => 'Luna Park', 'address' => 'Av. Madero 420', 'city' => 'Buenos Aires', 'state' => 'CABA', 'country' => 'Argentina', 'capacity' => 8000],
        ];
        foreach ($venues as $v) {
            Venue::firstOrCreate(['name' => $v['name']], $v);
        }

        // Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@recoticket.com'],
            ['name' => 'Administrador', 'password' => Hash::make('password'), 'role' => 'admin']
        );

        // Organizer user
        $orgUser = User::firstOrCreate(
            ['email' => 'organizer@recoticket.com'],
            ['name' => 'Super Eventos SA', 'password' => Hash::make('password'), 'role' => 'organizer']
        );
        $organizer = Organizer::firstOrCreate(
            ['user_id' => $orgUser->id],
            ['name' => 'Super Eventos SA', 'description' => 'Productora líder de eventos en Argentina.', 'verified' => true]
        );

        // Buyer user
        User::firstOrCreate(
            ['email' => 'buyer@recoticket.com'],
            ['name' => 'Juan Comprador', 'password' => Hash::make('password'), 'role' => 'buyer']
        );

        // Events
        $musicCat = Category::where('slug', 'musica')->first();
        $teatroCat = Category::where('slug', 'teatro')->first();
        $deportesCat = Category::where('slug', 'deportes')->first();
        $lunaVenue = Venue::where('name', 'Luna Park')->first();
        $teatroVenue = Venue::where('name', 'Teatro Colón')->first();
        $estadioVenue = Venue::where('name', 'Estadio Monumental')->first();

        $events = [
            [
                'organizer_id'   => $organizer->id,
                'category_id'    => $musicCat->id,
                'venue_id'       => $lunaVenue->id,
                'title'          => 'Festival Rock en el Luna Park',
                'slug'           => 'festival-rock-luna-park-' . Str::random(6),
                'description'    => 'El festival de rock más esperado del año. Bandas nacionales e internacionales en un mismo escenario.',
                'start_date'     => now()->addDays(30),
                'end_date'       => now()->addDays(30)->addHours(5),
                'status'         => 'published',
                'featured'       => true,
                'total_capacity' => 8000,
                'ticket_types'   => [
                    ['name' => 'General', 'price' => 15000, 'quantity' => 5000, 'status' => 'active'],
                    ['name' => 'VIP', 'price' => 45000, 'quantity' => 500, 'status' => 'active'],
                    ['name' => 'Campo', 'price' => 25000, 'quantity' => 2500, 'status' => 'active'],
                ],
            ],
            [
                'organizer_id'   => $organizer->id,
                'category_id'    => $teatroCat->id,
                'venue_id'       => $teatroVenue->id,
                'title'          => 'Noche de Ópera en el Colón',
                'slug'           => 'noche-opera-colon-' . Str::random(6),
                'description'    => 'Una noche mágica con las mejores arias de la ópera clásica interpretadas por artistas de primer nivel mundial.',
                'start_date'     => now()->addDays(15),
                'end_date'       => now()->addDays(15)->addHours(3),
                'status'         => 'published',
                'featured'       => false,
                'total_capacity' => 2478,
                'ticket_types'   => [
                    ['name' => 'Pullman', 'price' => 8000, 'quantity' => 800, 'status' => 'active'],
                    ['name' => 'Palco', 'price' => 20000, 'quantity' => 200, 'status' => 'active'],
                ],
            ],
            [
                'organizer_id'   => $organizer->id,
                'category_id'    => $deportesCat->id,
                'venue_id'       => $estadioVenue->id,
                'title'          => 'Clásico River vs Boca — Torneo Apertura',
                'slug'           => 'clasico-river-boca-' . Str::random(6),
                'description'    => 'El superclásico del fútbol argentino. Viví la emoción del partido más importante del año.',
                'start_date'     => now()->addDays(45),
                'end_date'       => now()->addDays(45)->addHours(2),
                'status'         => 'published',
                'featured'       => true,
                'total_capacity' => 84567,
                'ticket_types'   => [
                    ['name' => 'Popular', 'price' => 5000, 'quantity' => 50000, 'status' => 'active'],
                    ['name' => 'Platea', 'price' => 12000, 'quantity' => 20000, 'status' => 'active'],
                    ['name' => 'Platea Premium', 'price' => 25000, 'quantity' => 5000, 'status' => 'active'],
                ],
            ],
        ];

        foreach ($events as $eventData) {
            $ticketTypes = $eventData['ticket_types'];
            unset($eventData['ticket_types']);

            $event = Event::firstOrCreate(['slug' => $eventData['slug']], $eventData);

            if ($event->wasRecentlyCreated) {
                foreach ($ticketTypes as $tt) {
                    $tt['event_id'] = $event->id;
                    $tt['quantity_sold'] = 0;
                    TicketType::create($tt);
                }
            }
        }
    }
}
