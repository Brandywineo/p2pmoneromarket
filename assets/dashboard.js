document.addEventListener('DOMContentLoaded', () => {
    const generateBtn = document.getElementById('generate-new-subaddress');
    const latestAddrEl = document.getElementById('latest-addr');
    const pendingEl = document.getElementById('pending-deposits');
    const listEl = document.getElementById('address-list');

    /* -----------------------------
     * Fetch subaddresses
     * ----------------------------- */
    async function fetchAddresses() {
        try {
            const res = await fetch('/wallet/fetch_addresses.php');
            const data = await res.json();

            if (latestAddrEl) {
                if (data.latest) {
                    latestAddrEl.textContent =
                        data.latest.address.substring(0, 12) + '…';
                    latestAddrEl.title = data.latest.address;
                } else {
                    latestAddrEl.textContent = 'No address yet';
                }
            }

            if (listEl && Array.isArray(data.all)) {
                listEl.innerHTML = '';

                data.all.forEach(addr => {
                    const div = document.createElement('div');
                    div.className = 'addr-item short-address';
                    div.title = addr.address;
                    div.textContent = addr.address.substring(0, 16) + '…';

                    div.addEventListener('click', () => {
                        navigator.clipboard.writeText(addr.address);
                        alert('Address copied to clipboard');
                    });

                    listEl.appendChild(div);
                });
            }
        } catch (e) {
            console.error('fetchAddresses failed', e);
        }
    }

    /* -----------------------------
     * Fetch deposits (FIXED)
     * ----------------------------- */
    async function fetchDeposits() {
        try {
            const res = await fetch('/wallet/fetch_deposits.php');
            const data = await res.json();

            if (!pendingEl) return;
            pendingEl.innerHTML = '';

            if (!Array.isArray(data) || data.length === 0) {
                pendingEl.innerHTML =
                    '<p style="opacity:.6">No deposits yet</p>';
                return;
            }

            data.forEach(dep => {
                const percent = Math.min(
                    100,
                    Math.round((dep.confirmations / dep.required) * 100)
                );

                let statusText = '';
                let statusClass = dep.status;

                if (dep.status === 'pending') {
                    statusText = 'Pending (waiting for first confirmation)';
                } else if (dep.status === 'locked') {
                    statusText = `Unlocking in ${dep.blocks_left} blocks`;
                } else if (dep.status === 'confirmed') {
                    statusText = 'Confirmed & credited';
                } else {
                    statusText = 'Unknown state';
                }

                const div = document.createElement('div');
                div.className = `deposit ${statusClass}`;

                div.innerHTML = `
                    <p><strong>${dep.amount} XMR</strong></p>
                    <p>${statusText}</p>

                    <div class="progress-bar">
                        <div class="progress-fill" style="width:${percent}%"></div>
                        <span class="progress-label">${percent}%</span>
                    </div>
                `;

                pendingEl.appendChild(div);
            });
        } catch (e) {
            console.error('fetchDeposits failed', e);
        }
    }

    /* -----------------------------
     * Generate new subaddress
     * ----------------------------- */
    if (generateBtn) {
        generateBtn.addEventListener('click', async () => {
            try {
                generateBtn.disabled = true;

                const res = await fetch('/wallet/generate_subaddress.php', {
                    method: 'POST'
                });
                const data = await res.json();

                if (data.status === 'ok') {
                    alert('New subaddress generated');
                    fetchAddresses();
                } else {
                    alert('Failed to generate subaddress');
                }
            } catch (e) {
                alert('Network error while generating address');
            } finally {
                generateBtn.disabled = false;
            }
        });
    }

    /* -----------------------------
     * Initial load + polling
     * ----------------------------- */
    fetchAddresses();
    fetchDeposits();

    setInterval(fetchAddresses, 10000);
    setInterval(fetchDeposits, 10000);
});
