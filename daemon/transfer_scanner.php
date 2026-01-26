<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

echo "[scanner] transfer scanner active\n";

$data = rpc([
    'jsonrpc' => '2.0',
    'id'      => 'scan',
    'method'  => 'get_transfers',
    'params'  => [
        'in' => true
    ]
]);

$incoming = $data['result']['in'] ?? [];

foreach ($incoming as $tx) {

    // HARD FILTERS (CRITICAL)
    if (($tx['type'] ?? '') !== 'in') continue;
    if (($tx['amount'] ?? 0) <= 0) continue;
    if (!isset($tx['subaddr_index']['minor'])) continue;

    $minorIndex = (int)$tx['subaddr_index']['minor'];

    // Ignore wallet primary address & change
    if ($minorIndex === 0) continue;

    $txid   = $tx['txid'];
    $amount = $tx['amount'] / 1e12;
    $height = $tx['height'];

    // Skip if already recorded
    $exists = $pdo->prepare(
        "SELECT 1 FROM deposits WHERE txid = ? LIMIT 1"
    );
    $exists->execute([$txid]);
    if ($exists->fetch()) continue;

    // Resolve subaddress ownership
    $stmt = $pdo->prepare("
        SELECT id, user_id
        FROM subaddresses
        WHERE index_no = ?
        LIMIT 1
    ");
    $stmt->execute([$minorIndex]);
    $sub = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$sub) continue;

    // Insert deposit as pending
    $pdo->prepare("
        INSERT INTO deposits
            (user_id, subaddress_id, txid, amount, confirmations, height, status, credited)
        VALUES (?, ?, ?, ?, 0, ?, 'pending', 0)
    ")->execute([
        $sub['user_id'],
        $sub['id'],
        $txid,
        $amount,
        $height
    ]);

    echo "[scanner] new deposit {$txid} ({$amount} XMR)\n";
}
