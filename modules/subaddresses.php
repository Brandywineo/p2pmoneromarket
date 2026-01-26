<?php
// Fetch latest subaddress for dashboard
$stmt = $pdo->prepare("
    SELECT id, address
    FROM subaddresses
    WHERE user_id = ?
    ORDER BY created_at DESC
    LIMIT 1
");
$stmt->execute([$user['id']]);
$latest = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!-- Latest deposit address -->
<div class="balance-box">
    <h3>Your Latest Subaddress</h3>

    <?php if ($latest): ?>
        <p id="latest-addr"
           class="short-address"
           title="<?= htmlspecialchars($latest['address']) ?>">
            <?= htmlspecialchars(substr($latest['address'], 0, 14)) ?>…
        </p>
    <?php else: ?>
        <p id="latest-addr">No subaddress yet</p>
    <?php endif; ?>

    <button id="generate-new-subaddress">
        Generate New Subaddress
    </button>

    <a href="/wallet/addresses.php" class="view-all-link">
        View all deposit addresses →
    </a>
</div>

<!-- Incoming deposits / confirmations -->
<div class="balance-box">
    <h3>Incoming Deposits</h3>
    <div id="pending-deposits"></div>
</div>
