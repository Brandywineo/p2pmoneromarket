<?php
declare(strict_types=1);

session_start();
$is_logged_in = isset($_SESSION['user_id']);

require_once __DIR__ . '/ads/fetch_listings.php';
require_once __DIR__ . '/ads/apply_filters.php';

/* Trade permission */
$user_can_trade = false;
if ($is_logged_in) {
    require_once __DIR__ . '/db/database.php';
    $stmt = $pdo->prepare("
        SELECT pgp_public, backup_completed
        FROM users WHERE id = :id
    ");
    $stmt->execute([':id' => $_SESSION['user_id']]);
    $u = $stmt->fetch(PDO::FETCH_ASSOC);
    $user_can_trade = !empty($u['pgp_public']) && (int)$u['backup_completed'] === 1;
}

/* ===============================
   Split ads (FIXED LOGIC)
   Viewer intent:
   - Buy XMR tab  → show SELL ads
   - Sell XMR tab → show BUY ads
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
.tabs {
    display: flex;
    max-width: 900px;
    margin: 16px auto;
}
.tab-btn {
    flex: 1;
    padding: 12px;
    background: var(--bg-input);
    border: none;
    color: var(--text-main);
    cursor: pointer;
}
.tab-btn.active {
    background: var(--accent);
    color: #000;
    font-weight: 600;
}
.listings {
    max-width: 900px;
    margin: auto;
}
.ad-card {
    background: var(--bg-card);
    border-radius: var(--radius-md);
    padding: 16px;
    margin-bottom: 14px;
}
.ad-header {
    font-weight: 600;
}
.ad-meta {
    font-size: 0.8rem;
    color: var(--text-muted);
    margin-top: 4px;
}

/* TRADE BUTTON LAYOUT */
.trade-action {
    margin-top: 14px;
}
.trade-action a {
    display: block;
    width: 100%;
    text-align: center;
}
@media (min-width: 640px) {
    .trade-action a {
        width: auto;
        display: inline-block;
    }
}
.trade-muted {
    font-size: 0.75rem;
    color: var(--text-dim);
}
</style>

<script>
function showTab(type) {
    document.getElementById('buyTab').style.display  = type === 'buy' ? 'block' : 'none';
    document.getElementById('sellTab').style.display = type === 'sell' ? 'block' : 'none';
    document.getElementById('btnBuy').classList.toggle('active', type === 'buy');
    document.getElementById('btnSell').classList.toggle('active', type === 'sell');
}
</script>
</head>
<body>

<?php require __DIR__ . '/assets/header.php'; ?>
<?php require __DIR__ . '/assets/filters.php'; ?>

<div class="tabs">
    <button id="btnBuy" class="tab-btn active" onclick="showTab('buy')">Buy XMR</button>
    <button id="btnSell" class="tab-btn" onclick="showTab('sell')">Sell XMR</button>
</div>

<div class="listings">
    <div id="buyTab">
        <?php foreach ($buyAds as $ad): ?>
            <?php require __DIR__ . '/ads/ad_card.php'; ?>
        <?php endforeach; ?>
    </div>

    <div id="sellTab" style="display:none;">
        <?php foreach ($sellAds as $ad): ?>
            <?php require __DIR__ . '/ads/ad_card.php'; ?>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>
