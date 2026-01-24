<section class="container section">
    <div class="section-header">
        <div>
            <h1 class="headline-md">Mon profil</h1>
            <p class="muted">Bienvenue <?= e($user['full_name'] ?? '') ?>.</p>
        </div>
        <a class="link" href="/deconnexion">Déconnexion</a>
    </div>

    <div class="card-grid">
        <article class="card">
            <h3>Mes commentaires</h3>
            <p>Suivez les commentaires publiés sur les articles.</p>
            <a class="btn btn-small" href="/profil/commentaires">Voir mes commentaires</a>
        </article>
        <article class="card">
            <h3>Mes achats PDF</h3>
            <p>Retrouvez vos achats et téléchargez vos PDF.</p>
            <a class="btn btn-small" href="/profil/achats">Voir mes achats</a>
        </article>
        <article class="card">
            <h3>Mot de passe</h3>
            <p>Mettre à jour le mot de passe de votre compte.</p>
            <a class="btn btn-small" href="/mot-de-passe">Changer le mot de passe</a>
        </article>
    </div>
</section>
