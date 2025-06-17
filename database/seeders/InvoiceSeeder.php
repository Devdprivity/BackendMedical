<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Invoice;
use Carbon\Carbon;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            ['description' => 'Consulta médica general', 'price' => 80.00],
            ['description' => 'Consulta especializada cardiología', 'price' => 120.00],
            ['description' => 'Consulta pediatría', 'price' => 100.00],
            ['description' => 'Consulta ginecología', 'price' => 110.00],
            ['description' => 'Consulta dermatología', 'price' => 95.00],
            ['description' => 'Consulta traumatología', 'price' => 130.00],
            ['description' => 'Consulta neurología', 'price' => 140.00],
            ['description' => 'Consulta oftalmología', 'price' => 115.00],
            ['description' => 'Consulta medicina interna', 'price' => 105.00],
            ['description' => 'Consulta psiquiatría', 'price' => 125.00],
            ['description' => 'Hemograma completo', 'price' => 25.00],
            ['description' => 'Química sanguínea', 'price' => 35.00],
            ['description' => 'Radiografía de tórax', 'price' => 45.00],
            ['description' => 'Electrocardiograma', 'price' => 30.00],
            ['description' => 'Ecografía abdominal', 'price' => 85.00],
            ['description' => 'Tomografía computarizada', 'price' => 250.00],
            ['description' => 'Resonancia magnética', 'price' => 350.00],
            ['description' => 'Colecistectomía laparoscópica', 'price' => 2500.00],
            ['description' => 'Artroscopia de rodilla', 'price' => 1800.00],
            ['description' => 'Apendicectomía', 'price' => 2200.00],
            ['description' => 'Cesárea', 'price' => 1500.00],
            ['description' => 'Hernia inguinal', 'price' => 1200.00],
            ['description' => 'Medicamentos', 'price' => 45.00],
            ['description' => 'Hospitalización día', 'price' => 150.00],
            ['description' => 'Urgencias', 'price' => 200.00]
        ];

        $paymentMethods = ['cash', 'credit_card', 'debit_card', 'bank_transfer', 'insurance'];
        $invoices = [];
        $invoiceNumber = 1;

        // Generar facturas para los últimos 3 meses
        for ($i = 1; $i <= 100; $i++) {
            $issueDate = Carbon::now()->subDays(rand(1, 90));
            $dueDate = $issueDate->copy()->addDays(30);
            
            // Seleccionar servicios (1-3 servicios por factura)
            $numServices = rand(1, 3);
            $selectedServices = array_rand($services, $numServices);
            if (!is_array($selectedServices)) {
                $selectedServices = [$selectedServices];
            }
            
            $subtotal = 0;
            $items = [];
            
            foreach ($selectedServices as $serviceIndex) {
                $service = $services[$serviceIndex];
                $quantity = 1;
                $unitPrice = $service['price'];
                $total = $quantity * $unitPrice;
                $subtotal += $total;
                
                $items[] = [
                    'description' => $service['description'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total' => $total
                ];
            }
            
            $tax = round($subtotal * 0.19, 2); // IVA 19%
            $total = $subtotal + $tax;
            
            // Determinar estado de pago
            $paymentStatus = 'pending';
            $paymentDate = null;
            $paymentMethod = null;
            
            if ($issueDate->diffInDays(now()) > 7) {
                $paymentStatus = ['paid', 'pending', 'overdue'][rand(0, 2)];
                
                // 70% pagadas, 20% pendientes, 10% vencidas
                $rand = rand(1, 100);
                if ($rand <= 70) {
                    $paymentStatus = 'paid';
                    $paymentDate = $issueDate->copy()->addDays(rand(1, 20));
                    $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
                } elseif ($rand <= 90) {
                    $paymentStatus = 'pending';
                } else {
                    $paymentStatus = 'overdue';
                }
            }
            
            $invoice = [
                'patient_id' => rand(1, 10),
                'appointment_id' => rand(1, 50), // Asumiendo 50+ citas
                'invoice_number' => 'INV-2024-' . str_pad($invoiceNumber, 4, '0', STR_PAD_LEFT),
                'issue_date' => $issueDate->format('Y-m-d'),
                'due_date' => $dueDate->format('Y-m-d'),
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => rand(0, 1) ? round($subtotal * 0.05, 2) : 0, // 5% descuento ocasional
                'total' => $total,
                'payment_status' => $paymentStatus,
                'payment_date' => $paymentDate ? $paymentDate->format('Y-m-d') : null,
                'payment_method' => $paymentMethod,
                'notes' => rand(1, 100) <= 20 ? 'Paciente con seguro médico' : '',
                'items' => json_encode($items),
                'created_at' => $issueDate,
                'updated_at' => $paymentDate ?? $issueDate
            ];
            
            // Aplicar descuento al total
            if ($invoice['discount'] > 0) {
                $invoice['total'] = $invoice['subtotal'] + $invoice['tax'] - $invoice['discount'];
            }
            
            $invoices[] = $invoice;
            $invoiceNumber++;
        }
        
        // Agregar algunas facturas específicas
        $specificInvoices = [
            [
                'patient_id' => 1,
                'appointment_id' => 1,
                'invoice_number' => 'INV-2024-' . str_pad($invoiceNumber++, 4, '0', STR_PAD_LEFT),
                'issue_date' => Carbon::today()->format('Y-m-d'),
                'due_date' => Carbon::today()->addDays(30)->format('Y-m-d'),
                'subtotal' => 120.00,
                'tax' => 22.80,
                'discount' => 0,
                'total' => 142.80,
                'payment_status' => 'pending',
                'payment_date' => null,
                'payment_method' => null,
                'notes' => 'Consulta cardiológica de hoy',
                'items' => json_encode([
                    [
                        'description' => 'Consulta especializada cardiología',
                        'quantity' => 1,
                        'unit_price' => 120.00,
                        'total' => 120.00
                    ]
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'patient_id' => 3,
                'appointment_id' => 3,
                'invoice_number' => 'INV-2024-' . str_pad($invoiceNumber++, 4, '0', STR_PAD_LEFT),
                'issue_date' => Carbon::yesterday()->format('Y-m-d'),
                'due_date' => Carbon::yesterday()->addDays(30)->format('Y-m-d'),
                'subtotal' => 2500.00,
                'tax' => 475.00,
                'discount' => 125.00, // Descuento por seguro
                'total' => 2850.00,
                'payment_status' => 'paid',
                'payment_date' => Carbon::today()->format('Y-m-d'),
                'payment_method' => 'insurance',
                'notes' => 'Pago cubierto por seguro médico',
                'items' => json_encode([
                    [
                        'description' => 'Colecistectomía laparoscópica',
                        'quantity' => 1,
                        'unit_price' => 2500.00,
                        'total' => 2500.00
                    ]
                ]),
                'created_at' => Carbon::yesterday(),
                'updated_at' => now()
            ],
            // Factura vencida para testing
            [
                'patient_id' => 8,
                'appointment_id' => 25,
                'invoice_number' => 'INV-2024-' . str_pad($invoiceNumber++, 4, '0', STR_PAD_LEFT),
                'issue_date' => Carbon::now()->subDays(45)->format('Y-m-d'),
                'due_date' => Carbon::now()->subDays(15)->format('Y-m-d'),
                'subtotal' => 350.00,
                'tax' => 66.50,
                'discount' => 0,
                'total' => 416.50,
                'payment_status' => 'overdue',
                'payment_date' => null,
                'payment_method' => null,
                'notes' => 'Paciente no responde a llamadas de cobranza',
                'items' => json_encode([
                    [
                        'description' => 'Consulta medicina interna',
                        'quantity' => 1,
                        'unit_price' => 105.00,
                        'total' => 105.00
                    ],
                    [
                        'description' => 'Hemograma completo',
                        'quantity' => 1,
                        'unit_price' => 25.00,
                        'total' => 25.00
                    ],
                    [
                        'description' => 'Química sanguínea',
                        'quantity' => 1,
                        'unit_price' => 35.00,
                        'total' => 35.00
                    ],
                    [
                        'description' => 'Radiografía de tórax',
                        'quantity' => 1,
                        'unit_price' => 45.00,
                        'total' => 45.00
                    ],
                    [
                        'description' => 'Medicamentos',
                        'quantity' => 3,
                        'unit_price' => 45.00,
                        'total' => 135.00
                    ]
                ]),
                'created_at' => Carbon::now()->subDays(45),
                'updated_at' => Carbon::now()->subDays(45)
            ]
        ];
        
        $invoices = array_merge($invoices, $specificInvoices);
        
        foreach ($invoices as $invoiceData) {
            Invoice::create($invoiceData);
        }
        
        $this->command->info('✅ Facturas creadas exitosamente!');
        $this->command->info('💰 Se crearon ' . count($invoices) . ' facturas con diferentes estados de pago.');
        
        // Estadísticas
        $paid = collect($invoices)->where('payment_status', 'paid')->count();
        $pending = collect($invoices)->where('payment_status', 'pending')->count();
        $overdue = collect($invoices)->where('payment_status', 'overdue')->count();
        
        $this->command->info("📊 Estadísticas: {$paid} pagadas, {$pending} pendientes, {$overdue} vencidas");
    }
} 