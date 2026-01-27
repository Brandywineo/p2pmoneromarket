<?php
declare(strict_types=1);

$REQUIRED_CONFIRMATIONS = 10;

$stmt = $pdo->prepare("
    SELECT txid, amount, confirmations
    FROM deposits
    WHERE user_id = ?
    ORDER BY created_at DESC
    LIMIT 15
");
$stmt->execute([$_SESSION['user_id']]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// detect if there is an active incoming deposit
$has_pending = false;
foreach ($rows as $r) {
    if ((int)$r['confirmations'] > 0 && (int)$r['confirmations'] < $REQUIRED_CONFIRMATIONS) {
        $has_pending = true;
        break;
    }
}
?>

<section class="card transactions" id="tx-container">
    <h3>Recent Deposits</h3>

    <?php if (!$rows): ?>
        <p class="note">No deposits yet.</p>
    <?php else: ?>

        <?php if ($has_pending): ?>
            <div class="incoming-banner">
                Incoming deposit detected…
            </div>
        <?php endif; ?>

        <div class="tx-list">
            <?php foreach ($rows as $tx): ?>
                <div class="tx-row"
                     data-txid="<?= htmlspecialchars($tx['txid']) ?>"
                     onclick="copyTxid(this)">

                    <div class="tx-amount">
                        <?= number_format((float)$tx['amount'], 12) ?> XMR
                    </div>

                    <div class="tx-id">
                        <span class="tx-short">
                            <?= htmlspecialchars(substr($tx['txid'], 0, 10)) ?>…
                        </span>
                        <span class="tx-full">
                            <?= htmlspecialchars($tx['txid']) ?>
                        </span>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>
</section>
