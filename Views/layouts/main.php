<?php
$user = Auth::user();
$flashError = flash('error');
$flashSuccess = flash('success');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? e($title) . ' | ' : '' ?>EducationPriorité</title>
    <meta name="description" content="EducationPriorité : journal d'information éducative, articles, dossiers et boutique PDF.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/Asset/css/styles.css">
</head>
<body>
    <header class="site-header">
        <div class="container header-inner">
            <a class="logo" href="/">
                <img src="/Asset/logo.svg" alt="Logo EducationPriorité">
                <span>EducationPriorité</span>
            </a>
            <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="main-nav">
                <span></span>
            </button>
            <nav class="main-nav" id="main-nav">
                <a href="/">Accueil</a>
                <a href="/rubriques">Rubriques</a>
                <a href="/boutique">Journal PDF</a>
                <a href="/a-propos">À propos</a>
                <a href="/contact">Contact</a>
                <?php if ($user && ($user['role_name'] ?? '') === 'Admin'): ?>
                    <a href="/admin">Admin</a>
                <?php endif; ?>
            </nav>
            <div class="header-actions d-flex gap-2 flex-wrap">
                <?php if ($user): ?>
                    <a class="btn btn-outline" href="/profil">Mon compte</a>
                    <a class="btn btn-primary" href="/deconnexion">Déconnexion</a>
                <?php else: ?>
                    <a class="btn btn-outline" href="/connexion">Connexion</a>
                    <a class="btn btn-primary" href="/inscription">Inscription</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main>
        <?php if ($flashError): ?>
            <div class="container mt-3">
                <div class="alert alert-danger" role="alert"><?= e($flashError) ?></div>
            </div>
        <?php endif; ?>
        <?php if ($flashSuccess): ?>
            <div class="container mt-3">
                <div class="alert alert-success" role="alert"><?= e($flashSuccess) ?></div>
            </div>
        <?php endif; ?>
        <?= $content ?? '' ?>
    </main>

    <footer class="site-footer">
        <div class="container footer-inner">
            <div class="footer-brand">
                <div class="logo">
                    <img src="/Asset/logo.svg" alt="Logo EducationPriorité">
                    <span>EducationPriorité</span>
                </div>
                <p>Média numérique pour la communauté éducative. Actualités, dossiers, enquêtes et PDF premium.</p>
                <div class="footer-socials">
                    <a href="#" aria-label="Facebook">Facebook</a>
                    <a href="#" aria-label="WhatsApp">WhatsApp</a>
                    <a href="#" aria-label="LinkedIn">LinkedIn</a>
                </div>
            </div>
            <div class="footer-col">
                <span class="footer-title">Navigation</span>
                <div class="footer-links">
                    <a href="/rubriques">Rubriques</a>
                    <a href="/boutique">Journal PDF</a>
                    <a href="/recherche">Recherche</a>
                    <a href="/contact">Contact</a>
                </div>
            </div>
            <div class="footer-col">
                <span class="footer-title">Compte</span>
                <div class="footer-links">
                    <?php if ($user): ?>
                        <a href="/profil">Mon profil</a>
                        <a href="/profil/achats">Mes achats</a>
                        <a href="/profil/commentaires">Mes commentaires</a>
                    <?php else: ?>
                        <a href="/connexion">Connexion</a>
                        <a href="/inscription">Inscription</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="footer-col">
                <span class="footer-title">Support</span>
                <div class="footer-links">
                    <a href="/a-propos">À propos</a>
                    <a href="/contact">Service client</a>
                    <a href="#">Mentions légales</a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container footer-bottom-inner">
                <span>© <?= date('Y') ?> EducationPriorité. Tous droits réservés.</span>
                <button class="scroll-top" type="button" data-scroll-top>Haut de page</button>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/Asset/js/main.js"></script>
</body>
</html>
