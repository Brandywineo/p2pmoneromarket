<form method="post" action="create_submit.php" class="ad-form">
<input type="hidden" name="type" value="<?= $type ?>">

<label>Minimum XMR</label>
<input type="number" step="0.00000001" name="min_xmr" required>

<label>Maximum XMR</label>
<input type="number" step="0.00000001" name="max_xmr" required>

<label><?= $type === 'buy' ? 'Pay using crypto' : 'Receive payment in crypto' ?></label>
<select name="crypto_pay" required>
<?php foreach ($coins as $coin): ?>
    <option value="<?= $coin ?>"><?= strtoupper($coin) ?></option>
<?php endforeach; ?>
</select>

<label>Price margin (%)</label>
<input type="number" step="0.001" name="margin_percent" required>

<div class="hint">
    Positive = above market, Negative = below market
</div>

<?php include __DIR__ . '/price_preview.php'; ?>

<label>Payment time limit (minutes)</label>
<input type="number" name="payment_time_limit" min="5" max="720" required>

<label>Trade terms</label>
<textarea name="terms" rows="4"></textarea>

<button type="submit">
    Create <?= ucfirst($type) ?> Ad
</button>
</form>
