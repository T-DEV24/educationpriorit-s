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

function loadHomeArticles() {
    homeAlert.classList.add('d-none');
    fetch('/api/articles?limit=6&page=1')
        .then(response => {
            if (!response.ok) {
                throw new Error('Impossible de charger les articles.');
            }
            return response.json();
        })
        .then(payload => {
            const articles = payload.data ?? [];
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
            homeAlert.textContent = 'Impossible d\'ajouter le like. Connectez-vous si nécessaire.';
            homeAlert.classList.remove('d-none');
        });
});

loadHomeArticles();
</script>
