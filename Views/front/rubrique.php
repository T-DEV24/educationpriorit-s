<section class="section">
    <div class="container" data-category-slug="<?= htmlspecialchars($slug ?? '') ?>">
        <div class="section-header">
            <div>
                <p class="eyebrow">Rubrique</p>
                <h1 id="rubrique-title"><?= htmlspecialchars($rubrique ?? 'Rubrique') ?></h1>
            </div>
            <a class="link" href="/rubriques">Retour aux rubriques</a>
        </div>
        <div class="alert alert-danger d-none" id="rubrique-alert" role="alert"></div>
        <div class="card-grid" id="rubrique-articles"></div>
        <div class="pagination">
            <button class="btn btn-outline" type="button" id="rubrique-prev">Précédent</button>
            <span id="rubrique-page">Page 1</span>
            <button class="btn btn-outline" type="button" id="rubrique-next">Suivant</button>
        </div>
    </div>
</section>

<script>
const rubriqueContainer = document.querySelector('[data-category-slug]');
const rubriquePathParts = window.location.pathname.split('/').filter(Boolean);
const rubriqueDerivedSlug = rubriquePathParts.length > 1 ? rubriquePathParts[rubriquePathParts.length - 1] : '';
const rubriqueSlug = rubriqueContainer?.dataset.categorySlug || rubriqueDerivedSlug;
const rubriqueTitle = document.getElementById('rubrique-title');
const rubriqueGrid = document.getElementById('rubrique-articles');
const rubriqueAlert = document.getElementById('rubrique-alert');
const rubriquePrev = document.getElementById('rubrique-prev');
const rubriqueNext = document.getElementById('rubrique-next');
const rubriquePage = document.getElementById('rubrique-page');
let rubriqueCurrentPage = 1;
let rubriqueLastPage = 1;
let rubriqueCategory = null;

function renderRubriqueCard(article) {
    const card = document.createElement('article');
    card.className = 'card';
    const title = document.createElement('h3');
    title.textContent = article.title ?? 'Article';
    const summary = document.createElement('p');
    summary.textContent = article.summary ?? 'Résumé indisponible.';
    const actions = document.createElement('div');
    actions.className = 'card-actions';
    const link = document.createElement('a');
    link.className = 'btn btn-small';
    link.href = article.slug ? `/article/${article.slug}` : '/article';
    link.textContent = 'Lire';
    const likeButton = document.createElement('button');
    likeButton.className = 'btn btn-small btn-outline js-like';
    likeButton.type = 'button';
    likeButton.dataset.articleId = article.id ?? '';
    likeButton.textContent = `Like (${article.likes_count ?? 0})`;
    actions.append(link, likeButton);
    card.append(title, summary, actions);
    return card;
}

function loadRubriqueArticles() {
    if (!rubriqueSlug) {
        rubriqueAlert.textContent = 'Rubrique introuvable.';
        rubriqueAlert.classList.remove('d-none');
        return;
    }
    rubriqueAlert.classList.add('d-none');
    const params = new URLSearchParams({
        page: String(rubriqueCurrentPage),
        limit: '6',
        category: rubriqueSlug,
    });
    fetch(`/api/articles?${params.toString()}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Impossible de charger les articles.');
            }
            return response.json();
        })
        .then(payload => {
            const articles = payload.data ?? [];
            rubriqueLastPage = payload.pagination?.pages ?? 1;
            rubriquePage.textContent = `Page ${payload.pagination?.page ?? rubriqueCurrentPage} / ${rubriqueLastPage}`;
            rubriqueGrid.innerHTML = '';
            if (articles.length === 0) {
                rubriqueGrid.innerHTML = '<p class="muted">Aucun article dans cette rubrique.</p>';
                return;
            }
            articles.forEach(article => rubriqueGrid.appendChild(renderRubriqueCard(article)));
            rubriquePrev.disabled = rubriqueCurrentPage <= 1;
            rubriqueNext.disabled = rubriqueCurrentPage >= rubriqueLastPage;
        })
        .catch(error => {
            rubriqueAlert.textContent = error.message;
            rubriqueAlert.classList.remove('d-none');
        });
}

function loadRubriqueInfo() {
    if (!rubriqueSlug) {
        return;
    }
    fetch(`/api/categories?slug=${encodeURIComponent(rubriqueSlug)}`)
        .then(response => response.json())
        .then(payload => {
            rubriqueCategory = payload.data ?? null;
            if (rubriqueCategory?.name) {
                rubriqueTitle.textContent = rubriqueCategory.name;
            }
        })
        .catch(() => {});
}

rubriqueGrid.addEventListener('click', event => {
    const target = event.target;
    if (!target.classList.contains('js-like')) {
        return;
    }
    const articleId = target.dataset.articleId;
    if (!articleId) {
        return;
    }
    fetch('/api/likes', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ article_id: Number(articleId) }),
        credentials: 'same-origin',
    })
        .then(response => {
            if (!response.ok && response.status !== 409) {
                throw new Error('Impossible d\'ajouter le like.');
            }
            return response.json();
        })
        .then(() => fetch(`/api/likes?article_id=${articleId}`))
        .then(response => response.json())
        .then(payload => {
            const count = payload.data?.count ?? 0;
            target.textContent = `Like (${count})`;
        })
        .catch(() => {
            rubriqueAlert.textContent = 'Impossible d\'ajouter le like. Connectez-vous si nécessaire.';
            rubriqueAlert.classList.remove('d-none');
        });
});

rubriquePrev.addEventListener('click', () => {
    if (rubriqueCurrentPage > 1) {
        rubriqueCurrentPage -= 1;
        loadRubriqueArticles();
    }
});

rubriqueNext.addEventListener('click', () => {
    if (rubriqueCurrentPage < rubriqueLastPage) {
        rubriqueCurrentPage += 1;
        loadRubriqueArticles();
    }
});

loadRubriqueInfo();
loadRubriqueArticles();
</script>
