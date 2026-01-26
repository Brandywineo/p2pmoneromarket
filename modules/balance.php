<?php
declare(strict_types=1);

/**
 * Ledger-based Balance Module
 * ---------------------------
 * - Available balance from balance_ledger
 * - Pending deposits with confirmation progress
 */

$userId = (int)$_SESSION['user_id'];

/* -----------------------------
 * Available balance (ledger)
 * ----------------------------- */
$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(
        CASE direction
            WHEN 'credit' THEN amount
            WHEN 'debit'  THEN -amount
        END
    ), 0) AS balance
    FROM balance_ledger
    WHERE user_id = ?
");
$stmt->execute([$userId]);
$availableBalance = (float)$stmt->fetchColumn();

/* -----------------------------
 * Pending deposits
 * ----------------------------- */
$stmt = $pdo->prepare("
    SELECT
        amount,
        confirmations,
        unlock_height,
        height
    FROM deposits
    WHERE user_id = ?
      AND credited = 0
    ORDER BY created_at DESC
");
$stmt->execute([$userId]);
$pendingDeposits = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="balance-box">

    <h2>Balance</h2>

    <div class="balance-amount">
        <span>Available</span>
        <strong><?= number_format($availableBalance, 12) ?> XMR</strong>
    </div>

    <?php if ($pendingDeposits): ?>
        <div class="pending-deposits">
            <h3>Pending Deposits</h3>

            <?php foreach ($pendingDeposits as $dep): 
                $confirmations = (int)$dep['confirmations'];
                $unlockHeight  = (int)$dep['unlock_height'];
                $height        = (int)$dep['height'];

                $required = 10; // must match daemon
                $percent  = min(100, (int)(($confirmations / $required) * 100));
                $remaining = max(0, $unlockHeight - $height);
            ?>
                <div class="deposit-progress">

                    <div class="deposit-info">
                        <span><?= number_format((float)$dep['amount'], 12) ?> XMR</span>
                        <span><?= $confirmations ?> / <?= $required ?> confirmations</span>
                    </div>

                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?= $percent ?>%"></div>
                    </div>

                    <small>
                        <?= $remaining > 0
                            ? "Unlocking in {$remaining} blocks"
                            : "Unlocking soon" ?>
                    </small>

                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</section>
