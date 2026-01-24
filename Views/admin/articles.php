<section class="container section">
    <div class="section-header">
        <h1 class="headline-md">Articles</h1>
        <a class="link" href="/admin">Retour dashboard</a>
    </div>

    <div class="info-card">
        <h3 class="headline">Nouvel article</h3>
        <form class="form-grid" method="post" action="/admin/articles">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <label>
                Titre
                <input type="text" name="title" required>
            </label>
            <label>
                Slug
                <input type="text" name="slug" required>
            </label>
            <label>
                Rubrique
                <select name="category_id" class="input" required>
                    <option value="">Choisir</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= (int) $category['id'] ?>"><?= e($category['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label class="full">
                Résumé
                <textarea name="summary"></textarea>
            </label>
            <label class="full">
                Contenu
                <textarea name="content"></textarea>
            </label>
            <label>
                Image (URL)
                <input type="text" name="image_path">
            </label>
            <label>
                Statut
                <select name="status" class="input">
                    <option value="draft">Brouillon</option>
                    <option value="published">Publié</option>
                </select>
            </label>
            <label>
                Date de publication
                <input type="datetime-local" name="published_at">
            </label>
            <div class="form-actions">
                <button class="btn btn-primary" type="submit">Enregistrer</button>
            </div>
        </form>
    </div>

    <h2 class="headline-md mt-4">Liste des articles</h2>
    <?php if (!empty($articles)): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Rubrique</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($articles as $article): ?>
                    <tr>
                        <td><?= e($article['title']) ?></td>
                        <td><?= e($article['category_name']) ?></td>
                        <td><?= e($article['status']) ?></td>
                        <td>
                            <form method="post" action="/admin/articles/<?= (int) $article['id'] ?>/status">
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                <select name="status" class="input">
                                    <option value="draft" <?= $article['status'] === 'draft' ? 'selected' : '' ?>>Brouillon</option>
                                    <option value="published" <?= $article['status'] === 'published' ? 'selected' : '' ?>>Publié</option>
                                </select>
                                <button class="btn btn-small" type="submit">Mettre à jour</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="muted">Aucun article enregistré.</p>
    <?php endif; ?>
</section>
