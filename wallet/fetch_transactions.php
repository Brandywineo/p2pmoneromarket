<script>
setInterval(() => {
    fetch('/wallet/fetch_transactions.php')
        .then(r => r.text())
        .then(html => {
            document.getElementById('tx-container').innerHTML = html;
        });
}, 10000);
</script>
