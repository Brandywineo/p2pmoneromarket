<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

echo "[tracker] confirmation tracker active\n";

/**
 * Fetch uncredited deposits
 */
$stmt = $pdo->query("
    SELECT id, txid, user_id, amount
    FROM deposits
    WHERE credited = 0
");
$deposits = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($deposits as $dep) {
    $tx = rpc([
        'jsonrpc' => '2.0',
        'id'      => 'tx',
        'method'  => 'get_transfer_by_txid',
        'params'  => ['txid' => $dep['txid']]
    ]);

    if (!isset($tx['result']['transfer'])) {
        continue;
    }

    $t = $tx['result']['transfer'];

    $confirmations = (int)($t['confirmations'] ?? 0);
    $unlockHeight  = (int)($t['unlock_height'] ?? 0);
    $height        = (int)($t['height'] ?? 0);

    $blocksLeft = max(
        0,
        $unlockHeight > 0 ? $unlockHeight - $height : 0
    );

    if ($confirmations === 0) {
        $status = 'pending';
    } elseif ($confirmations < REQUIRED_CONFIRMATIONS) {
        $status = 'locked';
    } else {
        $status = 'confirmed';
    }

    /**
     * Update deposit state
     */
    $upd = $pdo->prepare("
        UPDATE deposits
        SET confirmations = ?,
            blocks_left = ?,
            status = ?
        WHERE id = ?
    ");
    $upd->execute([
        $confirmations,
        $blocksLeft,
        $status,
        $dep['id']
    ]);

    /**
     * If confirmed → credit ledger
     */
    if ($status !== 'confirmed') {
        continue;
    }

    $pdo->beginTransaction();

    try {
        /**
         * Get last balance
         */
        $balStmt = $pdo->prepare("
            SELECT balance_after
            FROM balance_ledger
            WHERE user_id = ?
            ORDER BY id DESC
            LIMIT 1
        ");
        $balStmt->execute([$dep['user_id']]);
        $lastBalance = (float)($balStmt->fetchColumn() ?? 0);

        $newBalance = $lastBalance + (float)$dep['amount'];

        /**
         * Insert ledger entry
         */
        $ins = $pdo->prepare("
            INSERT INTO balance_ledger
                (user_id, related_type, related_id, amount, direction, status, balance_after)
            VALUES
                (?, 'deposit', ?, ?, 'credit', 'unlocked', ?)
        ");
        $ins->execute([
            $dep['user_id'],
            $dep['id'],
            $dep['amount'],
            $newBalance
        ]);

        /**
         * Mark deposit credited
         */
        $pdo->prepare("
            UPDATE deposits
            SET credited = 1
            WHERE id = ?
        ")->execute([$dep['id']]);

        $pdo->commit();

    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}
