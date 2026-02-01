let deleteAdId = null;

function openDeleteModal(adId) {
    deleteAdId = adId;
    document.getElementById('deleteModal')
        .classList.remove('hidden');
}

function closeDeleteModal() {
    deleteAdId = null;
    document.getElementById('deleteModal')
        .classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', () => {
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    if (!confirmBtn) return;

    confirmBtn.addEventListener('click', () => {
        if (!deleteAdId) return;

        fetch('/ads/delete.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-Token': window.CSRF_TOKEN
            },
            body: 'id=' + encodeURIComponent(deleteAdId)
        })
        .then(res => {
            if (!res.ok) throw new Error();
            location.reload();
        })
        .catch(() => alert('Delete failed'));
    });
});
