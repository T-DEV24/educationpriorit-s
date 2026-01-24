<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? htmlspecialchars($title) . ' | ' : '' ?>EducationPriorité</title>
    <meta name="description" content="EducationPriorité : journal d'information éducative, articles, dossiers et boutique PDF.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title><?= isset($title) ? htmlspecialchars($title) . ' | ' : '' ?>Journal Éducatif</title>
    <meta name="description" content="Journal d'information éducative : actualités, dossiers, enquêtes et boutique PDF.">
    <link rel="stylesheet" href="/Asset/css/styles.css">
</head>
<body>
    <header class="site-header">
        <div class="container header-inner">
            <a class="logo" href="/">
                <img src="/Asset/logo.svg" alt="Logo EducationPriorité">
                <span>EducationPriorité</span>
            </a>
            <a class="logo" href="/">Journal Éducatif</a>
            <nav class="main-nav">
                <a href="/">Accueil</a>
                <a href="/rubriques">Rubriques</a>
                <a href="/boutique">Journal PDF</a>
                <a href="/a-propos">À propos</a>
                <a href="/contact">Contact</a>
            </nav>
            <div class="header-actions d-flex gap-2 flex-wrap">
            <div class="header-actions">
                <a class="btn btn-outline" href="/connexion">Connexion</a>
                <a class="btn btn-primary" href="/inscription">Inscription</a>
            </div>
        </div>
    </header>

    <main>
        <?= $content ?? '' ?>
    </main>

    <footer class="site-footer">
        <div class="container footer-inner">
            <div>
                <strong>EducationPriorité</strong>
                <strong>Journal Éducatif</strong>
                <p>Média numérique pour la communauté éducative.</p>
            </div>
            <div class="footer-links">
                <a href="/a-propos">À propos</a>
                <a href="/contact">Contact</a>
                <a href="/connexion">Espace utilisateur</a>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
