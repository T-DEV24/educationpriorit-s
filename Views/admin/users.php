<section class="container section">
    <div class="section-header">
        <h1 class="headline-md">Utilisateurs</h1>
        <a class="link" href="/admin">Retour dashboard</a>
    </div>

    <?php if (!empty($users)): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= e($user['full_name']) ?></td>
                        <td><?= e($user['email']) ?></td>
                        <td><?= e($user['role_name']) ?></td>
                        <td><?= (int) $user['is_active'] === 1 ? 'Actif' : 'Bloqué' ?></td>
                        <td>
                            <form method="post" action="/admin/users">
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="user_id" value="<?= (int) $user['id'] ?>">
                                <?php if ((int) $user['is_active'] === 1): ?>
                                    <button class="btn btn-small btn-outline" name="action" value="disable" type="submit">Désactiver</button>
                                <?php else: ?>
                                    <button class="btn btn-small" name="action" value="enable" type="submit">Activer</button>
                                <?php endif; ?>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="muted">Aucun utilisateur enregistré.</p>
    <?php endif; ?>
</section>
