<section class="container section narrow">
    <h1 class="headline-md">Inscription</h1>
    <p class="muted">Créez votre compte pour liker, commenter et acheter des PDF.</p>
    <form class="form-grid" method="post" action="/inscription">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <label>
            Nom complet
            <input type="text" name="full_name" required placeholder="Votre nom">
        </label>
        <label>
            Email
            <input type="email" name="email" required placeholder="vous@email.com">
        </label>
        <label>
            Mot de passe
            <input type="password" name="password" required placeholder="••••••••">
        </label>
        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Créer mon compte</button>
            <a class="btn btn-outline" href="/connexion">J'ai déjà un compte</a>
        </div>
    </form>
</section>
