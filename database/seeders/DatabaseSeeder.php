<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Solo crear test users en entornos locales/testing
        if (app()->environment(['local', 'testing'])) {
            // User::factory(10)->create();

            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }

        // Ejecutar seeders en orden de dependencias
        $this->call([
            // 0. Crear planes de suscripción (base para usuarios)
            SubscriptionPlansSeeder::class,
            
            // 1. Crear usuarios admin y clínicas (base del sistema)
            AdminUserSeeder::class,
            ClinicSeeder::class,
            
            // 2. Crear usuarios doctores y sus perfiles
            DoctorUserSeeder::class,
            
            // 3. Crear pacientes con sus datos relacionados
            PatientSeeder::class,
            
            // 4. Crear medicamentos (inventario)
            MedicationSeeder::class,
            
            // 5. Crear citas médicas
            AppointmentSeeder::class,
            
            // 6. Crear videollamadas de prueba (depende de citas)
            VideoCallSeeder::class,
            
            // 7. Crear exámenes médicos con resultados
            MedicalExamSeeder::class,
            
            // 8. Crear cirugías
            SurgerySeeder::class,
            
            // 9. Crear signos vitales para pacientes
            VitalSignSeeder::class,
            
            // 10. Crear facturas (depende de citas y pacientes)
            InvoiceSeeder::class,
        ]);

        $this->command->info('');
        $this->command->info('🏥 ===== SISTEMA MÉDICO SEEDING COMPLETADO =====');
        $this->command->info('');
        $this->command->info('✅ Datos creados exitosamente:');
        $this->command->info('   💳 5 Planes de suscripción (incluyendo plan GRATUITO)');
        $this->command->info('   🏢 5 Clínicas con especialidades');
        $this->command->info('   👨‍⚕️ 10 Doctores especializados');
        $this->command->info('   👥 10 Pacientes con historiales completos');
        $this->command->info('   💊 20 Medicamentos en inventario');
        $this->command->info('   📅 Citas para las próximas 4 semanas');
        $this->command->info('   📹 Videollamadas de prueba configuradas');
        $this->command->info('   🧪 50 Exámenes médicos con resultados');
        $this->command->info('   🏥 30+ Cirugías programadas');
        $this->command->info('   💓 Signos vitales por paciente');
        $this->command->info('   💰 100+ Facturas con diferentes estados');
        $this->command->info('');
        $this->command->info('🔑 Usuarios de prueba:');
        $this->command->info('   Admin: admin@example.com / password');
        $this->command->info('   Doctores: [nombre].[apellido]@clinica.com / doctor123');
        $this->command->info('');
        $this->command->info('📹 Sistema de Videollamadas:');
        $this->command->info('   ✅ Integración con Jitsi Meet configurada');
        $this->command->info('   ✅ Citas de videoconsulta de prueba creadas');
        $this->command->info('   ✅ Interfaz de videollamadas disponible');
        $this->command->info('');
        $this->command->info('🌐 API disponible en: https://backendmedical-main-kqne9d.laravel.cloud/api');
        $this->command->info('📖 Documentación: Ver archivo API_ENDPOINTS.md');
    }
}
