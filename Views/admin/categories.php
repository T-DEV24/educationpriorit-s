<section class="section">
    <div class="container">
        <div class="section-header">
            <h1>Rubriques & Tags</h1>
            <div class="hero-actions">
                <button class="btn btn-primary" type="button" id="category-new">Ajouter une rubrique</button>
                <button class="btn btn-outline" type="button" id="tag-new">Ajouter un tag</button>
            </div>
        </div>
        <p class="muted">Gérez les rubriques et tags pour organiser les articles et faciliter la navigation.</p>
        <div class="alert alert-danger d-none" id="admin-category-alert" role="alert"></div>
        <div class="info-card mb-4">
            <h3>Nouvelle rubrique</h3>
            <form class="form-grid" id="category-form">
                <input type="hidden" id="category-id">
                <label>
                    Nom
                    <input type="text" id="category-name" required>
                </label>
                <label>
                    Slug
                    <input type="text" id="category-slug">
                </label>
                <button class="btn btn-primary" type="submit">Enregistrer la rubrique</button>
            </form>
        </div>
        <div class="card-grid" id="category-list"></div>
        <div class="section-header">
            <h2 class="headline-md">Tags</h2>
        </div>
        <div class="info-card mb-4">
            <h3>Nouveau tag</h3>
            <form class="form-grid" id="tag-form">
                <input type="hidden" id="tag-id">
                <label>
                    Nom
                    <input type="text" id="tag-name" required>
                </label>
                <label>
                    Slug
                    <input type="text" id="tag-slug">
                </label>
                <button class="btn btn-primary" type="submit">Enregistrer le tag</button>
            </form>
        </div>
        <div class="card-grid" id="tag-list"></div>
    </div>
</section>

<script>
const categoryAlert = document.getElementById('admin-category-alert');
const categoryList = document.getElementById('category-list');
const tagList = document.getElementById('tag-list');
const categoryForm = document.getElementById('category-form');
const tagForm = document.getElementById('tag-form');
const categoryId = document.getElementById('category-id');
const categoryName = document.getElementById('category-name');
const categorySlug = document.getElementById('category-slug');
const tagId = document.getElementById('tag-id');
const tagName = document.getElementById('tag-name');
const tagSlug = document.getElementById('tag-slug');
const categoryNew = document.getElementById('category-new');
const tagNew = document.getElementById('tag-new');

const slugify = (value) =>
    value
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/(^-|-$)+/g, '');

function renderCard(item, type) {
    const card = document.createElement('div');
    card.className = 'card';
    card.innerHTML = `
        <h3>${item.name ?? '-'}</h3>
        <p>${item.article_count ?? 0} articles</p>
        <div class="card-actions">
            <button class="btn btn-link p-0 admin-edit" data-type="${type}" data-id="${item.id}">Éditer</button>
            <span class="muted">•</span>
            <button class="btn btn-link text-danger p-0 admin-delete" data-type="${type}" data-id="${item.id}">Supprimer</button>
        </div>
    `;
    return card;
}

function loadCategories() {
    return window.apiFetch('/api/admin/categories?limit=100', { credentials: 'same-origin' })
        .then((payload) => {
            if (!payload.ok) {
                throw new Error(window.getApiErrorMessage(payload, 'Impossible de charger les rubriques.'));
            }
            const items = payload.data?.data ?? payload.data ?? [];
            categoryList.innerHTML = '';
            items.forEach((item) => categoryList.appendChild(renderCard(item, 'category')));
        });
}

function loadTags() {
    return window.apiFetch('/api/admin/tags?limit=100', { credentials: 'same-origin' })
        .then((payload) => {
            if (!payload.ok) {
                throw new Error(window.getApiErrorMessage(payload, 'Impossible de charger les tags.'));
            }
            const items = payload.data?.data ?? payload.data ?? [];
            tagList.innerHTML = '';
            items.forEach((item) => tagList.appendChild(renderCard(item, 'tag')));
        });
}

function resetCategoryForm() {
    categoryId.value = '';
    categoryName.value = '';
    categorySlug.value = '';
}

function resetTagForm() {
    tagId.value = '';
    tagName.value = '';
    tagSlug.value = '';
}

