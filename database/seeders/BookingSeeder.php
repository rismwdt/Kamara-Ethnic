<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingSeeder extends Seeder
{
    public function run()
    {
        // Pastikan sudah ada minimal 1 user di tabel users
        $userId = \App\Models\User::query()->inRandomOrder()->value('id')
            ?? \App\Models\User::factory()->create()->id;

        $bookings = [
            [
                'client_name'     => 'Hana & Ardi',
                'event_id'        => 1,
                'date'            => '2025-08-25',
                'start_time'      => '09:00:00',
                'end_time'        => '11:00:00',
                'location_detail' => 'Hotel Grand Preanger, Jl. Asia Afrika No.81, Bandung',
                // Grand Preanger (sekitar Alun-Alun Bandung)
                'latitude'        => -6.92186,
                'longitude'       => 107.60694,
                'male_parents'    => 'Adi & Lita',
                'female_parents'  => 'Rina & Budi',
                'phone'           => '081344556677',
                'email'           => 'hana@example.com',
                'nuance'          => 'Pernikahan modern',
                'notes'           => 'MC interaktif, musik akustik',
                'image'           => 'image/photo15.jpg',
                'location_photo'  => 'location_photos/photo15.jpg',
            ],
            [
                'client_name'     => 'Maya & Fikri',
                'event_id'        => 2,
                'date'            => '2025-08-25',
                'start_time'      => '12:00:00',
                'end_time'        => '14:00:00',
                'location_detail' => 'Gedung Merdeka, Jl. Asia Afrika No.65, Bandung',
                // Gedung Merdeka
                'latitude'        => -6.92108,
                'longitude'       => 107.60763,
                'male_parents'    => 'Joko & Siti',
                'female_parents'  => 'Dina & Agus',
                'phone'           => '081355667788',
                'email'           => 'maya@example.com',
                'nuance'          => 'Pernikahan tradisional',
                'notes'           => 'Dekorasi alami, band tradisional',
                'image'           => 'image/photo16.jpg',
                'location_photo'  => 'location_photos/photo16.jpg',
            ],
            [
                'client_name'     => 'Lina & Dewa',
                'event_id'        => 3,
                'date'            => '2025-08-26',
                'start_time'      => '08:30:00',
                'end_time'        => '10:30:00',
                'location_detail' => 'Gedung Sate, Jl. Diponegoro No.22, Bandung',
                // Gedung Sate
                'latitude'        => -6.90247,
                'longitude'       => 107.61870,
                'male_parents'    => 'Budi & Ani',
                'female_parents'  => 'Tina & Ardi',
                'phone'           => '081366778899',
                'email'           => 'lina@example.com',
                'nuance'          => 'Pernikahan modern',
                'notes'           => 'MC lucu, lighting bagus',
                'image'           => 'image/photo17.jpg',
                'location_photo'  => 'location_photos/photo17.jpg',
            ],
            [
                'client_name'     => 'Rafi & Selvi',
                'event_id'        => 4,
                'date'            => '2025-08-26',
                'start_time'      => '11:00:00',
                'end_time'        => '13:00:00',
                'location_detail' => 'Aston Braga Hotel & Residence (Ballroom), Jl. Braga No.99, Bandung',
                // Aston Braga
                'latitude'        => -6.92273,
                'longitude'       => 107.60962,
                'male_parents'    => 'Agus & Lilis',
                'female_parents'  => 'Rina & Eko',
                'phone'           => '081377889900',
                'email'           => 'rafi@example.com',
                'nuance'          => 'Pernikahan tradisional',
                'notes'           => 'Dekorasi minimalis dan band akustik',
                'image'           => 'image/photo18.jpg',
                'location_photo'  => 'location_photos/photo18.jpg',
            ],
            [
                'client_name'     => 'Nabila & Faris',
                'event_id'        => 4,
                'date'            => '2025-08-27',
                'start_time'      => '18:00:00',
                'end_time'        => '21:00:00',
                'location_detail' => 'The Trans Luxury Hotel Bandung (The Grand Ballroom), Jl. Gatot Subroto No.289',
                // The Trans Luxury Hotel / Trans Studio area
                'latitude'        => -6.92563,
                'longitude'       => 107.63601,
                'male_parents'    => 'Rahmat & Wati',
                'female_parents'  => 'Sari & Fajar',
                'phone'           => '081388889999',
                'email'           => 'nabila@example.com',
                'nuance'          => 'Glamour elegan',
                'notes'           => 'Butuh LED backdrop dan string quartet',
                'image'           => 'image/photo19.jpg',
                'location_photo'  => 'location_photos/photo19.jpg',
            ],
        ];

        foreach ($bookings as $booking) {
            DB::table('bookings')->insert([
                'booking_code'   => 'BK' . now()->format('ymd') . Str::upper(Str::random(4)), // 12 karakter
                'client_name'    => $booking['client_name'],
                'event_id'       => $booking['event_id'],
                'date'           => $booking['date'],
                'start_time'     => $booking['start_time'],
                'end_time'       => $booking['end_time'],
                'location_detail'=> $booking['location_detail'],
                'latitude'       => $booking['latitude'],
                'longitude'      => $booking['longitude'],
                'male_parents'   => $booking['male_parents'],
                'female_parents' => $booking['female_parents'],
                'phone'          => $booking['phone'],
                'email'          => $booking['email'],
                'nuance'         => $booking['nuance'],
                'notes'          => $booking['notes'],
                'image'          => $booking['image'],
                'location_photo' => $booking['location_photo'],
                'status'         => 'tertunda',
                'user_id'        => $userId,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }
    }
}
