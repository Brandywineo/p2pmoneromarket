<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../db/database.php';

require_login();
$userId = (int)$_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT address, created_at
    FROM subaddresses
    WHERE user_id = ?
    ORDER BY id DESC
");
$stmt->execute([$userId]);
$addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Your Subaddresses</title>
<link rel="stylesheet" href="/assets/dashboard.css">
</head>
<body>

<h1>Your Deposit Addresses</h1>

<button id="generateSubaddress">Generate New</button>

<ul class="address-list">
<?php foreach ($addresses as $a): ?>
    <li title="<?= htmlspecialchars($a['address']) ?>">
        <code><?= $a['address'] ?></code>
        <button onclick="copyText('<?= $a['address'] ?>')">Copy</button>
    </li>
<?php endforeach; ?>
</ul>

<script src="/assets/wallet.js"></script>
</body>
</html>
