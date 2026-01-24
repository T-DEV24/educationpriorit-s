<section class="container section">
    <div class="section-header">
        <div>
            <p class="eyebrow">Rubrique</p>
            <h1 class="headline-md"><?= e($category['name']) ?></h1>
        </div>
        <a class="link" href="/rubriques">Toutes les rubriques</a>
    </div>

    <?php if (!empty($articles)): ?>
        <div class="card-grid">
            <?php foreach ($articles as $article): ?>
                <article class="card">
                    <span class="tag"><?= e($article['category_name']) ?></span>
                    <h3><?= e($article['title']) ?></h3>
                    <p><?= e($article['summary'] ?: 'Article disponible dans cette rubrique.') ?></p>
                    <div class="card-actions">
                        <a class="btn btn-small" href="/article/<?= e($article['slug']) ?>">Lire l'article</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="muted">Aucun article publié dans cette rubrique.</p>
    <?php endif; ?>
</section>
