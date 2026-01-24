<section class="container section">
    <div class="section-header">
        <h1 class="headline-md">Mes commentaires</h1>
        <a class="link" href="/profil">Retour au profil</a>
    </div>

    <?php if (!empty($comments)): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Article</th>
                    <th>Commentaire</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comments as $comment): ?>
                    <tr>
                        <td><?= e($comment['title']) ?></td>
                        <td><?= e($comment['content']) ?></td>
                        <td><?= (int) $comment['is_approved'] === 1 ? 'Validé' : 'En attente' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="muted">Vous n'avez pas encore commenté.</p>
    <?php endif; ?>
</section>
