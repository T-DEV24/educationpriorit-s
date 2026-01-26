<section class="section">
    <div class="container">
        <h1>Commandes & Paiements</h1>
        <p class="muted">Suivi des commandes, paiement Mobile Money et statut de transaction.</p>
        <div class="alert alert-danger d-none" id="orders-alert" role="alert"></div>
        <table class="table">
            <thead>
                <tr>
                    <th>Client</th>
                    <th>PDF</th>
                    <th>Commande</th>
                    <th>Paiement</th>
                    <th>Montant</th>
                </tr>
            </thead>
            <tbody id="orders-body"></tbody>
        </table>
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline" type="button" id="orders-prev">Précédent</button>
            <span class="muted" id="orders-page">Page 1</span>
            <button class="btn btn-outline" type="button" id="orders-next">Suivant</button>
        </div>
    </div>
</section>

<script>
const ordersAlert = document.getElementById('orders-alert');
const ordersBody = document.getElementById('orders-body');
const ordersPrev = document.getElementById('orders-prev');
const ordersNext = document.getElementById('orders-next');
const ordersPage = document.getElementById('orders-page');
let ordersCurrentPage = 1;
let ordersLastPage = 1;

function renderOrders(items) {
    ordersBody.innerHTML = '';
    if (!items.length) {
        ordersBody.innerHTML = '<tr><td colspan="5" class="muted">Aucune commande.</td></tr>';
        return;
    }
    items.forEach((order) => {
        const row = document.createElement('tr');
        const customer = order.user_name ?? order.user_email ?? '-';
        const paymentLabel = order.payment_provider
            ? `${order.payment_provider} • ${order.payment_status ?? '-'}`
            : '—';
        row.innerHTML = `
            <td>${customer}</td>
            <td>${order.pdf_title ?? '-'}</td>
            <td>${order.status ?? '-'}</td>
            <td>${paymentLabel}</td>
            <td>${Number(order.amount ?? 0).toLocaleString('fr-FR')} FCFA</td>
        `;
        ordersBody.appendChild(row);
    });
}

function loadOrders() {
    ordersAlert.classList.add('d-none');
    const params = new URLSearchParams({ page: String(ordersCurrentPage), limit: '20' });
    window.apiFetch(`/api/admin/orders?${params.toString()}`, { credentials: 'same-origin' })
        .then((payload) => {
            if (!payload.ok) {
                throw new Error(window.getApiErrorMessage(payload, 'Impossible de charger les commandes.'));
            }
            const items = payload.data?.data ?? payload.data ?? [];
            const pagination = payload.data?.pagination ?? payload.pagination ?? {};
            ordersLastPage = pagination.pages ?? 1;
            ordersPage.textContent = `Page ${pagination.page ?? ordersCurrentPage} / ${ordersLastPage}`;
            renderOrders(items);
            ordersPrev.disabled = ordersCurrentPage <= 1;
            ordersNext.disabled = ordersCurrentPage >= ordersLastPage;
        })
        .catch((error) => {
            ordersAlert.textContent = error.message;
            ordersAlert.classList.remove('d-none');
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
</script>
