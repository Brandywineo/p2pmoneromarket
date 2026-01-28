<div class="ad-card card">
    <div class="ad-row">

        <!-- LEFT -->
        <div class="ad-left">
            <div class="ad-username">
                <?= htmlspecialchars($ad['username']) ?>

                <?php if (!empty($ad['is_online'])): ?>
                    <span class="online-dot" title="Online"></span>
                <?php endif; ?>
            </div>

            <div class="seller-meta">
                <span class="seller-rating">
                    ⭐ <?= number_format($ad['rating'] ?? 0, 1) ?>
                </span>

                <span class="seller-trades">
                    📊 <?= (int)($ad['trade_count'] ?? 0) ?> trades
                </span>
            </div>

            <div class="ad-price">
                <?= htmlspecialchars($ad['price']) ?>
                <?= strtoupper($ad['crypto_pay']) ?>/XMR
            </div>
        </div>

        <!-- RIGHT -->
        <div class="ad-right">
            <div class="ad-limits">
                <?= $ad['min_xmr'] ?> – <?= $ad['max_xmr'] ?> XMR
            </div>
            <div class="ad-margin">
                <?= $ad['margin_percent'] ?>% margin
            </div>
        </div>
    </div>

    <?php if ($user_can_trade): ?>
        <div class="trade-action">
            <a href="/trade/start.php?id=<?= $ad['id'] ?>" class="btn">
                <?= $ad['type'] === 'buy' ? 'Buy XMR' : 'Sell XMR' ?>
            </a>
        </div>

    <?php elseif ($is_logged_in): ?>
        <div class="trade-action trade-muted">
            Complete PGP backup to trade
        </div>
    <?php endif; ?>
</div>
