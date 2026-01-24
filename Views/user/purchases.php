<section class="container section">
    <div class="section-header">
        <h1 class="headline-md">Mes achats</h1>
        <a class="link" href="/profil">Retour au profil</a>
    </div>

    <?php if (!empty($orders)): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>PDF</th>
                    <th>Montant</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th>Téléchargement</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= e($order['title']) ?></td>
                        <td><?= e($order['amount']) ?> FCFA</td>
                        <td><?= e($order['status']) ?></td>
                        <td><?= e($order['created_at']) ?></td>
                        <td>
                            <?php if ($order['status'] === 'paid'): ?>
                                <a class="btn btn-small" href="/telechargement/<?= (int) $order['pdf_id'] ?>">Télécharger</a>
                            <?php else: ?>
                                <span class="muted">Indisponible</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="muted">Aucun achat enregistré.</p>
    <?php endif; ?>
</section>
