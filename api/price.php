<?php
declare(strict_types=1);

$coin = strtolower($_GET['coin'] ?? 'usdt');

/**
 * price_oracle.php RETURNS an array:
 * [
 *   'btc' => XMR/BTC,
 *   'eth' => XMR/ETH,
 *   ...
 * ]
 */
$prices = require __DIR__ . '/../includes/price_oracle.php';

if (!is_array($prices) || !isset($prices[$coin])) {
    http_response_code(404);
    echo json_encode(['error' => 'price_unavailable']);
    exit;
}

echo json_encode([
    'price' => (float) $prices[$coin]
]);
