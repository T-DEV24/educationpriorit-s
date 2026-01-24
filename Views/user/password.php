<section class="container section narrow">
    <div class="section-header">
        <h1 class="headline-md">Changer le mot de passe</h1>
        <a class="link" href="/profil">Retour au profil</a>
    </div>

    <form class="form-grid" method="post" action="/mot-de-passe">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <label>
            Mot de passe actuel
            <input type="password" name="current_password" required>
        </label>
        <label>
            Nouveau mot de passe
            <input type="password" name="new_password" required>
        </label>
        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Mettre à jour</button>
        </div>
    </form>
</section>
