<section class="container section">
    <div class="section-header">
        <div>
            <p class="eyebrow">Journal PDF</p>
            <h1 class="headline-md"><?= e($edition['title']) ?></h1>
            <p class="muted"><?= e($edition['published_at'] ?: 'Édition récente') ?></p>
        </div>
        <a class="link" href="/boutique">Retour au catalogue</a>
    </div>

    <div class="info-card">
        <p><?= nl2br(e($edition['description'] ?: 'Numéro complet du journal EducationPriorité.')) ?></p>
        <p><strong>Prix : <?= e($edition['price']) ?> FCFA</strong></p>
    </div>

    <?php if ($hasAccess): ?>
        <div class="info-card">
            <h3 class="headline">Téléchargement</h3>
            <p class="muted">Votre paiement a été validé. Téléchargez votre PDF.</p>
            <a class="btn btn-primary" href="/telechargement/<?= (int) $edition['id'] ?>">Télécharger le PDF</a>
        </div>
    <?php else: ?>
        <div class="info-card">
            <h3 class="headline">Acheter ce numéro</h3>
            <?php if (Auth::check()): ?>
                <form method="post" action="/boutique/<?= e($edition['slug']) ?>/acheter">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <label class="form-hint" for="provider">Moyen de paiement</label>
                    <select class="input" id="provider" name="provider">
                        <option value="mtn_momo">MTN MoMo</option>
                        <option value="orange_money">Orange Money</option>
                    </select>
                    <div class="form-actions">
                        <button class="btn btn-primary" type="submit">Payer maintenant</button>
                    </div>
                    <p class="muted">Après confirmation, le PDF sera disponible dans votre compte.</p>
                </form>
            <?php else: ?>
                <p class="muted">Connectez-vous pour finaliser votre achat.</p>
                <a class="btn btn-primary" href="/connexion">Se connecter</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>
