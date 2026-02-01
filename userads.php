<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/modules/backup_guard.php';
require_once __DIR__ . '/ads/fetch_listings.php';

$userId = (int)$_SESSION['user_id'];

require_once __DIR__ . '/ads/user/filter_user_ads.php';
require_once __DIR__ . '/ads/user/split_user_ads.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Ads</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="/assets/global.css">
<script src="/assets/userads.js" defer></script>

<style>
.userads-header {
    max-width:900px;
    margin:16px auto;
    display:grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap:10px;
}
.userads-header .btn,
.userads-header .tab-btn {
    width:100%;
}

/* Modal styles */
.modal {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.65);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}
.modal.hidden {
    display: none;
}
.modal-card {
    max-width: 420px;
    width: 90%;
    padding: 24px;
    text-align: center;
}
.modal-actions {
    display: flex;
    justify-content: center;
    gap: 12px;
    margin-top: 20px;
}
</style>
</head>

<body>

<?php require __DIR__ . '/assets/header.php'; ?>

<!-- HEADER BUTTONS -->
<div class="userads-header">
    <button class="tab-btn active" id="btnBuy" onclick="showTab('buy')">
        Buy Ads
    </button>
    <button class="tab-btn" id="btnSell" onclick="showTab('sell')">
        Sell Ads
    </button>
    <a href="/ads/create.php" class="btn">
        ＋ Create Ad
    </a>
</div>

<!-- LISTINGS -->
<div class="listings">
    <div id="buyTab">
        <?php foreach ($buyAds as $ad): ?>
            <?php require __DIR__ . '/ads/user/ad_card_user.php'; ?>
        <?php endforeach; ?>
    </div>

    <div id="sellTab" style="display:none;">
        <?php foreach ($sellAds as $ad): ?>
            <?php require __DIR__ . '/ads/user/ad_card_user.php'; ?>
        <?php endforeach; ?>
    </div>
</div>

<!-- TAB SCRIPT -->
<script>
function showTab(type) {
    buyTab.style.display  = type === 'buy'  ? 'block' : 'none';
    sellTab.style.display = type === 'sell' ? 'block' : 'none';
    btnBuy.classList.toggle('active', type === 'buy');
    btnSell.classList.toggle('active', type === 'sell');
}
</script>

<!-- DELETE MODAL -->
<div id="deleteModal" class="modal hidden">
    <div class="card modal-card">
        <h3>Delete Ad</h3>
        <p>This ad will be permanently deleted.<br>This action cannot be undone.</p>

        <div class="modal-actions">
            <button class="btn danger" id="confirmDeleteBtn">
                Delete
            </button>
            <button class="btn" onclick="closeDeleteModal()">
                Cancel
            </button>
        </div>
    </div>
</div>

</body>
</html>
