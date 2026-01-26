<section class="section">
    <div class="container">
        <div class="section-header">
            <h1>Gestion des articles</h1>
            <button class="btn btn-primary" type="button" id="admin-article-create">Nouvel article</button>
        </div>
        <p class="muted">Créez, modifiez et publiez les articles. Les brouillons restent invisibles jusqu’à publication.</p>
        <div id="admin-article-alert" class="alert alert-danger d-none" role="alert"></div>
        <table class="table">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Rubrique</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="admin-article-body"></tbody>
        </table>
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline" type="button" id="admin-article-prev">Précédent</button>
            <span class="muted" id="admin-article-page">Page 1</span>
            <button class="btn btn-outline" type="button" id="admin-article-next">Suivant</button>
        </div>
    </div>
</section>

<script>
    const articleBody = document.getElementById('admin-article-body');
    const articleAlert = document.getElementById('admin-article-alert');
    const articlePage = document.getElementById('admin-article-page');
    const articlePrev = document.getElementById('admin-article-prev');
    const articleNext = document.getElementById('admin-article-next');
    const articleCreate = document.getElementById('admin-article-create');
    let articleCurrentPage = 1;
    let articleLastPage = 1;
    let articleRows = [];

    const renderArticles = (items) => {
        articleBody.innerHTML = '';
        if (!items.length) {
            articleBody.innerHTML = '<tr><td colspan="4" class="muted">Aucun article trouvé.</td></tr>';
            return;
        }
        items.forEach((item) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${item.title ?? 'Sans titre'}</td>
                <td>${item.category_id ?? '-'}</td>
                <td>${item.status ?? 'draft'}</td>
                <td>
                    <button class="btn btn-link p-0 admin-article-edit" data-id="${item.id}">Éditer</button>
                    <span class="muted">•</span>
                    <button class="btn btn-link text-danger p-0 admin-article-delete" data-id="${item.id}">Supprimer</button>
                </td>
            `;
            articleBody.appendChild(row);
        });
    };

    const loadArticles = () => {
        articleAlert.classList.add('d-none');
        fetch(`/api/admin/articles?page=${articleCurrentPage}&limit=10`, { credentials: 'same-origin' })
            .then((response) => {
                if (!response.ok) {
                    throw new Error('Impossible de charger les articles.');
                }
                return response.json();
            })
            .then((payload) => {
                articleRows = payload.data ?? [];
                articleLastPage = payload.pagination?.pages ?? 1;
                articlePage.textContent = `Page ${payload.pagination?.page ?? articleCurrentPage} / ${articleLastPage}`;
                renderArticles(articleRows);
            })
            .catch((error) => {
                articleAlert.textContent = error.message;
                articleAlert.classList.remove('d-none');
            });
    };

    const promptForPayload = (initial) => {
        const value = window.prompt('Entrez le JSON de l’article :', initial);
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

    articleCreate.addEventListener('click', () => {
        const payload = promptForPayload('{"title":"","content":"","status":"draft"}');
        if (!payload) {
            return;
        }
        fetch('/api/admin/articles', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify(payload),
        }).then(() => loadArticles());
    });

    articleBody.addEventListener('click', (event) => {
        const target = event.target;
        if (target.classList.contains('admin-article-edit')) {
            const id = target.getAttribute('data-id');
            const current = articleRows.find((item) => String(item.id) === id);
            const payload = promptForPayload(JSON.stringify(current ?? {}, null, 2));
            if (!payload) {
                return;
            }
            fetch(`/api/admin/articles/${id}`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'same-origin',
                body: JSON.stringify(payload),
            }).then(() => loadArticles());
        }
        if (target.classList.contains('admin-article-delete')) {
            const id = target.getAttribute('data-id');
            if (!window.confirm('Supprimer cet article ?')) {
                return;
            }
            fetch(`/api/admin/articles/${id}`, {
                method: 'DELETE',
                credentials: 'same-origin',
            }).then(() => loadArticles());
        }
    });

    articlePrev.addEventListener('click', () => {
        if (articleCurrentPage > 1) {
            articleCurrentPage -= 1;
            loadArticles();
        }
    });

    articleNext.addEventListener('click', () => {
        if (articleCurrentPage < articleLastPage) {
            articleCurrentPage += 1;
            loadArticles();
        }
    });

    loadArticles();
</script>
