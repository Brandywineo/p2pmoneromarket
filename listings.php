<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/ads/fetch_listings.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>P2P Listings</title>

<link rel="stylesheet" href="/assets/global.css">
<script src="/assets/app.js" defer></script>

<style>
/* ===============================
   Listings Page Styles
   =============================== */

.container {
    max-width: 680px;
}

.listing {
    background: var(--bg-input);
    border-radius: var(--radius-sm);
    padding: 14px;
    margin-bottom: 14px;
}

.listing-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 6px;
}

.listing-user {
    font-weight: 600;
}

.listing-type.buy {
    color: #3ad17b;
    font-size: 0.8rem;
    font-weight: 600;
}

.listing-type.sell {
    color: #ff6b6b;
    font-size: 0.8rem;
    font-weight: 600;
}

.listing-price {
    font-family: var(--font-mono);
    font-size: 0.95rem;
}

.listing-meta {
    font-size: 0.75rem;
    color: var(--text-muted);
    margin-top: 6px;
    display: flex;
    justify-content: space-between;
}

.listing button {
    margin-top: 10px;
}
</style>
</head>

<body>

<div class="container">
    <h1>P2P Monero Ads</h1>

    <?php if (empty($ads)): ?>
        <div class="card note">
            No ads available right now.
        </div>
    <?php endif; ?>

    <?php foreach ($ads as $ad): ?>
        <div class="listing">

            <div class="listing-header">
                <div class="listing-user">
                    <?= htmlspecialchars($ad['username']) ?>
                </div>

                <div class="listing-type <?= $ad['type'] ?>">
                    <?= strtoupper($ad['type']) ?> XMR
                </div>
            </div>

            <div class="listing-price">
                <?= number_format($ad['price'], 8) ?>
                <?= strtoupper($ad['crypto_pay']) ?> / XMR
            </div>

            <div class="listing-meta">
                <span>
                    <?= $ad['min_xmr'] ?> – <?= $ad['max_xmr'] ?> XMR
                </span>
                <span>
                    <?= $ad['margin_percent'] ?>% from market
                </span>
            </div>

            <div class="listing-meta">
                <span>
                    Time limit: <?= $ad['payment_time_limit'] ?> min
                </span>
                <span>
                    <?= date('Y-m-d', strtotime($ad['created_at'])) ?>
                </span>
            </div>

            <button>
                <?= $ad['type'] === 'buy' ? 'Sell XMR' : 'Buy XMR' ?>
            </button>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>
