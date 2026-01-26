document.addEventListener('DOMContentLoaded', () => {
    const generateBtn = document.getElementById('generate-new-subaddress');
    const latestAddrEl = document.getElementById('latest-addr');
    const pendingEl = document.getElementById('pending-deposits');
    const listEl = document.getElementById('address-list');

    function fetchAddresses() {
        fetch('/wallet/fetch_addresses.php')
            .then(r => r.json())
            .then(data => {
                if (latestAddrEl && data.latest) latestAddrEl.textContent = data.latest.address;

                if (listEl) {
                    listEl.innerHTML = '';
                    data.all.forEach(addr => {
                        listEl.innerHTML += `<div class="addr-item">${addr.address}</div>`;
                    });
                }
            });
    }

    function fetchDeposits() {
        fetch('/wallet/fetch_deposits.php')
            .then(r => r.json())
            .then(data => {
                if (!pendingEl) return;
                pendingEl.innerHTML = '';
                data.forEach(dep => {
                    const percent = Math.min(100, (dep.confirmations / dep.required) * 100);
                    const blocksLeft = Math.max(0, dep.required - dep.confirmations);
                    pendingEl.innerHTML += `
                        <div class="deposit">
                            <p>Deposit: ${dep.amount} XMR</p>
                            <p>Confirmations: ${dep.confirmations}/${dep.required}</p>
                            <div class="progress-bar"><div style="width:${percent}%"></div></div>
                            <p>Unlocking in ${blocksLeft} blocks</p>
                        </div>
                    `;
                });
            });
    }

    if (generateBtn) {
        generateBtn.addEventListener('click', () => {
            generateBtn.disabled = true;
            fetch('/wallet/generate_subaddress.php')
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'ok') {
                        if (latestAddrEl) latestAddrEl.textContent = data.address;
                        fetchAddresses();
                    }
                    generateBtn.disabled = false;
                }).catch(() => generateBtn.disabled = false);
        });
    }

    setInterval(fetchAddresses, 10000);
    setInterval(fetchDeposits, 10000);
    fetchAddresses();
    fetchDeposits();
});
