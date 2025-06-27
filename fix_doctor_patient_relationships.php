<?php

/**
 * Script para establecer relaciones médico-paciente para datos existentes
 * Ejecutar: php fix_doctor_patient_relationships.php
 */

// Configuración de base de datos - AJUSTAR SEGÚN TU CONFIGURACIÓN
$host = 'localhost';
$dbname = 'medical_system'; // Cambiar por el nombre real de tu base de datos
$username = 'root'; // Cambiar por tu usuario de DB
$password = ''; // Cambiar por tu contraseña de DB

try {
    // Conexión a la base de datos
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✅ Conexión exitosa a la base de datos\n";

    // Crear relaciones basadas en creadores de pacientes
    createRelationshipsFromPatientCreators($pdo);

    // Crear relaciones basadas en citas existentes
    createRelationshipsFromAppointments($pdo);

    // Crear relaciones basadas en tratamientos existentes
    createRelationshipsFromTreatments($pdo);

    echo "\n🎉 ¡Proceso completado exitosamente!\n";

} catch (PDOException $e) {
    echo "❌ Error de conexión: " . $e->getMessage() . "\n";
    echo "\n📝 Asegúrate de ajustar los datos de conexión en la parte superior del script:\n";
    echo "   - Host: $host\n";
    echo "   - Base de datos: $dbname\n";
    echo "   - Usuario: $username\n";
    echo "   - Contraseña: [configurada]\n";
}

/**
 * Crear relaciones basadas en quien creó los pacientes
 */
function createRelationshipsFromPatientCreators($pdo) {
    echo "\n🔄 Creando relaciones basadas en creadores de pacientes...\n";

    // Obtener pacientes creados por médicos
    $sql = "
        SELECT
            p.id as patient_id,
            d.id as doctor_id,
            u.clinic_id,
            p.created_at
        FROM patients p
        JOIN users u ON p.created_by = u.id
        JOIN doctors d ON u.id = d.user_id
        WHERE u.role = 'doctor'
        AND p.created_by IS NOT NULL
    ";

    $stmt = $pdo->query($sql);
    $patients = $stmt->fetchAll(PDO::FETCH_OBJ);
    $created = 0;

    foreach ($patients as $patient) {
        // Verificar si ya existe la relación
        $checkSql = "
            SELECT COUNT(*)
            FROM doctor_patient_relationships
            WHERE doctor_id = ? AND patient_id = ?
        ";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([$patient->doctor_id, $patient->patient_id]);

        if ($checkStmt->fetchColumn() == 0) {
            // Crear la relación
            $insertSql = "
                INSERT INTO doctor_patient_relationships
                (doctor_id, patient_id, clinic_id, relationship_type, started_at, status, notes, permissions, created_at, updated_at)
                VALUES (?, ?, ?, 'primary', ?, 'active', 'Relación creada automáticamente - doctor creó al paciente', ?, NOW(), NOW())
            ";

            $permissions = json_encode(['view_history', 'prescribe', 'update_records', 'schedule_appointments', 'emergency_access']);

            $insertStmt = $pdo->prepare($insertSql);
            $insertStmt->execute([
                $patient->doctor_id,
                $patient->patient_id,
                $patient->clinic_id,
                $patient->created_at,
                $permissions
            ]);

            $created++;
        }
    }

    echo "   ✅ Creadas $created relaciones desde creadores de pacientes\n";
}

/**
 * Crear relaciones basadas en citas existentes
 */
function createRelationshipsFromAppointments($pdo) {
    echo "\n🔄 Creando relaciones basadas en citas existentes...\n";

    // Obtener pares únicos doctor-paciente de citas
    $sql = "
        SELECT
            d.id as doctor_id,
            p.id as patient_id,
            MIN(a.date_time) as first_appointment,
            a.clinic_id
        FROM appointments a
        JOIN doctors d ON a.doctor_id = d.id
        JOIN patients p ON a.patient_id = p.id
        GROUP BY d.id, p.id, a.clinic_id
    ";

    $stmt = $pdo->query($sql);
    $pairs = $stmt->fetchAll(PDO::FETCH_OBJ);
    $created = 0;

    foreach ($pairs as $pair) {
        // Verificar si ya existe la relación
        $checkSql = "
            SELECT COUNT(*)
            FROM doctor_patient_relationships
            WHERE doctor_id = ? AND patient_id = ?
        ";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([$pair->doctor_id, $pair->patient_id]);

        if ($checkStmt->fetchColumn() == 0) {
            // Crear la relación
            $insertSql = "
                INSERT INTO doctor_patient_relationships
                (doctor_id, patient_id, clinic_id, relationship_type, started_at, status, notes, permissions, created_at, updated_at)
                VALUES (?, ?, ?, 'consulting', ?, 'active', 'Relación creada automáticamente - basada en citas existentes', ?, NOW(), NOW())
            ";

            $permissions = json_encode(['view_history', 'prescribe', 'update_records']);
            $startDate = date('Y-m-d', strtotime($pair->first_appointment));

            $insertStmt = $pdo->prepare($insertSql);
            $insertStmt->execute([
                $pair->doctor_id,
                $pair->patient_id,
                $pair->clinic_id,
                $startDate,
                $permissions
            ]);

            $created++;
        }
    }

    echo "   ✅ Creadas $created relaciones desde citas\n";
}

/**
 * Crear relaciones basadas en tratamientos existentes
 */
function createRelationshipsFromTreatments($pdo) {
    echo "\n🔄 Creando relaciones basadas en tratamientos existentes...\n";

    // Verificar si existe la tabla treatments
    $checkTableSql = "SHOW TABLES LIKE 'treatments'";
    $checkStmt = $pdo->query($checkTableSql);

    if ($checkStmt->rowCount() == 0) {
        echo "   ⏭️  Tabla 'treatments' no existe, saltando este paso\n";
        return;
    }

    // Obtener pares únicos doctor-paciente de tratamientos
    $sql = "
        SELECT
            d.id as doctor_id,
            p.id as patient_id,
            MIN(t.start_date) as first_treatment,
            t.clinic_id
        FROM treatments t
        JOIN doctors d ON t.doctor_id = d.id
        JOIN patients p ON t.patient_id = p.id
        GROUP BY d.id, p.id, t.clinic_id
    ";

    $stmt = $pdo->query($sql);
    $pairs = $stmt->fetchAll(PDO::FETCH_OBJ);
    $created = 0;

    foreach ($pairs as $pair) {
        // Verificar si ya existe la relación
        $checkSql = "
            SELECT COUNT(*)
            FROM doctor_patient_relationships
            WHERE doctor_id = ? AND patient_id = ?
        ";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([$pair->doctor_id, $pair->patient_id]);

        if ($checkStmt->fetchColumn() == 0) {
            // Crear la relación
            $insertSql = "
                INSERT INTO doctor_patient_relationships
                (doctor_id, patient_id, clinic_id, relationship_type, started_at, status, notes, permissions, created_at, updated_at)
                VALUES (?, ?, ?, 'consulting', ?, 'active', 'Relación creada automáticamente - basada en tratamientos existentes', ?, NOW(), NOW())
            ";

            $permissions = json_encode(['view_history', 'prescribe', 'update_records']);

            $insertStmt = $pdo->prepare($insertSql);
            $insertStmt->execute([
                $pair->doctor_id,
                $pair->patient_id,
                $pair->clinic_id,
                $pair->first_treatment,
                $permissions
            ]);

            $created++;
        }
    }

    echo "   ✅ Creadas $created relaciones desde tratamientos\n";
}

?>
