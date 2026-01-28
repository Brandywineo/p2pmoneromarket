<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/../includes/auth.php';
require_login();

require_once __DIR__ . '/../db/database.php';

$user_id = $_SESSION['user_id'];

// Allowed trading coins (must match ENUM)
$coins = [
    'btc' => 'Bitcoin (BTC)',
    'eth' => 'Ethereum (ETH)',
    'ltc' => 'Litecoin (LTC)',
    'bch' => 'Bitcoin Cash (BCH)',
    'bnb' => 'Binance Coin (BNB)',
    'eos' => 'EOS',
    'xrp' => 'Ripple (XRP)',
    'xlm' => 'Stellar (XLM)',
    'link' => 'Chainlink (LINK)',
    'dot' => 'Polkadot (DOT)',
    'yfi' => 'Yearn Finance (YFI)',
    'sol' => 'Solana (SOL)',
    'usdt' => 'Tether (USDT)'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Ad</title>

    <link rel="stylesheet" href="/assets/global.css">
    <script src="/assets/app.js" defer></script>

    <style>
        /* Page-specific only — no color overrides */

        .tab-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .tab-buttons button {
            flex: 1;
        }

        .tab-buttons button.active {
            outline: 1px solid var(--accent);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .hint {
            font-size: 0.7rem;
            color: var(--text-muted);
            margin-top: -12px;
            margin-bottom: 14px;
        }
    </style>
</head>

<body>

<div class="container">

    <h1>Create P2P Ad</h1>

    <!-- Tabs -->
    <div class="tab-buttons">
        <button id="buyTab" class="active" onclick="switchTab('buy')">
            Buy XMR
        </button>
        <button id="sellTab" class="btn-ghost" onclick="switchTab('sell')">
            Sell XMR
        </button>
    </div>

    <!-- BUY AD -->
    <div id="buyForm" class="tab-content active card">
        <form method="post" action="create_submit.php">

            <input type="hidden" name="type" value="buy">

            <label>Minimum XMR to buy</label>
            <input type="number" step="0.00000001" name="min_xmr" required>

            <label>Maximum XMR to buy</label>
            <input type="number" step="0.00000001" name="max_xmr" required>

            <label>Pay using crypto</label>
            <select name="crypto_pay" required>
                <?php foreach ($coins as $key => $label): ?>
                    <option value="<?= htmlspecialchars($key) ?>">
                        <?= htmlspecialchars($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Price margin (%)</label>
            <input type="number" step="0.001" name="margin_percent" required>
            <div class="hint">
                Positive = above market, Negative = below market
            </div>

            <label>Payment time limit (minutes)</label>
            <input type="number" name="payment_time_limit" min="5" required>

            <label>Trade terms</label>
            <textarea name="terms" rows="4"
                placeholder="Payment instructions, conditions, etc."></textarea>

            <button type="submit">
                Create Buy Ad
            </button>
        </form>
    </div>

    <!-- SELL AD -->
    <div id="sellForm" class="tab-content card">
        <form method="post" action="create_submit.php">

            <input type="hidden" name="type" value="sell">

            <label>Minimum XMR to sell</label>
            <input type="number" step="0.00000001" name="min_xmr" required>

            <label>Maximum XMR to sell</label>
            <input type="number" step="0.00000001" name="max_xmr" required>

            <label>Receive payment in crypto</label>
            <select name="crypto_pay" required>
                <?php foreach ($coins as $key => $label): ?>
                    <option value="<?= htmlspecialchars($key) ?>">
                        <?= htmlspecialchars($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Price margin (%)</label>
            <input type="number" step="0.001" name="margin_percent" required>
            <div class="hint">
                Positive = above market, Negative = below market
            </div>

            <label>Payment time limit (minutes)</label>
            <input type="number" name="payment_time_limit" min="5" required>

            <label>Trade terms</label>
            <textarea name="terms" rows="4"
                placeholder="Payment instructions, conditions, etc."></textarea>

            <button type="submit">
                Create Sell Ad
            </button>
        </form>
    </div>

    <div class="note">
        Prices are calculated from live market rates when a trade begins.
    </div>

</div>

<script>
function switchTab(tab) {
    const buyTab = document.getElementById('buyTab');
    const sellTab = document.getElementById('sellTab');
    const buyForm = document.getElementById('buyForm');
    const sellForm = document.getElementById('sellForm');

    if (tab === 'buy') {
        buyTab.classList.add('active');
        buyTab.classList.remove('btn-ghost');

        sellTab.classList.remove('active');
        sellTab.classList.add('btn-ghost');

        buyForm.classList.add('active');
        sellForm.classList.remove('active');
    } else {
        sellTab.classList.add('active');
        sellTab.classList.remove('btn-ghost');

        buyTab.classList.remove('active');
        buyTab.classList.add('btn-ghost');

        sellForm.classList.add('active');
        buyForm.classList.remove('active');
    }
}
</script>

</body>
</html>
