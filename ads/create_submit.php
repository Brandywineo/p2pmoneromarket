<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../includes/auth.php';
require_login();

require_once __DIR__ . '/../db/database.php';

$user_id = (int) $_SESSION['user_id'];

/* ===============================
   Allowed values (MUST match ENUM)
   =============================== */
$allowedTypes = ['buy', 'sell'];
$allowedCoins = [
    'btc','eth','ltc','bch','bnb',
    'eos','xrp','xlm','link','dot','yfi','sol','usdt'
];

/* ===============================
   Input sanitization
   =============================== */
$type = $_POST['type'] ?? '';
$crypto_pay = $_POST['crypto_pay'] ?? '';
$margin_percent = $_POST['margin_percent'] ?? '';
$min_xmr = $_POST['min_xmr'] ?? '';
$max_xmr = $_POST['max_xmr'] ?? '';
$payment_time_limit = $_POST['payment_time_limit'] ?? '';
$terms = trim($_POST['terms'] ?? '');

/* ===============================
   Validation
   =============================== */
if (!in_array($type, $allowedTypes, true)) {
    exit('Invalid ad type.');
}

if (!in_array($crypto_pay, $allowedCoins, true)) {
    exit('Invalid payment coin.');
}

if (!is_numeric($margin_percent)) {
    exit('Invalid margin.');
}

$margin_percent = round((float) $margin_percent, 3);
if ($margin_percent < -50 || $margin_percent > 50) {
    exit('Margin out of allowed range.');
}

if (!is_numeric($min_xmr) || !is_numeric($max_xmr)) {
    exit('Invalid XMR amount.');
}

$min_xmr = (float) $min_xmr;
$max_xmr = (float) $max_xmr;

if ($min_xmr <= 0 || $max_xmr <= 0) {
    exit('XMR amount must be positive.');
}

if ($min_xmr > $max_xmr) {
    exit('Minimum XMR cannot exceed maximum.');
}

if (!ctype_digit((string)$payment_time_limit)) {
    exit('Invalid time limit.');
}

$payment_time_limit = (int) $payment_time_limit;
if ($payment_time_limit < 5 || $payment_time_limit > 720) {
    exit('Payment time limit out of range.');
}

/* ===============================
   Insert listing
   =============================== */
$sql = "
    INSERT INTO listings (
        user_id,
        type,
        crypto_pay,
        margin_percent,
        min_xmr,
        max_xmr,
        payment_time_limit,
        terms
    ) VALUES (
        :user_id,
        :type,
        :crypto_pay,
        :margin_percent,
        :min_xmr,
        :max_xmr,
        :payment_time_limit,
        :terms
    )
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':user_id' => $user_id,
    ':type' => $type,
    ':crypto_pay' => $crypto_pay,
    ':margin_percent' => $margin_percent,
    ':min_xmr' => $min_xmr,
    ':max_xmr' => $max_xmr,
    ':payment_time_limit' => $payment_time_limit,
    ':terms' => $terms
]);

/* ===============================
   Success → redirect
   =============================== */
header('Location: /dashboard.php');
exit;
