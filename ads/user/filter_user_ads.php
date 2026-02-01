<?php
if (!isset($ads, $userId)) {
    throw new RuntimeException('filter_user_ads.php: missing dependencies');
}

$userAds = array_filter(
    $ads,
    fn($a) => (int)$a['user_id'] === (int)$userId
);
