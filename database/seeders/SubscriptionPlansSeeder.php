<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Plan PRUEBA TEMPORAL',
                'slug' => 'free',
                'description' => 'Prueba gratuita de 1 hora para evaluar el sistema. Incluye funciones básicas con límites.',
                'price_monthly' => 0,
                'price_yearly' => 0,
                'is_active' => true,
                'is_free' => true,
                'trial_days' => 0.042, // 1 hora = 1/24 días ≈ 0.042 días
                'max_doctors' => 1,
                'max_patients' => 10,
                'max_appointments_per_month' => 5,
                'max_locations' => 1,
                'max_staff' => 1,
                'features' => [
                    'basic_appointments',
                    'basic_patients',
                    'basic_reports',
                    'email_support',
                    'mobile_access'
                ],
                'sort_order' => 1,
            ],
            [
                'name' => 'Plan DOCTOR INDEPENDIENTE',
                'slug' => 'doctor',
                'description' => 'Ideal para doctores independientes que necesitan gestión completa de pacientes.',
                'price_monthly' => 70,
                'price_yearly' => 700,
                'is_active' => true,
                'is_free' => false,
                'trial_days' => 0,
                'max_doctors' => 1,
                'max_patients' => null, // unlimited
                'max_appointments_per_month' => null, // unlimited
                'max_locations' => 1,
                'max_staff' => 3, // 1 doctor + 2 assistants
                'features' => [
                    'unlimited_appointments',
                    'unlimited_patients',
                    'complete_medical_history',
                    'basic_reports',
                    'priority_support',
                    'mobile_access',
                    'appointment_reminders',
                    'patient_portal',
                    'prescription_management'
                ],
                'sort_order' => 2,
            ],
            [
                'name' => 'Plan CLÍNICA PEQUEÑA',
                'slug' => 'small_clinic',
                'description' => 'Para clínicas pequeñas con múltiples doctores y especialidades.',
                'price_monthly' => 340,
                'price_yearly' => 3400,
                'is_active' => true,
                'is_free' => false,
                'trial_days' => 0,
                'max_doctors' => 5,
                'max_patients' => null, // unlimited
                'max_appointments_per_month' => null, // unlimited
                'max_locations' => 1,
                'max_staff' => null, // unlimited administrative staff
                'features' => [
                    'unlimited_appointments',
                    'unlimited_patients',
                    'complete_medical_history',
                    'advanced_reports',
                    'priority_support',
                    'mobile_access',
                    'appointment_reminders',
                    'patient_portal',
                    'prescription_management',
                    'multi_specialty',
                    'lab_integration',
                    'integrated_billing',
                    'inventory_management',
                    'staff_management'
                ],
                'sort_order' => 3,
            ],
            [
                'name' => 'Plan CLÍNICA GRANDE',
                'slug' => 'large_clinic',
                'description' => 'Para clínicas grandes con múltiples ubicaciones y doctores ilimitados.',
                'price_monthly' => 299,
                'price_yearly' => 2990,
                'is_active' => true,
                'is_free' => false,
                'trial_days' => 0,
                'max_doctors' => null, // unlimited
                'max_patients' => null, // unlimited
                'max_appointments_per_month' => null, // unlimited
                'max_locations' => null, // unlimited
                'max_staff' => null, // unlimited
                'features' => [
                    'unlimited_appointments',
                    'unlimited_patients',
                    'unlimited_doctors',
                    'multiple_locations',
                    'complete_medical_history',
                    'advanced_reports',
                    'executive_dashboard',
                    'priority_support',
                    'mobile_access',
                    'appointment_reminders',
                    'patient_portal',
                    'prescription_management',
                    'multi_specialty',
                    'lab_integration',
                    'integrated_billing',
                    'inventory_management',
                    'staff_management',
                    'advanced_integrations',
                    'custom_api',
                    'white_label'
                ],
                'sort_order' => 4,
            ],
            [
                'name' => 'Plan ENTERPRISE',
                'slug' => 'enterprise',
                'description' => 'Solución empresarial completa con personalización total y soporte dedicado.',
                'price_monthly' => 1500,
                'price_yearly' => 15000,
                'is_active' => true,
                'is_free' => false,
                'trial_days' => 0,
                'max_doctors' => null, // unlimited
                'max_patients' => null, // unlimited
                'max_appointments_per_month' => null, // unlimited
                'max_locations' => null, // unlimited
                'max_staff' => null, // unlimited
                'features' => [
                    'unlimited_appointments',
                    'unlimited_patients',
                    'unlimited_doctors',
                    'multiple_locations',
                    'complete_medical_history',
                    'advanced_reports',
                    'executive_dashboard',
                    'dedicated_support',
                    'mobile_access',
                    'appointment_reminders',
                    'patient_portal',
                    'prescription_management',
                    'multi_specialty',
                    'lab_integration',
                    'integrated_billing',
                    'inventory_management',
                    'staff_management',
                    'advanced_integrations',
                    'custom_api',
                    'white_label',
                    'custom_development',
                    'dedicated_hosting',
                    'sla_guarantee',
                    'assisted_implementation',
                    'training_included',
                    'compliance_tools'
                ],
                'sort_order' => 5,
            ],
        ];

        foreach ($plans as $planData) {
            SubscriptionPlan::updateOrCreate(
                ['slug' => $planData['slug']],
                $planData
            );
        }
    }
}
