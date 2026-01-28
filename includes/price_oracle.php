<?php
declare(strict_types=1);

/* ===============================
   Kraken XMR Price Oracle
   =============================== */

$cacheFile = __DIR__ . '/../cache/xmr_prices.json';
$cacheTTL  = 60; // 5 minutes

// Serve cache if valid
if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTTL) {
    return json_decode(file_get_contents($cacheFile), true);
}

/* ===============================
   Kraken API
   =============================== */
$url = "https://api.kraken.com/0/public/Ticker?pair="
     . "XXMRZUSD,XXBTZUSD,XETHZUSD,XLTCZUSD,BCHUSD,"
     . "XXRPZUSD,XXLMZUSD,LINKUSD,DOTUSD,YFIUSD,SOLUSD,USDTZUSD";

$response = @file_get_contents($url);
if ($response === false) {
    return [];
}

$data = json_decode($response, true);
if (!isset($data['result']['XXMRZUSD'])) {
    return [];
}

$xmr_usd = (float) $data['result']['XXMRZUSD']['c'][0];

$pairs = [
    'btc'  => 'XXBTZUSD',
    'eth'  => 'XETHZUSD',
    'ltc'  => 'XLTCZUSD',
    'bch'  => 'BCHUSD',
    'xrp'  => 'XXRPZUSD',
    'xlm'  => 'XXLMZUSD',
    'link' => 'LINKUSD',
    'dot'  => 'DOTUSD',
    'yfi'  => 'YFIUSD',
    'sol'  => 'SOLUSD',
    'usdt' => 'USDTZUSD'
];

$prices = [];

foreach ($pairs as $coin => $pair) {
    if (!isset($data['result'][$pair])) continue;

    $usd = (float) $data['result'][$pair]['c'][0];
    if ($usd <= 0) continue;

    $prices[$coin] = round($xmr_usd / $usd, 8);
}

// Save cache
if (!is_dir(dirname($cacheFile))) {
    mkdir(dirname($cacheFile), 0755, true);
}
file_put_contents($cacheFile, json_encode($prices));

return $prices;
