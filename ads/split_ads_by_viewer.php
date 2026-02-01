<?php
declare(strict_types=1);

/*
 Viewer intent logic (single source of truth)

 - Buy XMR page/tab  → show SELL ads
 - Sell XMR page/tab → show BUY ads
*/

function splitAdsByViewerIntent(array $ads): array
{
    return [
        'buy'  => array_values(array_filter($ads, fn($a) => $a['type'] === 'sell')),
        'sell' => array_values(array_filter($ads, fn($a) => $a['type'] === 'buy')),
    ];
}
