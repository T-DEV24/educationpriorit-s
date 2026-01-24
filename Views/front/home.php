<section class="hero">
    <div class="container hero-grid">
        <div>
            <p class="eyebrow">Journal du jour</p>
            <?php if (!empty($featuredEdition)): ?>
                <h1 class="headline-xl"><?= e($featuredEdition['title']) ?></h1>
                <p class="headline"><?= e($featuredEdition['description'] ?: 'Découvrez les nouveautés éducatives du jour.') ?></p>
                <p>Accédez librement aux titres du jour, commentez les articles et achetez les numéros PDF en un clic.</p>
            <?php else: ?>
                <h1 class="headline-xl">Bienvenue sur EducationPriorité</h1>
                <p class="headline">Toute l'actualité éducative, les dossiers et les enquêtes.</p>
                <p>Publiez, interagissez et achetez les PDF du journal éducatif.</p>
            <?php endif; ?>
            <div class="hero-actions">
                <a class="btn btn-primary" href="/rubriques">Explorer les rubriques</a>
                <a class="btn btn-outline" href="/boutique">Voir la boutique PDF</a>
            </div>
        </div>
        <div class="hero-card">
            <h2 class="headline-md">Édition du jour</h2>
            <?php if (!empty($featuredEdition)): ?>
                <p class="muted"><?= e($featuredEdition['published_at'] ?: 'Nouvelle édition') ?> • Hebdo</p>
                <ul class="checklist">
                    <li><?= e($featuredEdition['description'] ?: 'Focus sur les actions éducatives') ?></li>
                    <li>Analyses, interviews et reportages</li>
                    <li>PDF premium disponible</li>
                </ul>
                <div class="hero-actions">
                    <a class="btn btn-primary" href="/boutique/<?= e($featuredEdition['slug']) ?>">Lire le journal</a>
                    <a class="btn btn-ghost" href="/boutique/<?= e($featuredEdition['slug']) ?>">Acheter le PDF</a>
                </div>
            <?php else: ?>
                <p class="muted">Aucune édition disponible pour le moment.</p>
                <p>Ajoutez un numéro PDF depuis l'admin pour l'afficher ici.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="container section">
    <div class="section-header">
        <h2 class="headline-md">À la une</h2>
        <a class="link" href="/rubriques">Tout voir</a>
    </div>
    <?php if (!empty($articles)): ?>
        <div class="card-grid">
            <?php foreach ($articles as $article): ?>
                <article class="card">
                    <span class="tag"><?= e($article['category_name']) ?></span>
                    <h3><?= e($article['title']) ?></h3>
                    <p><?= e($article['summary'] ?: 'Découvrez cet article de la rédaction EducationPriorité.') ?></p>
                    <div class="card-actions">
                        <a class="btn btn-small" href="/article/<?= e($article['slug']) ?>">Lire l'article</a>
                        <span class="btn btn-small btn-outline"><?= (int) Article::likesCount((int) $article['id']) ?> likes</span>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="muted">Aucun article publié pour le moment.</p>
    <?php endif; ?>
</section>

<section class="section alt">
    <div class="container section-header">
        <h2>Journal PDF en vente</h2>
        <a class="link" href="/boutique">Voir le catalogue</a>
    </div>
    <?php if (!empty($editions)): ?>
        <div class="container card-grid">
            <?php foreach (array_slice($editions, 0, 2) as $edition): ?>
                <article class="card">
                    <h3><?= e($edition['title']) ?></h3>
                    <p class="muted"><?= e($edition['published_at'] ?: 'Édition récente') ?></p>
                    <p>Prix : <?= e($edition['price']) ?> FCFA</p>
                    <div class="card-actions">
                        <a class="btn btn-small" href="/boutique/<?= e($edition['slug']) ?>">Détails</a>
                        <a class="btn btn-small btn-primary" href="/boutique/<?= e($edition['slug']) ?>">Acheter</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="container">
            <p class="muted">Aucun PDF en vente pour le moment.</p>
        </div>
    <?php endif; ?>
</section>
