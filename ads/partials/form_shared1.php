<form method="post" action="create_submit.php" class="ad-form">

    <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">

    <!-- MIN XMR -->
    <label>Minimum XMR</label>
    <input
        type="number"
        step="0.00000001"
        name="min_xmr"
        min="0.00000001"
        required
    >

    <!-- MAX XMR -->
    <label>Maximum XMR</label>

    <?php if ($type === 'sell'): ?>
        <div class="balance-info">
            Available balance:
            <strong>
                <?= number_format((float)($balances['xmr'] ?? 0), 8) ?> XMR
            </strong>
        </div>
    <?php endif; ?>

    <input
        type="number"
        step="0.00000001"
        name="max_xmr"
        min="0.00000001"
        required
        <?php if ($type === 'sell'): ?>
            max="<?= (float)($balances['xmr'] ?? 0) ?>"
        <?php endif; ?>
    >

    <!-- CRYPTO -->
    <label>
        <?= $type === 'buy'
            ? 'Pay using crypto'
            : 'Receive payment in crypto'
        ?>
    </label>

    <select name="crypto_pay" required>
        <?php foreach ($coins as $coin): ?>
            <option value="<?= htmlspecialchars($coin) ?>">
                <?= strtoupper($coin) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <!-- MARGIN -->
    <label>Price margin (%)</label>
    <input
        type="number"
        step="0.001"
        name="margin_percent"
        required
    >

    <div class="hint">
        Positive = above market, Negative = below market
    </div>

    <!-- LIVE PRICE PREVIEW -->
    <?php include __DIR__ . '/price_preview.php'; ?>

    <!-- TIME LIMIT -->
    <label>Payment time limit (minutes)</label>
    <input
        type="number"
        name="payment_time_limit"
        min="5"
        max="720"
        required
    >

    <!-- TERMS -->
    <label>Trade terms</label>
    <textarea name="terms" rows="4"></textarea>

    <!-- SUBMIT -->
    <button type="submit">
        Create <?= ucfirst($type) ?> Ad
    </button>

</form>
