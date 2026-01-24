<section class="container section">
    <div class="section-header">
        <div>
            <h1 class="headline-md">Boutique PDF</h1>
            <p class="muted">Achetez et téléchargez les numéros PDF du journal EducationPriorité.</p>
        </div>
        <a class="link" href="/profil/achats">Mes achats</a>
    </div>

    <?php if (!empty($editions)): ?>
        <div class="card-grid">
            <?php foreach ($editions as $edition): ?>
                <article class="card">
                    <h3><?= e($edition['title']) ?></h3>
                    <p class="muted"><?= e($edition['published_at'] ?: 'Édition récente') ?></p>
                    <p><?= e($edition['description'] ?: 'Numéro complet du journal EducationPriorité.') ?></p>
                    <p><strong><?= e($edition['price']) ?> FCFA</strong></p>
                    <div class="card-actions">
                        <a class="btn btn-small" href="/boutique/<?= e($edition['slug']) ?>">Détails</a>
                        <a class="btn btn-small btn-primary" href="/boutique/<?= e($edition['slug']) ?>">Acheter</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="muted">Aucun PDF disponible pour le moment.</p>
    <?php endif; ?>
</section>
