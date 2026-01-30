<?php
require_once __DIR__ . '/ad_validator.php';

function create_ad(array $data, PDO $pdo, string $type): void {
    $user_id = (int) $_SESSION['user_id'];
    $v = validate_ad($data);

    $stmt = $pdo->prepare("
        INSERT INTO listings
        (user_id, type, crypto_pay, margin_percent, min_xmr, max_xmr, payment_time_limit, terms)
        VALUES
        (:uid, :type, :coin, :margin, :min, :max, :time, :terms)
    ");

    $stmt->execute([
        ':uid' => $user_id,
        ':type' => $type,
        ':coin' => $v['crypto_pay'],
        ':margin' => $v['margin_percent'],
        ':min' => $v['min_xmr'],
        ':max' => $v['max_xmr'],
        ':time' => $v['payment_time_limit'],
        ':terms' => $v['terms']
    ]);
}
