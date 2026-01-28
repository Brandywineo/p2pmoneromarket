<?php
$coin   = $_GET['coin']   ?? '';
$amount = $_GET['amount'] ?? '';
$sort   = $_GET['sort']   ?? '';
?>

<div class="filters" style="
    max-width:900px;
    margin:12px auto;
    padding:12px;
    background:var(--bg-card);
    border-radius:var(--radius-md);
">
    <form method="get"
          style="
            width:100%;
            display:grid;
            gap:10px;
            align-items:center;
            grid-template-columns:
                1fr
                1fr
                1fr
                auto;
          ">

        <!-- Coin -->
        <select name="coin">
            <option value="">All</option>
            <?php foreach ([
                'BTC','ETH','LTC','BCH','XRP','XLM',
                'LINK','DOT','YFI','SOL','USDT'
            ] as $c): ?>
                <option value="<?= $c ?>" <?= $coin === $c ? 'selected' : '' ?>>
                    <?= $c ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- XMR Amount -->
        <input
            type="number"
            step="0.0001"
            name="amount"
            placeholder="XMR amount"
            value="<?= htmlspecialchars($amount) ?>"
        >

        <!-- Sort -->
        <select name="sort">
            <option value="">Sort</option>
            <option value="cheap" <?= $sort === 'cheap' ? 'selected' : '' ?>>
                Lowest margin
            </option>
            <option value="expensive" <?= $sort === 'expensive' ? 'selected' : '' ?>>
                Highest margin
            </option>
        </select>

        <!-- Button -->
        <button class="btn" style="padding:10px 16px;">
            Apply
        </button>

    </form>
</div>
