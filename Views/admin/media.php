<section class="container section">
    <div class="section-header">
        <h1 class="headline-md">Médias</h1>
        <a class="link" href="/admin">Retour dashboard</a>
    </div>

    <div class="info-card">
        <h3 class="headline">Ajouter un média</h3>
        <form class="form-grid" method="post" action="/admin/media">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <label>
                Chemin du fichier
                <input type="text" name="file_path" required>
            </label>
            <label>
                Texte alternatif
                <input type="text" name="alt_text">
            </label>
            <div class="form-actions">
                <button class="btn btn-primary" type="submit">Ajouter</button>
            </div>
        </form>
    </div>

    <?php if (!empty($media)): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Fichier</th>
                    <th>Alt</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($media as $item): ?>
                    <tr>
                        <td><?= e($item['file_path']) ?></td>
                        <td><?= e($item['alt_text']) ?></td>
                        <td><?= e($item['uploaded_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="muted">Aucun média enregistré.</p>
    <?php endif; ?>
</section>
