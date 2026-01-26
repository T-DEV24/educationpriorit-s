<section class="section">
    <div class="container">
        <div class="section-header">
            <h1>Pages statiques</h1>
            <button class="btn btn-primary" type="button" id="page-new">Nouvelle page</button>
        </div>
        <p class="muted">Gérez les pages institutionnelles et leur statut de publication.</p>
        <div class="alert alert-danger d-none" id="page-alert" role="alert"></div>
        <div class="info-card mb-4">
            <h3>Éditeur de page</h3>
            <form class="form-grid" id="page-form">
                <input type="hidden" id="page-id">
                <label>
                    Titre
                    <input type="text" id="page-title" required>
                </label>
                <label>
                    Slug
                    <input type="text" id="page-slug">
                </label>
                <label class="d-flex align-items-center gap-2">
                    <input type="checkbox" id="page-published">
                    Publier la page
                </label>
                <label>
                    Contenu
                    <textarea id="page-content" rows="6"></textarea>
                </label>
                <button class="btn btn-primary" type="submit">Enregistrer</button>
            </form>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Page</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="page-body"></tbody>
        </table>
    </div>
</section>

<script>
const pageAlert = document.getElementById('page-alert');
const pageForm = document.getElementById('page-form');
const pageId = document.getElementById('page-id');
const pageTitle = document.getElementById('page-title');
const pageSlug = document.getElementById('page-slug');
const pagePublished = document.getElementById('page-published');
const pageContent = document.getElementById('page-content');
const pageBody = document.getElementById('page-body');
const pageNew = document.getElementById('page-new');

const slugify = (value) =>
    value
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/(^-|-$)+/g, '');

function resetPageForm() {
    pageId.value = '';
    pageTitle.value = '';
    pageSlug.value = '';
    pageContent.value = '';
    pagePublished.checked = false;
}

function renderPages(items) {
    pageBody.innerHTML = '';
    if (!items.length) {
        pageBody.innerHTML = '<tr><td colspan="3" class="muted">Aucune page.</td></tr>';
        return;
    }
    items.forEach((item) => {
        const statusLabel = item.is_published ? 'Publié' : 'Brouillon';
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${item.title ?? '-'}</td>
            <td>${statusLabel}</td>
            <td>
                <button class="btn btn-link p-0 page-edit" data-id="${item.id}">Modifier</button>
                <span class="muted">•</span>
                <button class="btn btn-link text-danger p-0 page-delete" data-id="${item.id}">Supprimer</button>
            </td>
        `;
        pageBody.appendChild(row);
    });
}

function loadPages() {
    return window.apiFetch('/api/admin/pages?limit=50', { credentials: 'same-origin' })
        .then((payload) => {
            if (!payload.ok) {
                throw new Error(window.getApiErrorMessage(payload, 'Impossible de charger les pages.'));
            }
            const items = payload.data?.data ?? payload.data ?? [];
            renderPages(items);
        });
}

pageTitle.addEventListener('blur', () => {
    if (!pageSlug.value) {
        pageSlug.value = slugify(pageTitle.value);
    }
});

pageForm.addEventListener('submit', (event) => {
    event.preventDefault();
    pageAlert.classList.add('d-none');
    const payload = {
        title: pageTitle.value.trim(),
        slug: pageSlug.value.trim() || slugify(pageTitle.value),
        content: pageContent.value.trim(),
        is_published: pagePublished.checked ? 1 : 0,
    };
    const id = pageId.value;
    const method = id ? 'PATCH' : 'POST';
    const endpoint = id ? `/api/admin/pages/${id}` : '/api/admin/pages';

    window.apiFetch(endpoint, {
        method,
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify(payload),
    })
        .then((payload) => {
            if (!payload.ok) {
                throw new Error(window.getApiErrorMessage(payload, 'Impossible d’enregistrer la page.'));
            }
            resetPageForm();
            return loadPages();
        })
        .catch((error) => {
            pageAlert.textContent = error.message;
            pageAlert.classList.remove('d-none');
        });
});

pageBody.addEventListener('click', (event) => {
    const target = event.target;
    if (target.classList.contains('page-delete')) {
        const id = target.dataset.id;
        if (!window.confirm('Supprimer cette page ?')) {
            return;
        }
        window.apiFetch(`/api/admin/pages/${id}`, { method: 'DELETE', credentials: 'same-origin' })
            .then(() => loadPages())
            .catch((error) => {
                pageAlert.textContent = error.message;
                pageAlert.classList.remove('d-none');
            });
        return;
    }
    if (target.classList.contains('page-edit')) {
        const id = target.dataset.id;
        window.apiFetch(`/api/admin/pages/${id}`, { credentials: 'same-origin' })
            .then((payload) => {
                if (!payload.ok) {
                    throw new Error(window.getApiErrorMessage(payload, 'Impossible de charger la page.'));
                }
                const item = payload.data?.data ?? payload.data ?? {};
                pageId.value = item.id ?? '';
                pageTitle.value = item.title ?? '';
                pageSlug.value = item.slug ?? '';
                pageContent.value = item.content ?? '';
                pagePublished.checked = Number(item.is_published ?? 0) === 1;
                pageForm.scrollIntoView({ behavior: 'smooth' });
            })
            .catch((error) => {
                pageAlert.textContent = error.message;
                pageAlert.classList.remove('d-none');
            });
    }
});

pageNew.addEventListener('click', () => {
    resetPageForm();
    pageForm.scrollIntoView({ behavior: 'smooth' });
});

loadPages();
</script>
