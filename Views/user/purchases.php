<section class="section">
    <div class="container">
        <div class="section-header">
            <h1>Historique des achats</h1>
            <a class="link" href="/profil">Retour au profil</a>
        </div>
        <div class="alert alert-danger d-none" id="purchases-alert" role="alert"></div>
        <div class="card-grid" id="orders-grid"></div>
        <div class="pagination">
            <button class="btn btn-outline" type="button" id="orders-prev">Précédent</button>
            <span id="orders-page">Page 1</span>
            <button class="btn btn-outline" type="button" id="orders-next">Suivant</button>
        </div>
        <div class="info-card">
            <h3>Historique des téléchargements</h3>
            <ul id="downloads-list"></ul>
            <p class="muted">Chaque téléchargement est lié à votre compte et enregistré.</p>
        </div>
    </div>
</section>

<script>
const purchasesAlert = document.getElementById('purchases-alert');
const ordersGrid = document.getElementById('orders-grid');
const ordersPrev = document.getElementById('orders-prev');
const ordersNext = document.getElementById('orders-next');
const ordersPage = document.getElementById('orders-page');
const downloadsList = document.getElementById('downloads-list');
let ordersCurrentPage = 1;
let ordersLastPage = 1;

function formatDate(value) {
    if (!value) {
        return '-';
    }
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) {
        return value;
    }
    return date.toLocaleDateString('fr-FR');
}

function renderOrder(order) {
    const card = document.createElement('article');
    card.className = 'card';
    const title = document.createElement('h3');
    title.textContent = order.pdf_title ?? `Commande #${order.id ?? ''}`;
    const date = document.createElement('p');
    date.className = 'muted';
    date.textContent = `Commande du ${formatDate(order.created_at)}`;
    const status = document.createElement('p');
    status.textContent = `Statut : ${order.status ?? '-'}`;
    card.append(title, date, status);
    if (order.status === 'paid') {
        const download = document.createElement('a');
        download.className = 'btn btn-small btn-primary';
        download.href = order.pdf_edition_id ? `/api/shop/download/${order.pdf_edition_id}` : '#';
        download.textContent = 'Télécharger';
        card.append(download);
    }
    return card;
}

function renderDownload(download) {
    const item = document.createElement('li');
    const label = download.pdf_title ?? `PDF #${download.pdf_edition_id ?? ''}`;
    item.textContent = `${label} • ${formatDate(download.downloaded_at)}`;
    return item;
}

function loadOrders() {
    purchasesAlert.classList.add('d-none');
    const params = new URLSearchParams({
        mine: '1',
        page: String(ordersCurrentPage),
        limit: '6',
    });
    window.apiFetch(`/api/orders?${params.toString()}`, { credentials: 'same-origin' })
        .then((payload) => {
            if (!payload.ok) {
                throw new Error(window.getApiErrorMessage(payload, 'Impossible de charger les commandes.'));
            }
            const orders = payload.data?.data ?? payload.data ?? [];
            const pagination = payload.data?.pagination ?? payload.pagination ?? {};
            ordersLastPage = pagination.pages ?? 1;
            ordersPage.textContent = `Page ${pagination.page ?? ordersCurrentPage} / ${ordersLastPage}`;
            ordersGrid.innerHTML = '';
            if (orders.length === 0) {
                ordersGrid.innerHTML = '<p class="muted">Aucune commande trouvée.</p>';
                return;
            }
            orders.forEach(order => ordersGrid.appendChild(renderOrder(order)));
            ordersPrev.disabled = ordersCurrentPage <= 1;
            ordersNext.disabled = ordersCurrentPage >= ordersLastPage;
        })
        .catch(error => {
            purchasesAlert.textContent = error.message;
            purchasesAlert.classList.remove('d-none');
        });
}

function loadDownloads() {
    const params = new URLSearchParams({ mine: '1', limit: '10' });
    window.apiFetch(`/api/downloads?${params.toString()}`, { credentials: 'same-origin' })
        .then((payload) => {
            if (!payload.ok) {
                throw new Error(window.getApiErrorMessage(payload, 'Impossible de charger les téléchargements.'));
            }
            const downloads = payload.data?.data ?? payload.data ?? [];
            downloadsList.innerHTML = '';
            if (downloads.length === 0) {
                downloadsList.innerHTML = '<li class="muted">Aucun téléchargement enregistré.</li>';
                return;
            }
            downloads.forEach(download => downloadsList.appendChild(renderDownload(download)));
        })
        .catch(error => {
            purchasesAlert.textContent = error.message;
            purchasesAlert.classList.remove('d-none');
        });
}

ordersPrev.addEventListener('click', () => {
    if (ordersCurrentPage > 1) {
        ordersCurrentPage -= 1;
        loadOrders();
    }
});

ordersNext.addEventListener('click', () => {
    if (ordersCurrentPage < ordersLastPage) {
        ordersCurrentPage += 1;
        loadOrders();
    }
});

loadOrders();
loadDownloads();
</script>
