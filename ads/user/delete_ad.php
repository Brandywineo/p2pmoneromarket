<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../includes/auth.php';
require_login();
require_once __DIR__ . '/../../db/database.php';

$id = (int)($_POST['id'] ?? 0);
$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    DELETE FROM ads
    WHERE id = :id AND user_id = :uid
");
$stmt->execute([
    ':id'  => $id,
    ':uid' => $userId
]);

echo json_encode(['ok' => true]);
