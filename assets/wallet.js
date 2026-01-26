function copyText(text) {
    navigator.clipboard.writeText(text);
    alert("Copied");
}

document.getElementById('generateSubaddress')?.addEventListener('click', () => {
    fetch('/wallet/generate_subaddress.php')
        .then(r => r.json())
        .then(() => location.reload());
});

/* Poll deposits every 10 seconds */
setInterval(() => {
    fetch('/wallet/fetch_deposits.php')
        .then(r => r.json())
        .then(deposits => {
            deposits.forEach(dep => {
                const percent = Math.min(
                    100,
                    Math.floor((dep.confirmations / 10) * 100)
                );
                console.log(
                    `Pending ${dep.amount} XMR – ${percent}% – unlocking in ${
                        Math.max(0, dep.unlock_height - dep.height)
                    } blocks`
                );
            });
        });
}, 10000);
