<div class="ad-card card">
    <div class="ad-row">
        <!-- LEFT -->
        <div class="ad-left">
            <!-- PRICE -->
            <div class="ad-price">
                <?= number_format((float)$ad['price_per_xmr'], 8) ?>
                <?= strtoupper($ad['crypto_pay']) ?>/XMR
            </div>

            <!-- MARKET +/- -->
            <div class="ad-market">
                Market
                <?= ((float)$ad['margin_percent'] >= 0 ? '+' : '') ?>
                <?= number_format((float)$ad['margin_percent'], 2) ?>%
            </div>

            <!-- FEE PREVIEW -->
            <?php if (!empty($ad['fee_preview'])): ?>
                <div class="ad-fee">
                    Fee ≈ <?= number_format((float)$ad['fee_preview'], 8) ?>
                    <?= strtoupper($ad['crypto_pay']) ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- RIGHT -->
        <div class="ad-right">
            <div class="ad-limits">
                <?= rtrim($ad['min_xmr'], '0.') ?>
                –
                <?= rtrim($ad['max_xmr'], '0.') ?> XMR
            </div>
            <div class="ad-margin">
                <?= number_format((float)$ad['margin_percent'], 2) ?>% margin
            </div>
        </div>
    </div>

    <!-- ACTIONS -->
    <div class="trade-action" style="display:flex; gap:10px;">
        <a
            href="/ads/edit.php?id=<?= (int)$ad['id'] ?>"
            class="btn"
        >
            Edit
        </a>
        <button
            class="btn danger"
            onclick="openDeleteModal(<?= (int)$ad['id'] ?>)"
        >
            Delete
        </button>
    </div>
</div>
