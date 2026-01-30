<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/../includes/auth.php';
require_login();

require_once __DIR__ . '/../db/database.php';

/* Allowed coins (single source of truth) */
$coins = [
    'btc','eth','ltc','bch','xrp','xlm',
    'link','dot','yfi','sol','usdt'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Create Ad</title>

<link rel="stylesheet" href="/assets/global.css">
<script src="/assets/app.js" defer></script>
<script src="/assets/ad_preview.js" defer></script>
</head>

<body>
<div class="container">
    <h1>Create P2P Ad</h1>

    <?php include __DIR__ . '/partials/tabs.php'; ?>

    <div id="buyForm" class="tab-content active card">
        <?php include __DIR__ . '/partials/form_buy.php'; ?>
    </div>

    <div id="sellForm" class="tab-content card">
        <?php include __DIR__ . '/partials/form_sell.php'; ?>
    </div>

    <div class="note">
        Prices are calculated from live market rates when a trade begins.
    </div>
</div>
</body>
</html>
