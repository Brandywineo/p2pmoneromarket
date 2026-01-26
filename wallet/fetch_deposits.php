<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../db/database.php';
require_login();

$userId = $_SESSION['user_id'];

// Required confirmations constant
$REQUIRED_CONFIRMATIONS = 10;

$stmt = $pdo->prepare("
    SELECT amount, confirmations, credited, unlock_height
    FROM deposits
    WHERE user_id = ?
    ORDER BY created_at DESC
");
$stmt->execute([$userId]);
$deposits = $stmt->fetchAll(PDO::FETCH_ASSOC);

$data = array_map(function($dep) use ($REQUIRED_CONFIRMATIONS) {
    $status = 'pending';
    $blocksLeft = max(0, $REQUIRED_CONFIRMATIONS - $dep['confirmations']);
    if ($dep['confirmations'] === 0) {
        $status = 'pending';
    } elseif ($dep['confirmations'] < $REQUIRED_CONFIRMATIONS) {
        $status = 'locked';
    } elseif ($dep['credited']) {
        $status = 'confirmed';
    }
    return [
        'amount' => $dep['amount'],
        'confirmations' => (int)$dep['confirmations'],
        'required' => $REQUIRED_CONFIRMATIONS,
        'blocks_left' => $blocksLeft,
        'status' => $status
    ];
}, $deposits);

header('Content-Type: application/json');
echo json_encode($data);
