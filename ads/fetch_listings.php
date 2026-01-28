<?php
declare(strict_types=1);

require_once __DIR__ . '/../db/database.php';

/* ===============================
   Load price oracle
   =============================== */
$prices = require __DIR__ . '/../includes/price_oracle.php';

if (empty($prices)) {
    exit('Price oracle unavailable.');
}

/* ===============================
   Allowed coins
   =============================== */
$allowedCoins = array_keys($prices);

/* ===============================
   Selected coin
   =============================== */
$crypto_pay = $_GET['crypto'] ?? 'usdt';

if (!in_array($crypto_pay, $allowedCoins, true)) {
    $crypto_pay = 'usdt';
}

$marketPrice = $prices[$crypto_pay];

/* ===============================
   Fetch listings
   =============================== */
$sql = "
    SELECT
        l.id,
        l.type,
        l.crypto_pay,
        l.margin_percent,
        l.min_xmr,
        l.max_xmr,
        l.payment_time_limit,
        l.terms,
        l.created_at,
        u.username
    FROM listings l
    JOIN users u ON u.id = l.user_id
    WHERE l.crypto_pay = :crypto
      AND l.status = 'active'
    ORDER BY l.created_at DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([':crypto' => $crypto_pay]);
$listings = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ===============================
   Calculate live price
   =============================== */
$ads = [];

foreach ($listings as $row) {
    $finalPrice = $marketPrice * (1 + ($row['margin_percent'] / 100));

    $ads[] = [
        'id' => (int)$row['id'],
        'type' => $row['type'],
        'username' => $row['username'],
        'crypto_pay' => $row['crypto_pay'],
        'price' => round($finalPrice, 8),
        'market_price' => $marketPrice,
        'margin_percent' => (float)$row['margin_percent'],
        'min_xmr' => (float)$row['min_xmr'],
        'max_xmr' => (float)$row['max_xmr'],
        'payment_time_limit' => (int)$row['payment_time_limit'],
        'terms' => $row['terms'],
        'created_at' => $row['created_at']
    ];
}
