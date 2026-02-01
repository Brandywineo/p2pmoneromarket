<form method="post"
      action="<?= htmlspecialchars($action) ?>"
      class="edit-ad-form">

    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
    <input type="hidden" name="id" value="<?= (int)$ad['id'] ?>">
    <input type="hidden" name="type" value="<?= htmlspecialchars($ad['type']) ?>">

    <!-- CRYPTO -->
    <label>
        <?= $ad['type'] === 'buy'
            ? 'Pay using crypto'
            : 'Receive payment in crypto'
        ?>
    </label>

    <select name="crypto_pay" required>
        <?php foreach ($coins as $coin): ?>
            <option value="<?= htmlspecialchars($coin) ?>"
                <?= $coin === $ad['crypto_pay'] ? 'selected' : '' ?>>
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
        value="<?= htmlspecialchars($ad['margin_percent']) ?>"
    >

    <div class="hint">
        Positive = above market, Negative = below market
    </div>

    <!-- LIVE PRICE PREVIEW -->
    <div class="price-preview">
        <?php include __DIR__ . '/price_preview.php'; ?>
    </div>

    <!-- TERMS -->
    <label>Trade terms</label>
    <textarea name="terms"><?= htmlspecialchars($ad['terms'] ?? '') ?></textarea>

    <!-- SUBMIT -->
    <button type="submit" class="btn">
        Update <?= ucfirst($ad['type']) ?> Ad
    </button>

</form>
