<?php
declare(strict_types=1);

/* ===============================
   SAFETY GUARD
   =============================== */
if (!isset($ads) || !is_array($ads)) {
    $ads = [];
    return;
}

$coinFilter   = $_GET['coin']   ?? null;
$amountFilter = (float)($_GET['amount'] ?? 0);
$spendFilter  = (float)($_GET['spend'] ?? 0);
$sortMode     = $_GET['sort']   ?? 'cheap';

/* ===============================
   FILTER ADS
   =============================== */
$ads = array_filter($ads, function ($ad) use ($coinFilter, $amountFilter) {

    if ($coinFilter && strtolower($ad['crypto_pay']) !== strtolower($coinFilter)) {
        return false;
    }

    if ($amountFilter > 0) {
        if ($amountFilter < (float)$ad['min_xmr'] || $amountFilter > (float)$ad['max_xmr']) {
            return false;
        }
    }

    return true;
});

/* ===============================
   SORT ADS
   =============================== */
usort($ads, function ($a, $b) use ($sortMode) {

    if ($a['online'] !== $b['online']) {
        return $a['online'] ? -1 : 1;
    }

    if ($a['rating'] !== $b['rating']) {
        return $b['rating'] <=> $a['rating'];
    }

    if ($sortMode === 'expensive') {
        return $b['price_per_xmr'] <=> $a['price_per_xmr'];
    }

    return $a['price_per_xmr'] <=> $b['price_per_xmr'];
});
