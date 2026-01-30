<?php
declare(strict_types=1);

require_once __DIR__ . '/../db/database.php';

/**
 * Get ENUM values from a table column (MySQL-safe)
 */
function get_enum_values(string $table, string $column): array
{
    global $pdo;

    // Defensive whitelist: only allow valid identifier characters
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
        return [];
    }

    if (!preg_match('/^[a-zA-Z0-9_]+$/', $column)) {
        return [];
    }

    $sql = "SHOW COLUMNS FROM `$table` LIKE '$column'";
    $stmt = $pdo->query($sql);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row || !isset($row['Type'])) {
        return [];
    }

    if (!preg_match("/^enum\('(.*)'\)$/", $row['Type'], $matches)) {
        return [];
    }

    return explode("','", $matches[1]);
}

/**
 * listings.crypto_pay ENUM
 */
function get_crypto_pay_coins(): array
{
    return get_enum_values('listings', 'crypto_pay');
}
