<section class="section">
    <div class="container">
        <h1>Statistiques</h1>
        <div class="alert alert-danger d-none" id="stats-alert" role="alert"></div>
        <div class="card-grid">
            <div class="card">
                <h3>Articles publiés</h3>
                <p id="stats-articles">...</p>
            </div>
            <div class="card">
                <h3>Comptes</h3>
                <p id="stats-users">...</p>
            </div>
            <div class="card">
                <h3>Commentaires</h3>
                <p id="stats-comments">...</p>
            </div>
            <div class="card">
                <h3>Commandes payées</h3>
                <p id="stats-orders-paid">...</p>
            </div>
            <div class="card">
                <h3>Revenus</h3>
                <p id="stats-revenue">...</p>
            </div>
        </div>
    </div>
</section>

<script>
const statsAlert = document.getElementById('stats-alert');
const statsArticles = document.getElementById('stats-articles');
const statsUsers = document.getElementById('stats-users');
const statsComments = document.getElementById('stats-comments');
const statsOrdersPaid = document.getElementById('stats-orders-paid');
const statsRevenue = document.getElementById('stats-revenue');

window.apiFetch('/api/admin/stats', { credentials: 'same-origin' })
    .then((payload) => {
        if (!payload.ok) {
            throw new Error(window.getApiErrorMessage(payload, 'Impossible de charger les statistiques.'));
        }
        const data = payload.data?.data ?? payload.data ?? {};
        statsArticles.textContent = (data.articles_published ?? 0).toLocaleString('fr-FR');
        statsUsers.textContent = (data.users ?? 0).toLocaleString('fr-FR');
        statsComments.textContent = (data.comments ?? 0).toLocaleString('fr-FR');
        statsOrdersPaid.textContent = (data.orders_paid ?? 0).toLocaleString('fr-FR');
        statsRevenue.textContent = `${(data.revenue ?? 0).toLocaleString('fr-FR')} FCFA`;
    })
    .catch((error) => {
        statsAlert.textContent = error.message;
        statsAlert.classList.remove('d-none');
    });
</script>
