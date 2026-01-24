<section class="container section">
    <div class="section-header">
        <h1 class="headline-md">Commandes PDF</h1>
        <a class="link" href="/admin">Retour dashboard</a>
    </div>

    <?php if (!empty($orders)): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Client</th>
                    <th>PDF</th>
                    <th>Montant</th>
                    <th>Statut</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= e($order['full_name']) ?></td>
                        <td><?= e($order['title']) ?></td>
                        <td><?= e($order['amount']) ?> FCFA</td>
                        <td><?= e($order['status']) ?></td>
                        <td><?= e($order['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="muted">Aucune commande enregistrée.</p>
    <?php endif; ?>
</section>
