<section class="section">
    <div class="container">
        <div class="section-header">
            <h1>Gestion des PDF</h1>
            <button class="btn btn-primary" type="button" id="admin-pdf-create">Ajouter un PDF</button>
        </div>
        <p class="muted">Ajoutez les numéros, leurs prix et les fichiers PDF associés.</p>
        <div id="admin-pdf-alert" class="alert alert-danger d-none" role="alert"></div>
        <table class="table">
            <thead>
                <tr>
                    <th>Numéro</th>
                    <th>Date</th>
                    <th>Prix</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="admin-pdf-body"></tbody>
        </table>
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline" type="button" id="admin-pdf-prev">Précédent</button>
            <span class="muted" id="admin-pdf-page">Page 1</span>
            <button class="btn btn-outline" type="button" id="admin-pdf-next">Suivant</button>
        </div>
    </div>
</section>

<script>
    const pdfBody = document.getElementById('admin-pdf-body');
    const pdfAlert = document.getElementById('admin-pdf-alert');
    const pdfPage = document.getElementById('admin-pdf-page');
    const pdfPrev = document.getElementById('admin-pdf-prev');
    const pdfNext = document.getElementById('admin-pdf-next');
    const pdfCreate = document.getElementById('admin-pdf-create');
    let pdfCurrentPage = 1;
    let pdfLastPage = 1;
    let pdfRows = [];

    const renderPdfs = (items) => {
        pdfBody.innerHTML = '';
        if (!items.length) {
            pdfBody.innerHTML = '<tr><td colspan="5" class="muted">Aucun PDF trouvé.</td></tr>';
            return;
        }
        items.forEach((item) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${item.title ?? 'Sans titre'}</td>
                <td>${item.published_at ?? '-'}</td>
                <td>${item.price ?? '-'}</td>
                <td>${item.status ?? 'draft'}</td>
                <td>
                    <button class="btn btn-link p-0 admin-pdf-edit" data-id="${item.id}">Modifier</button>
                    <span class="muted">•</span>
                    <button class="btn btn-link text-danger p-0 admin-pdf-delete" data-id="${item.id}">Supprimer</button>
                </td>
            `;
            pdfBody.appendChild(row);
        });
    };

    const loadPdfs = () => {
        pdfAlert.classList.add('d-none');
        fetch(`/api/admin/pdf-editions?page=${pdfCurrentPage}&limit=10`, { credentials: 'same-origin' })
            .then((response) => {
                if (!response.ok) {
                    throw new Error('Impossible de charger les PDF.');
                }
                return response.json();
            })
            .then((payload) => {
                pdfRows = payload.data ?? [];
                pdfLastPage = payload.pagination?.pages ?? 1;
                pdfPage.textContent = `Page ${payload.pagination?.page ?? pdfCurrentPage} / ${pdfLastPage}`;
                renderPdfs(pdfRows);
            })
            .catch((error) => {
                pdfAlert.textContent = error.message;
                pdfAlert.classList.remove('d-none');
            });
    };

    const promptForPayload = (initial) => {
        const value = window.prompt('Entrez le JSON du PDF :', initial);
        if (!value) {
            return null;
        }
        try {
            return JSON.parse(value);
        } catch (error) {
            window.alert('JSON invalide. Veuillez réessayer.');
            return null;
        }
    };

    pdfCreate.addEventListener('click', () => {
        const payload = promptForPayload('{"title":"","price":0,"published_at":"","pdf_path":""}');
        if (!payload) {
            return;
        }
        fetch('/api/admin/pdf-editions', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify(payload),
        }).then(() => loadPdfs());
    });

    pdfBody.addEventListener('click', (event) => {
        const target = event.target;
        if (target.classList.contains('admin-pdf-edit')) {
            const id = target.getAttribute('data-id');
            const current = pdfRows.find((item) => String(item.id) === id);
            const payload = promptForPayload(JSON.stringify(current ?? {}, null, 2));
            if (!payload) {
                return;
            }
            fetch(`/api/admin/pdf-editions/${id}`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'same-origin',
                body: JSON.stringify(payload),
            }).then(() => loadPdfs());
        }
        if (target.classList.contains('admin-pdf-delete')) {
            const id = target.getAttribute('data-id');
            if (!window.confirm('Supprimer ce PDF ?')) {
                return;
            }
            fetch(`/api/admin/pdf-editions/${id}`, {
                method: 'DELETE',
                credentials: 'same-origin',
            }).then(() => loadPdfs());
        }
    });

    pdfPrev.addEventListener('click', () => {
        if (pdfCurrentPage > 1) {
            pdfCurrentPage -= 1;
            loadPdfs();
        }
    });

    pdfNext.addEventListener('click', () => {
        if (pdfCurrentPage < pdfLastPage) {
            pdfCurrentPage += 1;
            loadPdfs();
        }
    });

    loadPdfs();
</script>
