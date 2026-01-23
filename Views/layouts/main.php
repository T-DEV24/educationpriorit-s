<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? htmlspecialchars($title) . ' | ' : '' ?>EducationPriorité</title>
    <meta name="description" content="EducationPriorité : journal d'information éducative, articles, dossiers et boutique PDF.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/Asset/css/styles.css">
</head>
<body>
    <header class="site-header">
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container header-inner">
                <a class="logo navbar-brand" href="/">
                    <img src="/Asset/logo.svg" alt="Logo EducationPriorité">
                    <span>EducationPriorité</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Basculer la navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="mainNavbar">
                    <nav class="main-nav navbar-nav me-auto mb-2 mb-lg-0">
                        <a class="nav-link" href="/">Accueil</a>
                        <a class="nav-link" href="/rubriques">Rubriques</a>
                        <a class="nav-link" href="/boutique">Journal PDF</a>
                        <a class="nav-link" href="/a-propos">À propos</a>
                        <a class="nav-link" href="/contact">Contact</a>
                    </nav>
                    <div class="header-actions d-flex gap-2">
                        <a class="btn btn-outline" href="/connexion">Connexion</a>
                        <a class="btn btn-primary" href="/inscription">Inscription</a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <?= $content ?? '' ?>
    </main>

    <footer class="site-footer">
        <div class="container footer-inner">
            <div>
                <strong>EducationPriorité</strong>
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
