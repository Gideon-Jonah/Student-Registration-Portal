<?php
// process.php - Validate and insert student record
require_once __DIR__ . '/database.php';

function redirect_with($status, $message) {
    $qs = http_build_query(['status' => $status, 'message' => $message]);
    header("Location: index.html?{$qs}");
    exit;
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method Not Allowed';
    exit;
}

$full_name = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$department = trim($_POST['department'] ?? '');
$matric_number = trim($_POST['matric_number'] ?? '');

// Validate required fields
if ($full_name === '' || $email === '' || $department === '' || $matric_number === '') {
    redirect_with('error', 'All fields are required.');
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    redirect_with('error', 'Invalid email format.');
}

try {
    // Prevent duplicates: check existing email or matric number
    $stmt = $pdo->prepare('SELECT COUNT(*) as cnt FROM student_records WHERE email = :email OR matric_number = :matric');
    $stmt->execute([':email' => $email, ':matric' => $matric_number]);
    $exists = (int)$stmt->fetchColumn();
    if ($exists > 0) {
        redirect_with('error', 'Duplicate email or matric number.');
    }

    // Insert record
    $stmt = $pdo->prepare('INSERT INTO student_records (full_name, email, department, matric_number) VALUES (:full_name, :email, :department, :matric)');
    $stmt->execute([
        ':full_name' => $full_name,
        ':email' => $email,
        ':department' => $department,
        ':matric' => $matric_number,
    ]);

    redirect_with('success', 'Student registered successfully.');
} catch (PDOException $e) {
    // Handle unique constraint errors gracefully
    if ((int)$e->getCode() === 23000) {
        redirect_with('error', 'Duplicate email or matric number.');
    }
    redirect_with('error', 'Database error: ' . $e->getMessage());
}