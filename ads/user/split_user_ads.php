<?php
if (!isset($userAds)) {
    throw new RuntimeException('split_user_ads.php: $userAds not set');
}

$buyAds  = array_filter($userAds, fn($a) => $a['type'] === 'buy');
$sellAds = array_filter($userAds, fn($a) => $a['type'] === 'sell');
