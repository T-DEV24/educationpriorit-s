<section class="container section">
    <div class="section-header">
        <h1 class="headline-md">Rubriques</h1>
        <a class="link" href="/recherche">Rechercher un article</a>
    </div>

    <?php if (!empty($categories)): ?>
        <div class="card-grid">
            <?php foreach ($categories as $category): ?>
                <article class="card">
                    <h3><?= e($category['name']) ?></h3>
                    <p class="muted"><?= (int) $category['article_count'] ?> article(s) publiés</p>
                    <div class="card-actions">
                        <a class="btn btn-small" href="/rubrique/<?= e($category['slug']) ?>">Voir la rubrique</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="muted">Aucune rubrique enregistrée.</p>
    <?php endif; ?>
</section>
