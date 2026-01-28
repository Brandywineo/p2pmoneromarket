<?php
// ads/apply_filters.php
declare(strict_types=1);

$coinFilter   = $_GET['coin']   ?? null;
$amountFilter = (float)($_GET['amount'] ?? 0);
$spendFilter  = (float)($_GET['spend'] ?? 0);
$sortMode     = $_GET['sort']   ?? 'cheap';

/* ===============================
   FILTER ADS
   =============================== */

$ads = array_filter($ads, function ($ad) use ($coinFilter, $amountFilter, $spendFilter) {

    if ($coinFilter && strtoupper($ad['crypto_pay']) !== strtoupper($coinFilter)) {
        return false;
    }

    if ($amountFilter > 0) {
        if ($amountFilter < $ad['min_xmr'] || $amountFilter > $ad['max_xmr']) {
            return false;
        }
    }

    // Spend filter reserved for price conversion
    return true;
});

/* ===============================
   SORT ADS
   Priority:
   1. Online
   2. Rating
   3. Margin
   =============================== */

usort($ads, function ($a, $b) use ($sortMode) {

    // Online first
    if ($a['online'] !== $b['online']) {
        return $a['online'] ? -1 : 1;
    }

    // Higher rating first
    if ($a['rating'] !== $b['rating']) {
        return $b['rating'] <=> $a['rating'];
    }

    // Margin
    if ($sortMode === 'expensive') {
        return $b['margin_percent'] <=> $a['margin_percent'];
    }

    return $a['margin_percent'] <=> $b['margin_percent'];
});
