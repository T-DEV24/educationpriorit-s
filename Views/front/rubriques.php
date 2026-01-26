<section class="section">
    <div class="container">
        <h1>Rubriques</h1>
        <p>Parcourez les catégories du journal et découvrez les articles associés.</p>
        <div class="alert alert-danger d-none" id="rubriques-alert" role="alert"></div>
        <div class="pill-grid" id="rubriques-grid"></div>
    </div>
</section>

<script>
const rubriquesGrid = document.getElementById('rubriques-grid');
const rubriquesAlert = document.getElementById('rubriques-alert');

function renderRubrique(category) {
    const link = document.createElement('a');
    link.className = 'pill';
    link.href = `/rubriques/${category.slug ?? ''}`;
    const name = category.name ?? 'Rubrique';
    const count = category.article_count;
    link.textContent = Number.isInteger(count) ? `${name} (${count})` : name;
    return link;
}

function loadRubriques() {
    rubriquesAlert.classList.add('d-none');
    fetch('/api/categories?limit=50&with_counts=1')
        .then(response => {
            if (!response.ok) {
                throw new Error('Impossible de charger les rubriques.');
            }
            return response.json();
        })
        .then(payload => {
            const categories = payload.data ?? [];
            rubriquesGrid.innerHTML = '';
            if (categories.length === 0) {
                rubriquesGrid.innerHTML = '<p class="muted">Aucune rubrique disponible.</p>';
                return;
            }
            categories.forEach(category => {
                rubriquesGrid.appendChild(renderRubrique(category));
            });
        })
        .catch(error => {
            rubriquesAlert.textContent = error.message;
            rubriquesAlert.classList.remove('d-none');
        });
}

loadRubriques();
</script>
