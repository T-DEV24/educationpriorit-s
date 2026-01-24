<section class="container section">
    <div class="section-header">
        <h1 class="headline-md">Commentaires</h1>
        <a class="link" href="/admin">Retour dashboard</a>
    </div>

    <?php if (!empty($comments)): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Auteur</th>
                    <th>Article</th>
                    <th>Commentaire</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comments as $comment): ?>
                    <tr>
                        <td><?= e($comment['full_name']) ?></td>
                        <td><?= e($comment['title']) ?></td>
                        <td><?= e($comment['content']) ?></td>
                        <td><?= (int) $comment['is_approved'] === 1 ? 'Validé' : 'En attente' ?></td>
                        <td>
                            <form method="post" action="/admin/comments" class="d-flex gap-2">
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="comment_id" value="<?= (int) $comment['id'] ?>">
                                <button class="btn btn-small" name="action" value="approve" type="submit">Valider</button>
                                <button class="btn btn-small btn-outline" name="action" value="delete" type="submit">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="muted">Aucun commentaire à modérer.</p>
    <?php endif; ?>
</section>
