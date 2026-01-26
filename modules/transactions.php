<?php
declare(strict_types=1);

$stmt = $pdo->prepare("
    SELECT txid, amount, confirmations, credited
    FROM deposits
    WHERE user_id = ?
    ORDER BY created_at DESC
    LIMIT 10
");
$stmt->execute([$_SESSION['user_id']]);
$rows = $stmt->fetchAll();
?>

<section class="card transactions">
    <h3>Recent Deposits</h3>

    <?php if (!$rows): ?>
        <p class="note">No deposits yet.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>TXID</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $tx): ?>
                <tr>
                    <td><?= htmlspecialchars(substr($tx['txid'], 0, 12)) ?>…</td>
                    <td><?= number_format((float)$tx['amount'], 12) ?> XMR</td>
                    <td>
                        <?php if ($tx['credited']): ?>
                            <span class="ok">Unlocked</span>
                        <?php else: ?>
                            <span class="pending">
                                <?= (int)$tx['confirmations'] ?> / 10 confirmations
                            </span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>
