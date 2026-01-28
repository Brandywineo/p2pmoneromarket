<?php
session_start();
require_once __DIR__ . '/../db/database1.php';

header('Content-Type: application/json');

/* Basic hardening */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false]);
    exit;
}

$username = trim($_POST['username'] ?? '');

if (
    strlen($username) < 5 ||
    strlen($username) > 32 ||
    !preg_match('/^[a-zA-Z0-9_-]+$/', $username)
) {
    echo json_encode([
        'ok' => false,
        'message' => 'Invalid username format'
    ]);
    exit;
}

try {
    $stmt = $db->prepare("SELECT 1 FROM users WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);

    if ($stmt->fetch()) {
        echo json_encode([
            'ok' => false,
            'message' => 'Username unavailable'
        ]);
    } else {
        echo json_encode([
            'ok' => true,
            'message' => 'Username available'
        ]);
    }

} catch (PDOException $e) {
    error_log("Username check error: ".$e->getMessage());
    echo json_encode(['ok' => false]);
}
