<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Doctor;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario administrador
        $admin = User::firstOrCreate(
            ['email' => 'admin@clinica.com'],
            [
                'name' => 'Administrador Principal',
                'email' => 'admin@clinica.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        if ($admin->wasRecentlyCreated) {
            $this->command->info('✅ Usuario administrador creado: admin@clinica.com / admin123');
        } else {
            $this->command->info('ℹ️  Usuario administrador ya existe');
        }

        // Crear usuario doctor de prueba
        $doctorUser = User::firstOrCreate(
            ['email' => 'doctor.test@clinica.com'],
            [
                'name' => 'Dr. Test García',
                'email' => 'doctor.test@clinica.com',
                'password' => Hash::make('doctor123'),
                'role' => 'doctor',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        if ($doctorUser->wasRecentlyCreated) {
            // Crear perfil de doctor
            $doctor = Doctor::create([
                'user_id' => $doctorUser->id,
                'name' => 'Dr. Test García',
                'specialty' => 'Medicina General',
                'license_number' => 'TEST-001',
                'email' => 'doctor.test@clinica.com',
                'phone' => '555-TEST',
                'emergency_phone' => '555-EMERGENCY',
                'address' => 'Dirección de prueba',
                'experience_years' => 10,
                'education' => ['MD Universidad Test'],
                'certifications' => ['Certificado Test'],
                'languages' => ['Español', 'Inglés'],
                'status' => 'active',
                'bio' => 'Doctor de prueba para testing',
                'rating' => 4.5
            ]);

            $this->command->info('✅ Usuario doctor creado: doctor.test@clinica.com / doctor123');
        } else {
            $this->command->info('ℹ️  Usuario doctor ya existe');
        }

        // Crear usuario enfermera de prueba
        $nurse = User::firstOrCreate(
            ['email' => 'nurse.test@clinica.com'],
            [
                'name' => 'Enfermera Test López',
                'email' => 'nurse.test@clinica.com',
                'password' => Hash::make('nurse123'),
                'role' => 'nurse',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        if ($nurse->wasRecentlyCreated) {
            $this->command->info('✅ Usuario enfermera creado: nurse.test@clinica.com / nurse123');
        } else {
            $this->command->info('ℹ️  Usuario enfermera ya existe');
        }

        // Crear usuario recepcionista de prueba
        $receptionist = User::firstOrCreate(
            ['email' => 'reception.test@clinica.com'],
            [
                'name' => 'Recepcionista Test Martínez',
                'email' => 'reception.test@clinica.com',
                'password' => Hash::make('reception123'),
                'role' => 'receptionist',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        if ($receptionist->wasRecentlyCreated) {
            $this->command->info('✅ Usuario recepcionista creado: reception.test@clinica.com / reception123');
        } else {
            $this->command->info('ℹ️  Usuario recepcionista ya existe');
        }

        // Crear usuario técnico laboratorio de prueba
        $labTech = User::firstOrCreate(
            ['email' => 'lab.test@clinica.com'],
            [
                'name' => 'Técnico Lab Test Rodríguez',
                'email' => 'lab.test@clinica.com',
                'password' => Hash::make('lab123'),
                'role' => 'lab_technician',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        if ($labTech->wasRecentlyCreated) {
            $this->command->info('✅ Usuario técnico laboratorio creado: lab.test@clinica.com / lab123');
        } else {
            $this->command->info('ℹ️  Usuario técnico laboratorio ya existe');
        }

        // Crear usuario contador de prueba
        $accountant = User::firstOrCreate(
            ['email' => 'accountant.test@clinica.com'],
            [
                'name' => 'Contador Test Silva',
                'email' => 'accountant.test@clinica.com',
                'password' => Hash::make('accountant123'),
                'role' => 'accountant',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        if ($accountant->wasRecentlyCreated) {
            $this->command->info('✅ Usuario contador creado: accountant.test@clinica.com / accountant123');
        } else {
            $this->command->info('ℹ️  Usuario contador ya existe');
        }

        $this->command->info('');
        $this->command->info('🎯 USUARIOS DE PRUEBA CREADOS:');
        $this->command->info('👤 Admin: admin@clinica.com / admin123');
        $this->command->info('👨‍⚕️ Doctor: doctor.test@clinica.com / doctor123');
        $this->command->info('👩‍⚕️ Enfermera: nurse.test@clinica.com / nurse123');
        $this->command->info('👩‍💼 Recepcionista: reception.test@clinica.com / reception123');
        $this->command->info('🔬 Técnico Lab: lab.test@clinica.com / lab123');
        $this->command->info('💼 Contador: accountant.test@clinica.com / accountant123');
    }
}