categoryName.addEventListener('blur', () => {
    if (!categorySlug.value) {
        categorySlug.value = slugify(categoryName.value);
    }
});

tagName.addEventListener('blur', () => {
    if (!tagSlug.value) {
        tagSlug.value = slugify(tagName.value);
    }
});

categoryForm.addEventListener('submit', (event) => {
    event.preventDefault();
    categoryAlert.classList.add('d-none');
    const payload = {
        name: categoryName.value.trim(),
        slug: categorySlug.value.trim() || slugify(categoryName.value),
    };
    const id = categoryId.value;
    const method = id ? 'PATCH' : 'POST';
    const endpoint = id ? `/api/admin/categories/${id}` : '/api/admin/categories';
    window.apiFetch(endpoint, {
        method,
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify(payload),
    })
        .then((payload) => {
            if (!payload.ok) {
                throw new Error(window.getApiErrorMessage(payload, 'Impossible d’enregistrer la rubrique.'));
            }
            resetCategoryForm();
            return loadCategories();
        })
        .catch((error) => {
            categoryAlert.textContent = error.message;
            categoryAlert.classList.remove('d-none');
        });
});

tagForm.addEventListener('submit', (event) => {
    event.preventDefault();
    categoryAlert.classList.add('d-none');
    const payload = {
        name: tagName.value.trim(),
        slug: tagSlug.value.trim() || slugify(tagName.value),
    };
    const id = tagId.value;
    const method = id ? 'PATCH' : 'POST';
    const endpoint = id ? `/api/admin/tags/${id}` : '/api/admin/tags';
    window.apiFetch(endpoint, {
        method,
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify(payload),
    })
        .then((payload) => {
            if (!payload.ok) {
                throw new Error(window.getApiErrorMessage(payload, 'Impossible d’enregistrer le tag.'));
            }
            resetTagForm();
            return loadTags();
        })
        .catch((error) => {
            categoryAlert.textContent = error.message;
            categoryAlert.classList.remove('d-none');
        });
});

document.addEventListener('click', (event) => {
    const target = event.target;
    if (!target.classList.contains('admin-edit') && !target.classList.contains('admin-delete')) {
        return;
    }
    const type = target.dataset.type;
    const id = target.dataset.id;
    if (target.classList.contains('admin-delete')) {
        if (!window.confirm('Supprimer cet élément ?')) {
            return;
        }
        const endpoint = type === 'category' ? `/api/admin/categories/${id}` : `/api/admin/tags/${id}`;
        window.apiFetch(endpoint, { method: 'DELETE', credentials: 'same-origin' })
            .then(() => Promise.all([loadCategories(), loadTags()]))
            .catch((error) => {
                categoryAlert.textContent = error.message;
                categoryAlert.classList.remove('d-none');
            });
        return;
    }
    const endpoint = type === 'category' ? `/api/admin/categories/${id}` : `/api/admin/tags/${id}`;
    window.apiFetch(endpoint, { credentials: 'same-origin' })
        .then((payload) => {
            if (!payload.ok) {
                throw new Error(window.getApiErrorMessage(payload, 'Impossible de charger les données.'));
            }
            const item = payload.data?.data ?? payload.data ?? {};
            if (type === 'category') {
                categoryId.value = item.id ?? '';
                categoryName.value = item.name ?? '';
                categorySlug.value = item.slug ?? '';
                categoryForm.scrollIntoView({ behavior: 'smooth' });
            } else {
                tagId.value = item.id ?? '';
                tagName.value = item.name ?? '';
                tagSlug.value = item.slug ?? '';
                tagForm.scrollIntoView({ behavior: 'smooth' });
            }
        })
        .catch((error) => {
            categoryAlert.textContent = error.message;
            categoryAlert.classList.remove('d-none');
        });
});

categoryNew.addEventListener('click', () => {
    resetCategoryForm();
    categoryForm.scrollIntoView({ behavior: 'smooth' });
});

tagNew.addEventListener('click', () => {
    resetTagForm();
    tagForm.scrollIntoView({ behavior: 'smooth' });
});

Promise.all([loadCategories(), loadTags()]);
</script>
