<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/../includes/auth.php';
require_login();

require_once __DIR__ . '/../includes/csrf.php';
csrf_verify(); // ✅ correct function

require_once __DIR__ . '/../db/database.php';
require_once __DIR__ . '/lib/ad_updater.php';

$adId = (int)($_POST['id'] ?? 0);
$type = $_POST['type'] ?? '';

if ($adId <= 0 || !in_array($type, ['buy', 'sell'], true)) {
    http_response_code(400);
    exit('Invalid request');
}

try {
    update_ad($adId, $_POST, $pdo, $type);
    header('Location: /userads.php?updated=1');
    exit;
} catch (Throwable $e) {
    http_response_code(400);
    echo htmlspecialchars($e->getMessage());
}
