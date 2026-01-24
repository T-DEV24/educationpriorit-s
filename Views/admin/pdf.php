<section class="container section">
    <div class="section-header">
        <h1 class="headline-md">Journal PDF</h1>
        <a class="link" href="/admin">Retour dashboard</a>
    </div>

    <div class="info-card">
        <h3 class="headline">Ajouter un PDF</h3>
        <form class="form-grid" method="post" action="/admin/pdf">
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
                Prix (FCFA)
                <input type="number" name="price" required>
            </label>
            <label>
                Date de publication
                <input type="date" name="published_at">
            </label>
            <label class="full">
                Description
                <textarea name="description"></textarea>
            </label>
            <label>
                Couverture (URL)
                <input type="text" name="cover_path">
            </label>
            <label>
                Fichier PDF (chemin serveur)
                <input type="text" name="pdf_path" required>
            </label>
            <div class="form-actions">
                <button class="btn btn-primary" type="submit">Ajouter</button>
            </div>
        </form>
    </div>

    <?php if (!empty($editions)): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Prix</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($editions as $edition): ?>
                    <tr>
                        <td><?= e($edition['title']) ?></td>
                        <td><?= e($edition['price']) ?> FCFA</td>
                        <td><?= e($edition['published_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="muted">Aucun PDF enregistré.</p>
    <?php endif; ?>
</section>
