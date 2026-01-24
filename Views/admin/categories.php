<section class="container section">
    <div class="section-header">
        <h1 class="headline-md">Rubriques</h1>
        <a class="link" href="/admin">Retour dashboard</a>
    </div>

    <div class="info-card">
        <h3 class="headline">Ajouter une rubrique</h3>
        <form class="form-grid" method="post" action="/admin/categories">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <label>
                Nom
                <input type="text" name="name" required>
            </label>
            <label>
                Slug
                <input type="text" name="slug" required>
            </label>
            <div class="form-actions">
                <button class="btn btn-primary" type="submit">Ajouter</button>
            </div>
        </form>
    </div>

    <?php if (!empty($categories)): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Rubrique</th>
                    <th>Slug</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?= e($category['name']) ?></td>
                        <td><?= e($category['slug']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="muted">Aucune rubrique disponible.</p>
    <?php endif; ?>
</section>
