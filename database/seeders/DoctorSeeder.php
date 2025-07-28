<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Register;
use App\Models\Doctor;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $doctor1 = Register::create([
            'name' => 'Dr. Asha Patel',
            'email' => 'asha.patel@hospital.com',
            'password' => Hash::make('securepassword123'),
            'role' => 'doctor',
        ]);

        Doctor::create([
            'reid' => $doctor1->reid,
            'specialization' => 'Cardiology',
            'phone' => '9876543210',
        ]);

        $doctor2 = Register::create([
            'name' => 'Dr. Raj Mehta',
            'email' => 'raj.mehta@hospital.com',
            'password' => Hash::make('doctorpass456'),
            'role' => 'doctor',
        ]);

        Doctor::create([
            'reid' => $doctor2->reid,
            'specialization' => 'Neurology',
            'phone' => '9123456780',
        ]);
    }
}