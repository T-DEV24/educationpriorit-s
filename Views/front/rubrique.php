<section class="section">
    <div class="container">
        <div class="section-header">
            <div>
                <p class="eyebrow">Rubrique</p>
                <h1><?= htmlspecialchars($rubrique ?? 'Rubrique') ?></h1>
            </div>
            <a class="link" href="/rubriques">Retour aux rubriques</a>
        </div>
        <div class="card-grid">
            <article class="card">
                <h3>Article phare de la rubrique</h3>
                <p>Un aperçu de l'article vedette de cette rubrique.</p>
                <div class="card-actions">
                    <a class="btn btn-small" href="/article">Lire</a>
                    <button class="btn btn-small btn-outline" type="button">Like</button>
                </div>
            </article>
            <article class="card">
                <h3>Deuxième article</h3>
                <p>Résumé rapide pour encourager la lecture.</p>
                <div class="card-actions">
                    <a class="btn btn-small" href="/article">Lire</a>
                    <button class="btn btn-small btn-outline" type="button">Commenter</button>
                </div>
            </article>
        </div>
        <div class="pagination">
            <button class="btn btn-outline" type="button">Précédent</button>
            <span>Page 1 sur 5</span>
            <button class="btn btn-outline" type="button">Suivant</button>
        </div>
    </div>
</section>
