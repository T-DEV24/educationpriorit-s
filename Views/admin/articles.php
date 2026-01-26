<section class="section">
    <div class="container">
        <div class="section-header">
            <h1>Gestion des articles</h1>
            <button class="btn btn-primary" type="button" id="admin-article-create">Nouvel article</button>
        </div>
        <p class="muted">Créez, modifiez et publiez les articles. Les brouillons restent invisibles jusqu’à publication.</p>
        <div class="info-card mb-4" id="admin-article-form-wrapper">
            <h3>Éditeur d’article</h3>
            <form class="form-grid" id="admin-article-form">
                <input type="hidden" id="admin-article-id">
                <label>
                    Titre
                    <input type="text" id="admin-article-title" placeholder="Titre de l’article" required>
                </label>
                <label>
                    Slug
                    <input type="text" id="admin-article-slug" placeholder="slug-de-l-article">
                </label>
                <label>
                    Rubrique
                    <select id="admin-article-category" required></select>
                </label>
                <label>
                    Statut de publication
                    <select id="admin-article-status" required>
                        <option value="draft">Brouillon</option>
                        <option value="published">Publié</option>
                    </select>
                </label>
                <label>
                    Date de publication
                    <input type="datetime-local" id="admin-article-published">
                </label>
                <label>
                    Résumé
                    <textarea id="admin-article-summary" rows="3" placeholder="Résumé court pour les listes"></textarea>
                </label>
                <label>
                    Image de couverture
                    <input type="file" id="admin-article-image" accept="image/*">
                    <small class="muted" id="admin-article-image-info"></small>
                </label>
                <label>
                    Ajouter un PDF
                    <input type="file" id="admin-article-pdf" accept="application/pdf">
                    <small class="muted">Le lien sera inséré dans le contenu.</small>
                </label>
                <div>
                    <span class="muted d-block mb-2">Tags</span>
                    <div class="pill-grid" id="admin-article-tags"></div>
                </div>
                <div>
                    <span class="muted d-block mb-2">Contenu</span>
                    <div class="d-flex gap-2 flex-wrap mb-2">
                        <button class="btn btn-outline btn-small" type="button" data-command="bold">Gras</button>
                        <button class="btn btn-outline btn-small" type="button" data-command="italic">Italique</button>
                        <button class="btn btn-outline btn-small" type="button" data-command="underline">Souligné</button>
                        <button class="btn btn-outline btn-small" type="button" data-command="insertUnorderedList">Liste</button>
                        <button class="btn btn-outline btn-small" type="button" data-command="insertOrderedList">Numérotation</button>
                        <button class="btn btn-outline btn-small" type="button" id="admin-article-link">Lien</button>
                    </div>
                    <div id="admin-article-editor" class="editor" contenteditable="true"></div>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <button class="btn btn-primary" type="submit">Enregistrer</button>
                    <button class="btn btn-outline" type="button" id="admin-article-reset">Réinitialiser</button>
                </div>
                <div class="alert alert-danger d-none" id="admin-article-form-alert" role="alert"></div>
                <div class="alert alert-success d-none" id="admin-article-form-success" role="alert"></div>
            </form>
        </div>
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
    const articleForm = document.getElementById('admin-article-form');
    const articleFormAlert = document.getElementById('admin-article-form-alert');
    const articleFormSuccess = document.getElementById('admin-article-form-success');
    const articleIdInput = document.getElementById('admin-article-id');
    const articleTitleInput = document.getElementById('admin-article-title');
    const articleSlugInput = document.getElementById('admin-article-slug');
    const articleCategorySelect = document.getElementById('admin-article-category');
    const articleStatusSelect = document.getElementById('admin-article-status');
    const articlePublishedInput = document.getElementById('admin-article-published');
    const articleSummaryInput = document.getElementById('admin-article-summary');
    const articleImageInput = document.getElementById('admin-article-image');
    const articleImageInfo = document.getElementById('admin-article-image-info');
    const articlePdfInput = document.getElementById('admin-article-pdf');
    const articleTagsWrapper = document.getElementById('admin-article-tags');
    const articleEditor = document.getElementById('admin-article-editor');
    const articleReset = document.getElementById('admin-article-reset');
    const articleLink = document.getElementById('admin-article-link');
    let articleCurrentPage = 1;
    let articleLastPage = 1;
    let articleRows = [];
    let categories = [];
    let tags = [];
    let articleImagePath = '';

    const renderArticles = (items) => {
        articleBody.innerHTML = '';
        if (!items.length) {
            articleBody.innerHTML = '<tr><td colspan="4" class="muted">Aucun article trouvé.</td></tr>';
            return;
        }
        items.forEach((item) => {
            const row = document.createElement('tr');
            const categoryName = categories.find((category) => category.id === item.category_id)?.name ?? item.category_id ?? '-';
            row.innerHTML = `
                <td>${item.title ?? 'Sans titre'}</td>
                <td>${categoryName}</td>
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

    const slugify = (value) =>
        value
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/(^-|-$)+/g, '');

    const resetForm = () => {
        articleIdInput.value = '';
        articleTitleInput.value = '';
        articleSlugInput.value = '';
        articleStatusSelect.value = 'draft';
        articlePublishedInput.value = '';
        articleSummaryInput.value = '';
        articleImageInput.value = '';
        articlePdfInput.value = '';
        articleEditor.innerHTML = '';
        articleImagePath = '';
        articleImageInfo.textContent = '';
        articleFormAlert.classList.add('d-none');
        articleFormSuccess.classList.add('d-none');
        document.querySelectorAll('[data-tag-checkbox]').forEach((input) => {
            input.checked = false;
        });
    };

    const renderCategories = () => {
        articleCategorySelect.innerHTML = '<option value="">Sélectionner une rubrique</option>';
        categories.forEach((category) => {
            const option = document.createElement('option');
            option.value = category.id;
            option.textContent = category.name ?? category.slug;
            articleCategorySelect.appendChild(option);
        });
    };

    const renderTags = () => {
        articleTagsWrapper.innerHTML = '';
        if (!tags.length) {
            articleTagsWrapper.innerHTML = '<p class="muted">Aucun tag disponible.</p>';
            return;
        }
        tags.forEach((tag) => {
            const label = document.createElement('label');
            label.className = 'pill';
            label.innerHTML = `
                <input type="checkbox" data-tag-checkbox value="${tag.id}"> ${tag.name ?? tag.slug}
            `;
            articleTagsWrapper.appendChild(label);
        });
    };

    const loadCategories = () =>
        window.apiFetch('/api/categories?limit=100', { credentials: 'same-origin' })
            .then((payload) => {
                if (!payload.ok) {
                    throw new Error(window.getApiErrorMessage(payload, 'Impossible de charger les rubriques.'));
                }
                categories = payload.data?.data ?? payload.data ?? [];
                renderCategories();
            });

    const loadTags = () =>
        window.apiFetch('/api/tags?limit=100', { credentials: 'same-origin' })
            .then((payload) => {
                if (!payload.ok) {
                    throw new Error(window.getApiErrorMessage(payload, 'Impossible de charger les tags.'));
                }
                tags = payload.data?.data ?? payload.data ?? [];
                renderTags();
            });

    const loadArticles = () => {
        articleAlert.classList.add('d-none');
        window.apiFetch(`/api/admin/articles?page=${articleCurrentPage}&limit=10`, { credentials: 'same-origin' })
            .then((payload) => {
                if (!payload.ok) {
                    throw new Error(window.getApiErrorMessage(payload, 'Impossible de charger les articles.'));
                }
                articleRows = payload.data?.data ?? payload.data ?? [];
                const pagination = payload.data?.pagination ?? payload.pagination ?? {};
                articleLastPage = pagination.pages ?? 1;
                articlePage.textContent = `Page ${pagination.page ?? articleCurrentPage} / ${articleLastPage}`;
                renderArticles(articleRows);
            })
            .catch((error) => {
                articleAlert.textContent = error.message;
                articleAlert.classList.remove('d-none');
            });
    };

    const loadArticleDetail = (id) =>
        window.apiFetch(`/api/admin/articles/${id}`, { credentials: 'same-origin' })
            .then((payload) => {
                if (!payload.ok) {
                    throw new Error(window.getApiErrorMessage(payload, 'Impossible de charger l’article.'));
                }
                return payload.data?.data ?? payload.data ?? null;
            });

    articleCreate.addEventListener('click', () => {
        resetForm();
        articleForm.scrollIntoView({ behavior: 'smooth' });
    });

    articleBody.addEventListener('click', (event) => {
        const target = event.target;
        if (target.classList.contains('admin-article-edit')) {
            const id = target.getAttribute('data-id');
            loadArticleDetail(id)
                .then((article) => {
                    if (!article) {
                        return;
                    }
                    resetForm();
                    articleIdInput.value = article.id ?? '';
                    articleTitleInput.value = article.title ?? '';
                    articleSlugInput.value = article.slug ?? '';
                    articleStatusSelect.value = article.status ?? 'draft';
                    articlePublishedInput.value = article.published_at ? article.published_at.replace(' ', 'T') : '';
                    articleSummaryInput.value = article.summary ?? '';
                    articleEditor.innerHTML = article.content ?? '';
                    articleImagePath = article.image_path ?? '';
                    articleImageInfo.textContent = articleImagePath ? `Image enregistrée : ${articleImagePath}` : '';
                    const tagIds = (article.tag_ids ?? []).map(String);
                    document.querySelectorAll('[data-tag-checkbox]').forEach((input) => {
                        input.checked = tagIds.includes(input.value);
                    });
                    articleCategorySelect.value = article.category_id ?? '';
                    articleForm.scrollIntoView({ behavior: 'smooth' });
                })
                .catch((error) => {
                    articleAlert.textContent = error.message;
                    articleAlert.classList.remove('d-none');
                });
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

    articleTitleInput.addEventListener('blur', () => {
        if (!articleSlugInput.value) {
            articleSlugInput.value = slugify(articleTitleInput.value);
        }
    });

    articleForm.addEventListener('submit', (event) => {
        event.preventDefault();
        articleFormAlert.classList.add('d-none');
        articleFormSuccess.classList.add('d-none');

        if (!articleTitleInput.value.trim() || !articleCategorySelect.value) {
            articleFormAlert.textContent = 'Titre et rubrique sont requis.';
            articleFormAlert.classList.remove('d-none');
            return;
        }

        const tagIds = Array.from(document.querySelectorAll('[data-tag-checkbox]:checked')).map((input) => Number(input.value));
        const payload = {
            title: articleTitleInput.value.trim(),
            slug: articleSlugInput.value.trim() || slugify(articleTitleInput.value),
            category_id: Number(articleCategorySelect.value),
            status: articleStatusSelect.value,
            published_at: articlePublishedInput.value ? articlePublishedInput.value.replace('T', ' ') : null,
            summary: articleSummaryInput.value.trim(),
            content: articleEditor.innerHTML.trim(),
            image_path: articleImagePath,
            tag_ids: tagIds,
        };

        const id = articleIdInput.value;
        const method = id ? 'PATCH' : 'POST';
        const endpoint = id ? `/api/admin/articles/${id}` : '/api/admin/articles';

        window.apiFetch(endpoint, {
            method,
            headers: { 'Content-Type': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify(payload),
        })
            .then((payload) => {
                if (!payload.ok) {
                    throw new Error(window.getApiErrorMessage(payload, 'Impossible d’enregistrer l’article.'));
                }
                articleFormSuccess.textContent = 'Article enregistré.';
                articleFormSuccess.classList.remove('d-none');
                loadArticles();
            })
            .catch((error) => {
                articleFormAlert.textContent = error.message;
                articleFormAlert.classList.remove('d-none');
            });
    });

    articleReset.addEventListener('click', resetForm);

    articleEditor.addEventListener('input', () => {
        articleFormSuccess.classList.add('d-none');
    });

    articleForm.addEventListener('click', (event) => {
        const button = event.target.closest('[data-command]');
        if (!button) {
            return;
        }
        document.execCommand(button.dataset.command, false, null);
    });

    articleLink.addEventListener('click', () => {
        const url = window.prompt('URL du lien :');
        if (url) {
            document.execCommand('createLink', false, url);
        }
    });

    const uploadMedia = (file) => {
        const formData = new FormData();
        formData.append('file', file);
        return window.apiFetch('/api/media/upload', {
            method: 'POST',
            credentials: 'same-origin',
            body: formData,
        });
    };

    articleImageInput.addEventListener('change', () => {
        const file = articleImageInput.files[0];
        if (!file) {
            return;
        }
        uploadMedia(file)
            .then((payload) => {
                if (!payload.ok) {
                    throw new Error(window.getApiErrorMessage(payload, 'Impossible de téléverser l’image.'));
                }
                articleImagePath = payload.data?.data?.file_path ?? payload.data?.file_path ?? '';
                articleImageInfo.textContent = articleImagePath ? `Image enregistrée : ${articleImagePath}` : '';
            })
            .catch((error) => {
                articleFormAlert.textContent = error.message;
                articleFormAlert.classList.remove('d-none');
            });
    });

    articlePdfInput.addEventListener('change', () => {
        const file = articlePdfInput.files[0];
        if (!file) {
            return;
        }
        uploadMedia(file)
            .then((payload) => {
                if (!payload.ok) {
                    throw new Error(window.getApiErrorMessage(payload, 'Impossible de téléverser le PDF.'));
                }
                const filePath = payload.data?.data?.file_path ?? payload.data?.file_path ?? '';
                if (filePath) {
                    articleEditor.innerHTML += `<p><a href="/${filePath}" target="_blank">Télécharger le PDF</a></p>`;
                }
            })
            .catch((error) => {
                articleFormAlert.textContent = error.message;
                articleFormAlert.classList.remove('d-none');
            });
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

    Promise.all([loadCategories(), loadTags()]).then(() => loadArticles());
</script>
