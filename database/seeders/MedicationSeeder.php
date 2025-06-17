<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Medication;

class MedicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $medications = [
            // Analgésicos y Antiinflamatorios
            [
                'name' => 'Paracetamol',
                'generic_name' => 'Acetaminofén',
                'brand' => 'Tylenol',
                'dosage' => '500mg',
                'form' => 'Tableta',
                'manufacturer' => 'Johnson & Johnson',
                'category' => 'Analgésicos',
                'description' => 'Analgésico y antipirético para dolor leve a moderado',
                'stock_quantity' => 500,
                'min_stock_level' => 50,
                'unit_price' => 0.25,
                'purchase_price' => 0.15,
                'expiration_date' => '2025-12-31',
                'lot_number' => 'PAR2024001',
                'barcode' => '7501234567890',
                'status' => 'active',
                'requires_prescription' => false
            ],
            [
                'name' => 'Ibuprofeno',
                'generic_name' => 'Ibuprofeno',
                'brand' => 'Advil',
                'dosage' => '400mg',
                'form' => 'Tableta',
                'manufacturer' => 'Pfizer',
                'category' => 'AINES',
                'description' => 'Antiinflamatorio no esteroideo para dolor e inflamación',
                'stock_quantity' => 300,
                'min_stock_level' => 40,
                'unit_price' => 0.35,
                'purchase_price' => 0.20,
                'expiration_date' => '2025-08-15',
                'lot_number' => 'IBU2024002',
                'barcode' => '7501234567891',
                'status' => 'active',
                'requires_prescription' => false
            ],
            [
                'name' => 'Diclofenaco',
                'generic_name' => 'Diclofenaco Sódico',
                'brand' => 'Voltaren',
                'dosage' => '50mg',
                'form' => 'Tableta',
                'manufacturer' => 'Novartis',
                'category' => 'AINES',
                'description' => 'Antiinflamatorio para dolor articular y muscular',
                'stock_quantity' => 200,
                'min_stock_level' => 30,
                'unit_price' => 0.45,
                'purchase_price' => 0.25,
                'expiration_date' => '2025-10-20',
                'lot_number' => 'DIC2024003',
                'barcode' => '7501234567892',
                'status' => 'active',
                'requires_prescription' => true
            ],
            
            // Antibióticos
            [
                'name' => 'Amoxicilina',
                'generic_name' => 'Amoxicilina',
                'brand' => 'Amoxil',
                'dosage' => '500mg',
                'form' => 'Cápsula',
                'manufacturer' => 'GlaxoSmithKline',
                'category' => 'Antibióticos',
                'description' => 'Antibiótico betalactámico de amplio espectro',
                'stock_quantity' => 150,
                'min_stock_level' => 25,
                'unit_price' => 0.80,
                'purchase_price' => 0.50,
                'expiration_date' => '2025-06-30',
                'lot_number' => 'AMX2024004',
                'barcode' => '7501234567893',
                'status' => 'active',
                'requires_prescription' => true
            ],
            [
                'name' => 'Azitromicina',
                'generic_name' => 'Azitromicina',
                'brand' => 'Zithromax',
                'dosage' => '250mg',
                'form' => 'Tableta',
                'manufacturer' => 'Pfizer',
                'category' => 'Antibióticos',
                'description' => 'Antibiótico macrólido para infecciones respiratorias',
                'stock_quantity' => 80,
                'min_stock_level' => 15,
                'unit_price' => 1.20,
                'purchase_price' => 0.80,
                'expiration_date' => '2025-09-15',
                'lot_number' => 'AZI2024005',
                'barcode' => '7501234567894',
                'status' => 'active',
                'requires_prescription' => true
            ],
            [
                'name' => 'Cefalexina',
                'generic_name' => 'Cefalexina',
                'brand' => 'Keflex',
                'dosage' => '500mg',
                'form' => 'Cápsula',
                'manufacturer' => 'Eli Lilly',
                'category' => 'Antibióticos',
                'description' => 'Antibiótico cefalosporina de primera generación',
                'stock_quantity' => 120,
                'min_stock_level' => 20,
                'unit_price' => 0.95,
                'purchase_price' => 0.60,
                'expiration_date' => '2025-11-10',
                'lot_number' => 'CEF2024006',
                'barcode' => '7501234567895',
                'status' => 'active',
                'requires_prescription' => true
            ],
            
            // Cardiovasculares
            [
                'name' => 'Losartán',
                'generic_name' => 'Losartán Potásico',
                'brand' => 'Cozaar',
                'dosage' => '50mg',
                'form' => 'Tableta',
                'manufacturer' => 'Merck',
                'category' => 'Antihipertensivos',
                'description' => 'Antagonista de receptores de angiotensina II',
                'stock_quantity' => 250,
                'min_stock_level' => 35,
                'unit_price' => 0.65,
                'purchase_price' => 0.40,
                'expiration_date' => '2026-01-15',
                'lot_number' => 'LOS2024007',
                'barcode' => '7501234567896',
                'status' => 'active',
                'requires_prescription' => true
            ],
            [
                'name' => 'Enalapril',
                'generic_name' => 'Enalapril Maleato',
                'brand' => 'Vasotec',
                'dosage' => '10mg',
                'form' => 'Tableta',
                'manufacturer' => 'MSD',
                'category' => 'Antihipertensivos',
                'description' => 'Inhibidor de la enzima convertidora de angiotensina',
                'stock_quantity' => 180,
                'min_stock_level' => 30,
                'unit_price' => 0.55,
                'purchase_price' => 0.35,
                'expiration_date' => '2025-07-20',
                'lot_number' => 'ENA2024008',
                'barcode' => '7501234567897',
                'status' => 'active',
                'requires_prescription' => true
            ],
            [
                'name' => 'Atorvastatina',
                'generic_name' => 'Atorvastatina Cálcica',
                'brand' => 'Lipitor',
                'dosage' => '20mg',
                'form' => 'Tableta',
                'manufacturer' => 'Pfizer',
                'category' => 'Hipolipemiantes',
                'description' => 'Estatina para reducir colesterol y triglicéridos',
                'stock_quantity' => 160,
                'min_stock_level' => 25,
                'unit_price' => 1.15,
                'purchase_price' => 0.75,
                'expiration_date' => '2025-12-05',
                'lot_number' => 'ATO2024009',
                'barcode' => '7501234567898',
                'status' => 'active',
                'requires_prescription' => true
            ],
            
            // Antidiabéticos
            [
                'name' => 'Metformina',
                'generic_name' => 'Metformina Clorhidrato',
                'brand' => 'Glucophage',
                'dosage' => '850mg',
                'form' => 'Tableta',
                'manufacturer' => 'Bristol-Myers Squibb',
                'category' => 'Antidiabéticos',
                'description' => 'Hipoglucemiante oral para diabetes tipo 2',
                'stock_quantity' => 220,
                'min_stock_level' => 40,
                'unit_price' => 0.40,
                'purchase_price' => 0.25,
                'expiration_date' => '2026-03-10',
                'lot_number' => 'MET2024010',
                'barcode' => '7501234567899',
                'status' => 'active',
                'requires_prescription' => true
            ],
            [
                'name' => 'Glibenclamida',
                'generic_name' => 'Glibenclamida',
                'brand' => 'Daonil',
                'dosage' => '5mg',
                'form' => 'Tableta',
                'manufacturer' => 'Sanofi',
                'category' => 'Antidiabéticos',
                'description' => 'Sulfonilurea para diabetes tipo 2',
                'stock_quantity' => 140,
                'min_stock_level' => 20,
                'unit_price' => 0.35,
                'purchase_price' => 0.20,
                'expiration_date' => '2025-09-25',
                'lot_number' => 'GLI2024011',
                'barcode' => '7501234567900',
                'status' => 'active',
                'requires_prescription' => true
            ],
            
            // Respiratorios
            [
                'name' => 'Salbutamol',
                'generic_name' => 'Salbutamol Sulfato',
                'brand' => 'Ventolin',
                'dosage' => '100mcg/dosis',
                'form' => 'Inhalador',
                'manufacturer' => 'GlaxoSmithKline',
                'category' => 'Broncodilatadores',
                'description' => 'Broncodilatador de acción rápida para asma',
                'stock_quantity' => 50,
                'min_stock_level' => 10,
                'unit_price' => 8.50,
                'purchase_price' => 5.50,
                'expiration_date' => '2025-08-30',
                'lot_number' => 'SAL2024012',
                'barcode' => '7501234567901',
                'status' => 'active',
                'requires_prescription' => true
            ],
            [
                'name' => 'Loratadina',
                'generic_name' => 'Loratadina',
                'brand' => 'Claritin',
                'dosage' => '10mg',
                'form' => 'Tableta',
                'manufacturer' => 'Schering-Plough',
                'category' => 'Antihistamínicos',
                'description' => 'Antihistamínico para alergias',
                'stock_quantity' => 180,
                'min_stock_level' => 25,
                'unit_price' => 0.85,
                'purchase_price' => 0.55,
                'expiration_date' => '2025-11-15',
                'lot_number' => 'LOR2024013',
                'barcode' => '7501234567902',
                'status' => 'active',
                'requires_prescription' => false
            ],
            
            // Gastrointestinales
            [
                'name' => 'Omeprazol',
                'generic_name' => 'Omeprazol',
                'brand' => 'Prilosec',
                'dosage' => '20mg',
                'form' => 'Cápsula',
                'manufacturer' => 'AstraZeneca',
                'category' => 'Antiulcerosos',
                'description' => 'Inhibidor de la bomba de protones',
                'stock_quantity' => 200,
                'min_stock_level' => 30,
                'unit_price' => 0.75,
                'purchase_price' => 0.45,
                'expiration_date' => '2025-12-20',
                'lot_number' => 'OME2024014',
                'barcode' => '7501234567903',
                'status' => 'active',
                'requires_prescription' => true
            ],
            [
                'name' => 'Domperidona',
                'generic_name' => 'Domperidona',
                'brand' => 'Motilium',
                'dosage' => '10mg',
                'form' => 'Tableta',
                'manufacturer' => 'Janssen',
                'category' => 'Procinéticos',
                'description' => 'Procinético para náuseas y vómitos',
                'stock_quantity' => 120,
                'min_stock_level' => 20,
                'unit_price' => 0.95,
                'purchase_price' => 0.60,
                'expiration_date' => '2025-10-08',
                'lot_number' => 'DOM2024015',
                'barcode' => '7501234567904',
                'status' => 'active',
                'requires_prescription' => true
            ],
            
            // Vitaminas y Suplementos
            [
                'name' => 'Vitamina D3',
                'generic_name' => 'Colecalciferol',
                'brand' => 'Vitamin D3',
                'dosage' => '1000UI',
                'form' => 'Cápsula blanda',
                'manufacturer' => 'Nature Made',
                'category' => 'Vitaminas',
                'description' => 'Suplemento de vitamina D para salud ósea',
                'stock_quantity' => 300,
                'min_stock_level' => 50,
                'unit_price' => 0.30,
                'purchase_price' => 0.18,
                'expiration_date' => '2026-02-28',
                'lot_number' => 'VIT2024016',
                'barcode' => '7501234567905',
                'status' => 'active',
                'requires_prescription' => false
            ],
            [
                'name' => 'Complejo B',
                'generic_name' => 'Complejo Vitamínico B',
                'brand' => 'B-Complex',
                'dosage' => '50mg',
                'form' => 'Tableta',
                'manufacturer' => 'Centrum',
                'category' => 'Vitaminas',
                'description' => 'Complejo vitamínico del grupo B',
                'stock_quantity' => 250,
                'min_stock_level' => 40,
                'unit_price' => 0.45,
                'purchase_price' => 0.28,
                'expiration_date' => '2026-01-10',
                'lot_number' => 'COM2024017',
                'barcode' => '7501234567906',
                'status' => 'active',
                'requires_prescription' => false
            ],
            
            // Hormonales
            [
                'name' => 'Levotiroxina',
                'generic_name' => 'Levotiroxina Sódica',
                'brand' => 'Synthroid',
                'dosage' => '50mcg',
                'form' => 'Tableta',
                'manufacturer' => 'Abbott',
                'category' => 'Hormonas Tiroideas',
                'description' => 'Hormona tiroidea sintética para hipotiroidismo',
                'stock_quantity' => 180,
                'min_stock_level' => 30,
                'unit_price' => 0.85,
                'purchase_price' => 0.50,
                'expiration_date' => '2025-11-30',
                'lot_number' => 'LEV2024018',
                'barcode' => '7501234567907',
                'status' => 'active',
                'requires_prescription' => true
            ],
            
            // Productos con stock bajo (para testing)
            [
                'name' => 'Tramadol',
                'generic_name' => 'Tramadol Clorhidrato',
                'brand' => 'Tramal',
                'dosage' => '50mg',
                'form' => 'Cápsula',
                'manufacturer' => 'Grünenthal',
                'category' => 'Analgésicos Opioides',
                'description' => 'Analgésico opioide para dolor moderado a severo',
                'stock_quantity' => 15, // Stock bajo
                'min_stock_level' => 20,
                'unit_price' => 1.25,
                'purchase_price' => 0.80,
                'expiration_date' => '2025-06-15',
                'lot_number' => 'TRA2024019',
                'barcode' => '7501234567908',
                'status' => 'active',
                'requires_prescription' => true
            ],
            [
                'name' => 'Insulina NPH',
                'generic_name' => 'Insulina Humana NPH',
                'brand' => 'Humulin N',
                'dosage' => '100UI/mL',
                'form' => 'Vial 10mL',
                'manufacturer' => 'Eli Lilly',
                'category' => 'Insulinas',
                'description' => 'Insulina de acción intermedia',
                'stock_quantity' => 8, // Stock bajo
                'min_stock_level' => 12,
                'unit_price' => 25.00,
                'purchase_price' => 18.00,
                'expiration_date' => '2025-04-30', // Próximo a vencer
                'lot_number' => 'INS2024020',
                'barcode' => '7501234567909',
                'status' => 'active',
                'requires_prescription' => true
            ]
        ];

        foreach ($medications as $medicationData) {
            Medication::create($medicationData);
        }

        $this->command->info('✅ Medicamentos creados exitosamente!');
        $this->command->warn('⚠️  Algunos medicamentos tienen stock bajo y próximos a vencer para pruebas.');
    }
} 