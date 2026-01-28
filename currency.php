<?php

$url = "https://api.coingecko.com/api/v3/simple/price?ids=monero&vs_currencies=usd,btc,eth,ltc,bch,bnb,eos,xrp,xlm,link,dot,yfi,sol";

// Init cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_USERAGENT, "XMR-Price-Script");

// Execute
$response = curl_exec($ch);

// Error handling
if ($response === false) {
    http_response_code(500);
    echo "cURL error: " . curl_error($ch);
    curl_close($ch);
    exit;
}

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    http_response_code(500);
    echo "HTTP error: $httpCode";
    exit;
}

// Decode JSON
$data = json_decode($response, true);

if (!isset($data['monero'])) {
    http_response_code(500);
    echo "Invalid API response";
    exit;
}

// Output
header("Content-Type: text/plain");

echo "Live XMR Crypto Prices (CoinGecko)\n";
echo "---------------------------------\n";

foreach ($data['monero'] as $currency => $price) {
    echo "XMR/" . strtoupper($currency) . " : $price\n";
}
