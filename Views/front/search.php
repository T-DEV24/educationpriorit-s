<section class="section">
    <div class="container">
        <h1>Recherche</h1>
        <form class="search-bar" id="search-form">
            <input type="search" id="search-input" placeholder="Rechercher un article, un auteur, un tag...">
            <button class="btn btn-primary" type="submit">Rechercher</button>
        </form>
        <div class="alert alert-danger d-none" id="search-alert" role="alert"></div>
        <div class="card-grid" id="search-results"></div>
        <div class="pagination">
            <button class="btn btn-outline" type="button" id="search-prev">Précédent</button>
            <span id="search-page">Page 1</span>
            <button class="btn btn-outline" type="button" id="search-next">Suivant</button>
        </div>
    </div>
</section>

<script>
const searchForm = document.getElementById('search-form');
const searchInput = document.getElementById('search-input');
const searchAlert = document.getElementById('search-alert');
const searchResults = document.getElementById('search-results');
const searchPrev = document.getElementById('search-prev');
const searchNext = document.getElementById('search-next');
const searchPage = document.getElementById('search-page');
let searchCurrentPage = 1;
let searchLastPage = 1;

function renderSearchCard(article) {
    const card = document.createElement('article');
    card.className = 'card';
    const tag = document.createElement('span');
    tag.className = 'tag';
    tag.textContent = article.category_name ?? 'Rubrique';
    const title = document.createElement('h3');
    title.textContent = article.title ?? 'Article';
    const summary = document.createElement('p');
    summary.textContent = article.summary ?? 'Résultat de recherche.';
    const link = document.createElement('a');
    link.className = 'btn btn-small';
    link.href = article.slug ? `/article/${article.slug}` : '/article';
    link.textContent = 'Lire';
    card.append(tag, title, summary, link);
    return card;
}

function updateQueryParam(value) {
    const params = new URLSearchParams(window.location.search);
    if (value) {
        params.set('q', value);
    } else {
        params.delete('q');
    }
    const newUrl = `${window.location.pathname}?${params.toString()}`;
    window.history.replaceState({}, '', newUrl);
}

function loadSearchResults() {
    searchAlert.classList.add('d-none');
    const query = searchInput.value.trim();
    const params = new URLSearchParams({
        page: String(searchCurrentPage),
        limit: '9',
        search: query,
    });
    window.apiFetch(`/api/articles?${params.toString()}`)
        .then((payload) => {
            if (!payload.ok) {
                throw new Error(window.getApiErrorMessage(payload, 'Impossible de charger les résultats.'));
            }
            const articles = payload.data?.data ?? payload.data ?? [];
            const pagination = payload.data?.pagination ?? payload.pagination ?? {};
            searchLastPage = pagination.pages ?? 1;
            searchPage.textContent = `Page ${pagination.page ?? searchCurrentPage} / ${searchLastPage}`;
            searchResults.innerHTML = '';
            if (articles.length === 0) {
                searchResults.innerHTML = '<p class="muted">Aucun résultat pour cette recherche.</p>';
                return;
            }
            articles.forEach(article => searchResults.appendChild(renderSearchCard(article)));
            searchPrev.disabled = searchCurrentPage <= 1;
            searchNext.disabled = searchCurrentPage >= searchLastPage;
        })
        .catch(error => {
            searchAlert.textContent = error.message;
            searchAlert.classList.remove('d-none');
        });
}

searchForm.addEventListener('submit', event => {
    event.preventDefault();
    searchCurrentPage = 1;
    updateQueryParam(searchInput.value.trim());
    loadSearchResults();
});

searchPrev.addEventListener('click', () => {
    if (searchCurrentPage > 1) {
        searchCurrentPage -= 1;
        loadSearchResults();
    }
});

searchNext.addEventListener('click', () => {
    if (searchCurrentPage < searchLastPage) {
        searchCurrentPage += 1;
        loadSearchResults();
    }
});

const initialQuery = new URLSearchParams(window.location.search).get('q') ?? '';
searchInput.value = initialQuery;
loadSearchResults();
</script>
