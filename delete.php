<?php
// delete.php - Handle deletion and redirect back to view.php
require_once __DIR__ . '/database.php';

function back_to_view($status = null, $message = null) {
    $location = 'view.php';
    if ($status && $message) {
        $qs = http_build_query(['status' => $status, 'message' => $message]);
        $location .= '?' . $qs;
    }
    header('Location: ' . $location);
    exit;
}


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method Not Allowed';
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
    back_to_view('error', 'Invalid record ID.');
}

try {
    $stmt = $pdo->prepare('DELETE FROM student_records WHERE id = :id');
    $stmt->execute([':id' => $id]);
    back_to_view('success', 'Record deleted.');
} catch (PDOException $e) {
    back_to_view('error', 'Database error: ' . $e->getMessage());
}