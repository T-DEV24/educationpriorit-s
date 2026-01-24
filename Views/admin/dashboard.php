<section class="container section">
    <div class="section-header">
        <h1 class="headline-md">Tableau de bord</h1>
        <a class="link" href="/">Voir le site</a>
    </div>

    <div class="card-grid">
        <article class="card">
            <h3>Articles</h3>
            <p><?= (int) ($stats['articles'] ?? 0) ?> articles</p>
            <a class="btn btn-small" href="/admin/articles">Gérer</a>
        </article>
        <article class="card">
            <h3>Utilisateurs</h3>
            <p><?= (int) ($stats['users'] ?? 0) ?> comptes</p>
            <a class="btn btn-small" href="/admin/users">Gérer</a>
        </article>
        <article class="card">
            <h3>Commentaires</h3>
            <p><?= (int) ($stats['comments'] ?? 0) ?> commentaires</p>
            <a class="btn btn-small" href="/admin/comments">Modérer</a>
        </article>
        <article class="card">
            <h3>Ventes PDF</h3>
            <p><?= (int) ($stats['orders'] ?? 0) ?> commandes</p>
            <a class="btn btn-small" href="/admin/orders">Voir</a>
        </article>
    </div>
</section>
