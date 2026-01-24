<section class="container section">
    <div class="section-header">
        <div>
            <h1 class="headline-md">Recherche</h1>
            <p class="muted">Recherchez dans les articles EducationPriorité.</p>
        </div>
    </div>

    <form class="search-bar" method="get" action="/recherche">
        <input type="search" name="q" placeholder="Rechercher un article" value="<?= e($term ?? '') ?>">
        <button class="btn btn-primary" type="submit">Rechercher</button>
    </form>

    <?php if (!empty($term)): ?>
        <h2 class="headline">Résultats pour "<?= e($term) ?>"</h2>
        <?php if (!empty($results)): ?>
            <div class="card-grid">
                <?php foreach ($results as $article): ?>
                    <article class="card">
                        <span class="tag"><?= e($article['category_name']) ?></span>
                        <h3><?= e($article['title']) ?></h3>
                        <p><?= e($article['summary'] ?: 'Article EducationPriorité.') ?></p>
                        <a class="btn btn-small" href="/article/<?= e($article['slug']) ?>">Lire l'article</a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="muted">Aucun résultat trouvé.</p>
        <?php endif; ?>
    <?php endif; ?>
</section>
