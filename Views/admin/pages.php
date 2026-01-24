<section class="container section">
    <div class="section-header">
        <h1 class="headline-md">Pages</h1>
        <a class="link" href="/admin">Retour dashboard</a>
    </div>

    <div class="info-card">
        <h3 class="headline">Ajouter une page</h3>
        <form class="form-grid" method="post" action="/admin/pages">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <label>
                Titre
                <input type="text" name="title" required>
            </label>
            <label>
                Slug
                <input type="text" name="slug" required>
            </label>
            <label class="full">
                Contenu
                <textarea name="content"></textarea>
            </label>
            <label>
                Publication
                <select name="is_published" class="input">
                    <option value="1">Publié</option>
                    <option value="0">Brouillon</option>
                </select>
            </label>
            <div class="form-actions">
                <button class="btn btn-primary" type="submit">Ajouter</button>
            </div>
        </form>
    </div>

    <?php if (!empty($pages)): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Slug</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pages as $page): ?>
                    <tr>
                        <td><?= e($page['title']) ?></td>
                        <td><?= e($page['slug']) ?></td>
                        <td><?= (int) $page['is_published'] === 1 ? 'Publié' : 'Brouillon' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="muted">Aucune page créée.</p>
    <?php endif; ?>
</section>
