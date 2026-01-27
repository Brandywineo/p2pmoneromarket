/* ============================
   Global UI Helpers
   ============================ */

/* Copy TXID to clipboard */
function copyTxid(el) {
    const txid = el.dataset.txid;
    if (!txid) return;

    navigator.clipboard.writeText(txid).then(() => {
        el.classList.add('copied');
        setTimeout(() => el.classList.remove('copied'), 600);
    });
}

/* Dashboard mobile menu */
function toggleDashMenu() {
    const menu = document.getElementById('dashMenu');
    if (!menu) return;

    menu.style.display = 
        menu.style.display === 'block' ? 'none' : 'block';
}

/* Optional: close menu on outside click */
document.addEventListener('click', (e) => {
    const menu = document.getElementById('dashMenu');
    const toggle = document.querySelector('.menu-toggle');

    if (!menu || !toggle) return;

    if (!menu.contains(e.target) && !toggle.contains(e.target)) {
        menu.style.display = 'none';
    }
});
function copyAddress(el) {
    const address = el.dataset.address;
    if (!address) return;

    navigator.clipboard.writeText(address).then(() => {
        el.classList.add('copied');
        setTimeout(() => el.classList.remove('copied'), 600);
    });
}
