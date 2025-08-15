<?php

namespace Database\Seeders;

use App\Models\PerformerRole;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PerformerRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Role utama lama
        $roles = [
            'Penari',
            'Pemusik - Kendang',
            'Lengser',
            'Ambu',
            'Baksa',
            'MC',
            'Stage Crew',
        ];

        // Tambahan role spesifik
        $additionalRoles = [
            'Pemusik - Perkusi',
            'Pemusik - Kacapi',
            'Pemusik - Melodi',
            'Pemusik - Vokal',
            'Penari - Rama',
            'Penari - Sinta',
            'Penari - Pemayang',
            'Penari - Umum',
        ];

        // Gabungkan semua
        $allRoles = array_merge($roles, $additionalRoles);

        foreach ($allRoles as $role) {
            // Cek dulu supaya tidak duplikat
            PerformerRole::firstOrCreate(['name' => $role]);
        }
    }
}
