<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/../includes/auth.php';
require_login();

require_once __DIR__ . '/../db/database.php';

$id = (int)($_POST['id'] ?? 0);
$userId = (int)$_SESSION['user_id'];

if ($id <= 0) {
    http_response_code(400);
    exit;
}

$stmt = $pdo->prepare("
    DELETE FROM listings
    WHERE id = :id AND user_id = :uid
");

$stmt->execute([
    ':id'  => $id,
    ':uid' => $userId
]);

http_response_code(204);
exit;
