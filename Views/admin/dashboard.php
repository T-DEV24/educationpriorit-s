<section class="section">
    <div class="container">
        <h1>Tableau de bord</h1>
        <div id="admin-dashboard-alert" class="alert alert-danger d-none" role="alert"></div>
        <div class="card-grid" id="admin-dashboard-cards">
            <div class="card">
                <h3>Articles</h3>
                <p data-count="articles">...</p>
            </div>
            <div class="card">
                <h3>Utilisateurs</h3>
                <p data-count="users">...</p>
            </div>
            <div class="card">
                <h3>Commentaires</h3>
                <p data-count="comments">...</p>
            </div>
            <div class="card">
                <h3>PDF</h3>
                <p data-count="pdf_editions">...</p>
            </div>
            <div class="card">
                <h3>Commandes</h3>
                <p data-count="orders">...</p>
            </div>
            <div class="card">
                <h3>Téléchargements</h3>
                <p data-count="downloads">...</p>
            </div>
        </div>
    </div>
</section>

<script>
    const alertBox = document.getElementById('admin-dashboard-alert');
    const countNodes = document.querySelectorAll('[data-count]');

    const updateCounts = (data) => {
        countNodes.forEach((node) => {
            const key = node.getAttribute('data-count');
            const value = typeof data[key] === 'number' ? data[key] : 0;
            node.textContent = value.toLocaleString('fr-FR');
        });
    };

    const showError = (message) => {
        alertBox.textContent = message;
        alertBox.classList.remove('d-none');
    };

    window.apiFetch('/api/admin/dashboard', { credentials: 'same-origin' })
        .then((payload) => {
            if (!payload.ok) {
                throw new Error(window.getApiErrorMessage(payload, 'Impossible de charger les indicateurs admin.'));
            }
            updateCounts(payload.data?.data ?? payload.data ?? {});
        })
        .catch((error) => {
            showError(error.message);
        });
</script>
