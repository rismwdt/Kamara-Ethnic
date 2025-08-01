<?php

namespace Database\Factories;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $start = Carbon::createFromTime(rand(8, 14), [0, 30][rand(0, 1)]);
        $end = (clone $start)->addMinutes(rand(60, 120));

        return [
            'booking_code' => strtoupper(Str::random(8)),
            'event_id' => rand(1, 3), // sesuaikan dengan data event yang tersedia
            'date' => $this->faker->dateTimeBetween('2025-08-01', '2025-08-10')->format('Y-m-d'),
            'start_time' => $start->format('H:i'),
            'end_time' => $end->format('H:i'),
            'location_detail' => $this->faker->randomElement(['Depok', 'Bekasi', 'Bogor']),
            'client_name' => $this->faker->name,
            'male_parents' => $this->faker->name('male'),
            'female_parents' => $this->faker->name('female'),
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->safeEmail,
            'nuance' => $this->faker->word,
            'image' => null,
            'notes' => $this->faker->sentence,
            'status' => 'tertunda',
        ];
    }
}
