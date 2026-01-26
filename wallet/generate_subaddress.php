<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../db/database.php';

require_login();
$userId = (int)$_SESSION['user_id'];

/* Ask wallet RPC to create subaddress */
$rpc = [
    'jsonrpc' => '2.0',
    'id' => 'gen',
    'method' => 'create_address',
    'params' => ['account_index' => 0]
];

$ch = curl_init('http://127.0.0.1:18083/json_rpc');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_POSTFIELDS => json_encode($rpc),
]);
$res = json_decode(curl_exec($ch), true);

$address = $res['result']['address'];
$index   = $res['result']['address_index'];

$stmt = $pdo->prepare("
    INSERT INTO subaddresses (user_id, address, index_no)
    VALUES (?, ?, ?)
");
$stmt->execute([$userId, $address, $index]);

echo json_encode([
    'status' => 'ok',
    'address' => $address
]);
