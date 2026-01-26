<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../db/database.php';

require_login();
$userId = (int)$_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT address
    FROM subaddresses
    WHERE user_id = ?
    ORDER BY id DESC
    LIMIT 1
");
$stmt->execute([$userId]);

echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
