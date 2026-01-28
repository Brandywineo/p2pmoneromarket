<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/db/database.php';

/* ===============================
   Security config
   =============================== */

define('MAX_REGISTER_ATTEMPTS', 3);
define('REGISTER_COOLDOWN', 600); // 10 minutes

/* ===============================
   Helpers
   =============================== */

function client_ip(): string {
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/* ===============================
   Rate limiting
   =============================== */

$ip = client_ip();

$_SESSION['register_attempts'] ??= [];

$attempt = $_SESSION['register_attempts'][$ip] ?? [
    'count' => 0,
    'time'  => time()
];

$error = null;

if (
    $attempt['count'] >= MAX_REGISTER_ATTEMPTS &&
    (time() - $attempt['time']) < REGISTER_COOLDOWN
) {
    $error = "Registration temporarily locked. Try again later.";
}

/* ===============================
   CSRF token
   =============================== */

$_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));

/* ===============================
   Handle POST
   =============================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {

    /* Honeypot (bots will fill it) */
    if (!empty($_POST['website'] ?? '')) {
        http_response_code(400);
        exit;
    }

    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf'] ?? '')) {
        $error = "Invalid request";
    } else {

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';

        /* Validation */
        if (
            strlen($username) < 5 ||
            strlen($username) > 32 ||
            !preg_match('/^[a-zA-Z0-9_-]+$/', $username)
        ) {
            $error = "Invalid username format";
        } elseif (
            strlen($password) < 7 ||
            !preg_match('/[a-z]/', $password) ||
            !preg_match('/[A-Z]/', $password) ||
            !preg_match('/\d/', $password) ||
            !preg_match('/[\W_]/', $password)
        ) {
            $error = "Password too weak";
        } elseif ($password !== $confirm) {
            $error = "Passwords do not match";
        } else {

            try {
                /* Check username */
                $stmt = $pdo->prepare(
                    "SELECT 1 FROM users WHERE username = ? LIMIT 1"
                );
                $stmt->execute([$username]);

                if ($stmt->fetch()) {
                    $error = "Username unavailable";
                } else {

                    /* Create user */
                    $hash = password_hash($password, PASSWORD_ARGON2ID);

                    $stmt = $pdo->prepare(
                        "INSERT INTO users (username, password_hash)
                         VALUES (?, ?)"
                    );
                    $stmt->execute([$username, $hash]);

                    session_regenerate_id(true);
                    unset($_SESSION['register_attempts']);

                    $_SESSION['user_id']  = (int)$pdo->lastInsertId();
                    $_SESSION['username'] = $username;

                    header("Location: index.php");
                    exit;
                }

            } catch (PDOException $e) {
                error_log("Register DB error: ".$e->getMessage());
                $error = "Registration unavailable";
            }
        }
    }

    /* Record failed attempt */
    $_SESSION['register_attempts'][$ip] = [
        'count' => $attempt['count'] + 1,
        'time'  => time()
    ];
}

/* Random username suggestion */
$rand_user = 'anon-' . substr(bin2hex(random_bytes(3)), 0, 6);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register | MoneroMarket</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
body {
    margin:0;
    background:#0b0b0b;
    color:#e0e0e0;
    font-family:system-ui,sans-serif;
}
.container {
    max-width:420px;
    margin:80px auto;
    padding:30px;
    background:#121212;
    border-radius:10px;
}
h1 {
    text-align:center;
    margin-bottom:20px;
}
label {
    font-size:0.85rem;
    margin-bottom:6px;
    display:block;
}
.field {
    position:relative;
}
input {
    width:100%;
    padding:12px;
    margin-bottom:18px;
    background:#1d1d1d;
    border:none;
    border-radius:6px;
    color:#fff;
}
input:focus {
    outline:1px solid #ff6600;
}
.toggle {
    position:absolute;
    right:10px;
    top:36px;
    font-size:0.75rem;
    cursor:pointer;
    color:#aaa;
}
button {
    width:100%;
    padding:12px;
    background:#ff6600;
    border:none;
    border-radius:6px;
    font-weight:600;
    cursor:pointer;
}
button:disabled {
    opacity:.5;
    cursor:not-allowed;
}
.error {
    background:#2a1414;
    border:1px solid #ff5252;
    color:#ffb3b3;
    padding:10px;
    border-radius:6px;
    font-size:0.85rem;
    margin-bottom:16px;
    text-align:center;
}
#username-status {
    font-size:0.75rem;
    margin-top:-12px;
    margin-bottom:12px;
}
.links {
    font-size:0.8rem;
    text-align:center;
    margin-top:10px;
}
.links a {
    color:#ff6600;
    text-decoration:none;
}
.note {
    font-size:0.75rem;
    color:#888;
    text-align:center;
    margin-top:15px;
}
/* Honeypot */
.hp { display:none; }
</style>
</head>

<body>
<div class="container">

<h1>Create Account</h1>

<?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST" novalidate>

<input type="hidden" name="csrf"
       value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

<input type="text" name="website" class="hp" tabindex="-1" autocomplete="off">

<label>Username</label>
<input type="text"
       id="username"
       name="username"
       required minlength="5" maxlength="32"
       placeholder="<?= htmlspecialchars($rand_user) ?>"
       autocomplete="off">

<div id="username-status"></div>

<label>Password</label>
<div class="field">
    <input type="password" id="password" name="password" required minlength="7">
    <span class="toggle" data-target="password">view</span>
</div>

<label>Confirm Password</label>
<div class="field">
    <input type="password" id="confirm" name="confirm_password" required minlength="7">
    <span class="toggle" data-target="confirm">view</span>
</div>

<button id="submit" type="submit" disabled>Register</button>
</form>

<div class="links">
    Already have an account? <a href="login.php">Login</a>
</div>

<div class="note">
    Password recovery & PGP setup happens after login.
</div>

</div>

<script>
(() => {
    const username = document.getElementById('username');
    const status   = document.getElementById('username-status');
    const submit   = document.getElementById('submit');
    let timer, available = false;

    function setStatus(msg, ok) {
        status.textContent = msg;
        status.style.color = ok ? '#6aff6a' : '#ff7777';
        available = ok;
        submit.disabled = !available;
    }

    username.addEventListener('input', () => {
        clearTimeout(timer);
        status.textContent = '';
        submit.disabled = true;
        available = false;

        const val = username.value.trim();
        if (val.length < 5) return;

        timer = setTimeout(() => {
            fetch('/ajax/check_username.php', {
                method: 'POST',
                headers: {'Content-Type':'application/x-www-form-urlencoded'},
                body: 'username=' + encodeURIComponent(val)
            })
            .then(r => r.json())
            .then(d => d && d.message && setStatus(d.message, d.ok))
            .catch(() => {});
        }, 400);
    });

    document.querySelectorAll('.toggle').forEach(el => {
        el.addEventListener('click', () => {
            const input = document.getElementById(el.dataset.target);
            input.type = input.type === 'password' ? 'text' : 'password';
            el.textContent = input.type === 'password' ? 'view' : 'hide';
        });
    });
})();
</script>

</body>
</html>
