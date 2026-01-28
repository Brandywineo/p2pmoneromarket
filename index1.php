<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/db/database.php';
require_once __DIR__ . '/ads/fetch_listings.php';

$is_logged_in = isset($_SESSION['user_id']);
$can_trade = false;

/* ===============================
   Check trading eligibility
   =============================== */
if ($is_logged_in) {
    $stmt = $pdo->prepare("
        SELECT pgp_public, backup_completed
        FROM users
        WHERE id = :id
        LIMIT 1
    ");
    $stmt->execute([':id' => $_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!empty($user['pgp_public']) && (int)$user['backup_completed'] === 1) {
        $can_trade = true;
    }
}

/* ===============================
   Split ads by type
   =============================== */
$buyAds  = array_filter($ads, fn($a) => $a['type'] === 'sell');
$sellAds = array_filter($ads, fn($a) => $a['type'] === 'buy');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>MoneroMarket</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="/assets/global.css">
<script src="/assets/app.js" defer></script>

<style>
/* ===============================
   Header
   =============================== */
header {
    background: var(--bg-card);
    padding: 14px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

header .brand {
    font-weight: 700;
    color: var(--accent);
}

header nav a {
    margin-left: 14px;
    font-size: 0.85rem;
}

/* ===============================
   Tabs
   =============================== */
.tabs {
    display: flex;
    gap: 8px;
    margin: 20px auto;
    max-width: 1100px;
    padding: 0 20px;
}

.tab {
    flex: 1;
    text-align: center;
    padding: 10px;
    background: var(--bg-input);
    border-radius: var(--radius-sm);
    cursor: pointer;
    font-weight: 600;
}

.tab.active {
    background: var(--accent);
    color: #000;
}

/* ===============================
   Listings
   =============================== */
.container {
    max-width: 1100px;
    margin: 0 auto 40px;
    padding: 0 20px;
}

.ad {
    background: var(--bg-card);
    border-radius: var(--radius-md);
    padding: 16px;
    margin-bottom: 14px;
}

.ad-header {
    display: flex;
    justify-content: space-between;
    font-size: 0.85rem;
    margin-bottom: 10px;
}

.price {
    font-weight: 700;
}

.meta {
    font-size: 0.75rem;
    color: var(--text-muted);
    margin-top: 6px;
}

.trade-btn {
    margin-top: 12px;
}

.trade-disabled {
    font-size: 0.75rem;
    color: var(--text-muted);
    margin-top: 12px;
}
</style>

<script>
function switchTab(tab) {
    document.getElementById('buyAds').style.display  = tab === 'buy'  ? 'block' : 'none';
    document.getElementById('sellAds').style.display = tab === 'sell' ? 'block' : 'none';

    document.getElementById('tabBuy').classList.toggle('active', tab === 'buy');
    document.getElementById('tabSell').classList.toggle('active', tab === 'sell');
}
</script>
</head>

<body>

<header>
    <div class="brand">MoneroMarket</div>
    <nav>
        <?php if ($is_logged_in): ?>
            <a href="/dashboard.php">Dashboard</a>
            <a href="/ads/create.php">My Ads</a>
            <a href="/logout.php">Logout</a>
        <?php else: ?>
            <a href="/login.html">Login</a>
            <a href="/register.html">Register</a>
        <?php endif; ?>
    </nav>
</header>

<div class="tabs">
    <div id="tabBuy" class="tab active" onclick="switchTab('buy')">Buy XMR</div>
    <div id="tabSell" class="tab" onclick="switchTab('sell')">Sell XMR</div>
</div>

<div class="container">

<!-- ===============================
     BUY ADS
     =============================== -->
<div id="buyAds">
<?php foreach ($buyAds as $ad): ?>
    <div class="ad">
        <div class="ad-header">
            <strong><?= htmlspecialchars($ad['username']) ?></strong>
            <span class="price"><?= $ad['price'] ?> <?= strtoupper($ad['crypto_pay']) ?></span>
        </div>

        <div class="meta">
            Limits: <?= $ad['min_xmr'] ?> – <?= $ad['max_xmr'] ?> XMR<br>
            Margin: <?= $ad['margin_percent'] ?>%
        </div>

        <?php if ($can_trade): ?>
            <a class="btn trade-btn" href="/trades/start.php?ad=<?= $ad['id'] ?>">Buy XMR</a>
        <?php else: ?>
            <div class="trade-disabled">
                Complete PGP backup to trade
            </div>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
</div>

<!-- ===============================
     SELL ADS
     =============================== -->
<div id="sellAds" style="display:none;">
<?php foreach ($sellAds as $ad): ?>
    <div class="ad">
        <div class="ad-header">
            <strong><?= htmlspecialchars($ad['username']) ?></strong>
            <span class="price"><?= $ad['price'] ?> <?= strtoupper($ad['crypto_pay']) ?></span>
        </div>

        <div class="meta">
            Limits: <?= $ad['min_xmr'] ?> – <?= $ad['max_xmr'] ?> XMR<br>
            Margin: <?= $ad['margin_percent'] ?>%
        </div>

        <?php if ($can_trade): ?>
            <a class="btn trade-btn" href="/trades/start.php?ad=<?= $ad['id'] ?>">Sell XMR</a>
        <?php else: ?>
            <div class="trade-disabled">
                Complete PGP backup to trade
            </div>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
</div>

</div>

</body>
</html>
