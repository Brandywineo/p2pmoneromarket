<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../includes/auth.php';
require_login();

require_once __DIR__ . '/../db/database.php';
require_once __DIR__ . '/lib/ad_validator.php';
require_once __DIR__ . '/lib/ad_creator.php';

$type = $_POST['type'] ?? '';

try {

    if ($type === 'buy') {
        create_ad($_POST, $pdo, 'buy');

    } elseif ($type === 'sell') {
        create_ad($_POST, $pdo, 'sell');

    } else {
        throw new Exception('Invalid ad type');
    }

    header('Location: /dashboard.php');
    exit;

} catch (Exception $e) {
    http_response_code(400);
    exit($e->getMessage());
}
