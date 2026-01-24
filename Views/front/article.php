<section class="container section">
    <div class="section-header">
        <div>
            <p class="eyebrow"><?= e($article['category_name']) ?></p>
            <h1 class="headline-md"><?= e($article['title']) ?></h1>
            <p class="muted">Par <?= e($article['author_name']) ?> • <?= e($article['published_at'] ?: $article['created_at']) ?></p>
        </div>
        <a class="link" href="/rubrique/<?= e($article['category_slug']) ?>">Retour</a>
    </div>

    <article class="info-card">
        <?php if (!empty($article['image_path'])): ?>
            <img src="<?= e($article['image_path']) ?>" alt="<?= e($article['title']) ?>">
        <?php endif; ?>
        <p><?= nl2br(e($article['content'] ?: $article['summary'])) ?></p>
        <div class="card-actions">
            <?php if (Auth::check()): ?>
                <form method="post" action="/article/<?= e($article['slug']) ?>/like">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <button class="btn btn-small btn-outline" type="submit" <?= $hasLiked ? 'disabled' : '' ?>>
                        <?= $hasLiked ? 'Déjà liké' : 'J\'aime' ?> (<?= (int) $likesCount ?>)
                    </button>
                </form>
            <?php else: ?>
                <a class="btn btn-small btn-outline" href="/connexion">Se connecter pour liker (<?= (int) $likesCount ?>)</a>
            <?php endif; ?>
        </div>
    </article>
</section>

<section class="section alt">
    <div class="container">
        <h2 class="headline-md">Commentaires</h2>
        <?php if (!empty($comments)): ?>
            <?php foreach ($comments as $comment): ?>
                <div class="comment">
                    <strong><?= e($comment['full_name']) ?></strong>
                    <p class="muted"><?= e($comment['created_at']) ?></p>
                    <p><?= nl2br(e($comment['content'])) ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="muted">Aucun commentaire validé pour le moment.</p>
        <?php endif; ?>

        <div class="info-card">
            <h3 class="headline">Laisser un commentaire</h3>
            <?php if (Auth::check()): ?>
                <form class="comment-form" method="post" action="/article/<?= e($article['slug']) ?>/comment">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <textarea name="content" required placeholder="Votre commentaire..."></textarea>
                    <button class="btn btn-primary" type="submit">Envoyer</button>
                </form>
            <?php else: ?>
                <p class="muted">Connectez-vous pour commenter cet article.</p>
                <a class="btn btn-primary" href="/connexion">Se connecter</a>
            <?php endif; ?>
        </div>
    </div>
</section>
