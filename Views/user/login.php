<section class="container section narrow">
    <h1 class="headline-md">Connexion</h1>
    <p class="muted">Connectez-vous pour commenter et acheter les PDF.</p>
    <form class="form-grid" method="post" action="/connexion">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <label>
            Email
            <input type="email" name="email" required placeholder="vous@email.com">
        </label>
        <label>
            Mot de passe
            <input type="password" name="password" required placeholder="••••••••">
        </label>
        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Se connecter</button>
            <a class="btn btn-outline" href="/inscription">Créer un compte</a>
        </div>
    </form>
</section>
