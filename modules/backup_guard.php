<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Backup Completion Guard
|--------------------------------------------------------------------------
| - Requires logged-in user
| - Ensures PGP backup is completed
| - Redirects to dashboard if not
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../db/database.php';
require_once __DIR__ . '/../includes/user.php';

/* Must be logged in */
require_login();

/* Load user */
$user = get_current_user_data($_SESSION['user_id'], $pdo);

if (!$user) {
    session_destroy();
    header('Location: /login.php');
    exit;
}

/* Backup must be completed */
if ((int)$user['backup_completed'] !== 1) {
    header('Location: /dashboard.php');
    exit;
}
