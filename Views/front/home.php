<section class="hero">
    <div class="container hero-grid">
        <div>
            <p class="eyebrow">Journal du jour</p>
            <h1 class="headline-xl">Reprise sous haute sélection</h1>
            <p class="headline">Études doctorales : seulement 600 places ouvertes pour les 11 universités d'État.</p>
            <p>Découvrez les articles, dossiers et reportages essentiels. Accédez librement aux titres du jour et achetez les numéros PDF en un clic.</p>
            <div class="hero-actions">
                <a class="btn btn-primary" href="/rubriques">Explorer les rubriques</a>
                <a class="btn btn-outline" href="/boutique">Voir la boutique PDF</a>
            </div>
        </div>
        <div class="hero-card">
            <h2 class="headline-md">Édition du jour</h2>
            <p class="muted">12 septembre 2024 • Hebdo</p>
            <ul class="checklist">
                <li>Reprise des études doctorales</li>
                <li>Focus sur l'hygiène scolaire</li>
                <li>Zoom campus & innovations</li>
            </ul>
            <div class="hero-actions">
                <button class="btn btn-primary" type="button">Lire le journal</button>
                <button class="btn btn-ghost" type="button">Acheter le PDF</button>
            </div>
        </div>
    </div>
</section>

<section class="container section">
    <div class="section-header">
        <h2 class="headline-md">Rubriques</h2>
        <a class="link" href="/rubriques">Tout voir</a>
    </div>
    <div class="alert alert-danger d-none" id="home-rubriques-alert" role="alert"></div>
    <div class="pill-grid" id="home-rubriques-grid"></div>
</section>

<section class="container section">
    <div class="section-header">
        <h2 class="headline-md">À la une</h2>
        <a class="link" href="/rubriques">Tout voir</a>
    </div>
    <div class="alert alert-danger d-none" id="home-article-alert" role="alert"></div>
    <div class="card-grid" id="home-article-grid"></div>
</section>

<section class="section alt">
    <div class="container section-header">
        <h2>Journal PDF en vente</h2>
        <a class="link" href="/boutique">Voir le catalogue</a>
    </div>
    <div class="container card-grid">
        <article class="card">
            <h3>Numéro spécial Rentrée</h3>
            <p class="muted">Édition septembre 2024</p>
            <p>Prix : 1 500 FCFA</p>
            <div class="card-actions">
                <a class="btn btn-small" href="/boutique/numero">Détails</a>
                <button class="btn btn-small btn-primary" type="button">Acheter</button>
            </div>
        </article>
        <article class="card">
            <h3>Focus Innovation</h3>
            <p class="muted">Édition août 2024</p>
            <p>Prix : 1 000 FCFA</p>
            <div class="card-actions">
                <a class="btn btn-small" href="/boutique/numero">Détails</a>
                <button class="btn btn-small btn-primary" type="button">Acheter</button>
            </div>
        </article>
    </div>
</section>

<script>
const homeGrid = document.getElementById('home-article-grid');
const homeAlert = document.getElementById('home-article-alert');
const homeRubriquesGrid = document.getElementById('home-rubriques-grid');
const homeRubriquesAlert = document.getElementById('home-rubriques-alert');

function formatCard(article) {
    const card = document.createElement('article');
    card.className = 'card';
    const tag = document.createElement('span');
    tag.className = 'tag';
    tag.textContent = article.category_name ?? 'Rubrique';
    const title = document.createElement('h3');
    title.textContent = article.title ?? 'Article';
    const summary = document.createElement('p');
    summary.textContent = article.summary ?? 'Découvrez cet article.';
    const actions = document.createElement('div');
    actions.className = 'card-actions';
    const link = document.createElement('a');
    link.className = 'btn btn-small';
    link.href = article.slug ? `/article/${article.slug}` : '/article';
    link.textContent = 'Lire l\'article';
    const likeButton = document.createElement('button');
    likeButton.className = 'btn btn-small btn-outline js-like';
    likeButton.type = 'button';
    likeButton.dataset.articleId = article.id ?? '';
    likeButton.textContent = `Like (${article.likes_count ?? 0})`;
    actions.append(link, likeButton);
    card.append(tag, title, summary, actions);
    return card;
}

function renderRubrique(category) {
    const link = document.createElement('a');
    link.className = 'pill';
    link.href = `/rubriques/${category.slug ?? ''}`;
    const name = category.name ?? 'Rubrique';
    const count = category.article_count;
    link.textContent = Number.isInteger(count) ? `${name} (${count})` : name;
    return link;
}

function loadHomeRubriques() {
    homeRubriquesAlert.classList.add('d-none');
    window.apiFetch('/api/categories?limit=12&with_counts=1')
        .then((payload) => {
            if (!payload.ok) {
                throw new Error(window.getApiErrorMessage(payload, 'Impossible de charger les rubriques.'));
            }
            const categories = payload.data?.data ?? payload.data ?? [];
            homeRubriquesGrid.innerHTML = '';
            if (categories.length === 0) {
                homeRubriquesGrid.innerHTML = '<p class="muted">Aucune rubrique disponible.</p>';
                return;
            }
            categories.forEach(category => {
                homeRubriquesGrid.appendChild(renderRubrique(category));
            });
        })
        .catch(error => {
            homeRubriquesAlert.textContent = error.message;
            homeRubriquesAlert.classList.remove('d-none');
        });
}

function loadHomeArticles() {
    homeAlert.classList.add('d-none');
    window.apiFetch('/api/articles?limit=6&page=1')
        .then((payload) => {
            if (!payload.ok) {
                throw new Error(window.getApiErrorMessage(payload, 'Impossible de charger les articles.'));
            }
            const articles = payload.data?.data ?? payload.data ?? [];
            homeGrid.innerHTML = '';
            if (articles.length === 0) {
                homeGrid.innerHTML = '<p class="muted">Aucun article publié pour le moment.</p>';
                return;
            }
            articles.forEach(article => {
                homeGrid.appendChild(formatCard(article));
            });
        })
        .catch(error => {
            homeAlert.textContent = error.message;
            homeAlert.classList.remove('d-none');
        });
}

homeGrid.addEventListener('click', event => {
    const target = event.target;
    if (!target.classList.contains('js-like')) {
        return;
    }
    const articleId = target.dataset.articleId;
    if (!articleId) {
        return;
    }
    window.apiFetch('/api/likes', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ article_id: Number(articleId) }),
        credentials: 'same-origin',
    })
        .then((payload) => {
            if (!payload.ok && payload.status !== 409) {
                throw new Error(window.getApiErrorMessage(payload, 'Impossible d\'ajouter le like.'));
            }
            return window.apiFetch(`/api/likes?article_id=${articleId}`, { credentials: 'same-origin' });
        })
        .then((payload) => {
            const data = payload.data?.data ?? payload.data ?? {};
            const count = data.count ?? 0;
            target.textContent = `Like (${count})`;
        })
        .catch(() => {
            homeAlert.textContent = 'Impossible d\'ajouter le like. Connectez-vous si nécessaire.';
            homeAlert.classList.remove('d-none');
        });
});

loadHomeRubriques();
loadHomeArticles();
</script>
