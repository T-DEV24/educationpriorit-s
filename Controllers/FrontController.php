<?php

class FrontController
{
    public function home(): void
    {
        $articles = Article::latest(6);
        $editions = PdfEdition::all();
        $featuredEdition = PdfEdition::latest();
        $categories = Category::all();
        View::render('front/home', [
            'title' => 'Accueil',
            'articles' => $articles,
            'editions' => $editions,
            'featuredEdition' => $featuredEdition,
            'categories' => $categories,
        ]);
    }

    public function rubriques(): void
    {
        $categories = Category::allWithCounts();
        View::render('front/rubriques', [
            'title' => 'Rubriques',
            'categories' => $categories,
        ]);
    }

    public function rubrique(array $params): void
    {
        $category = Category::findBySlug($params['slug']);
        if (!$category) {
            http_response_code(404);
            View::render('404', ['title' => 'Rubrique introuvable']);
            return;
        }
        $articles = Article::byCategory((int) $category['id']);
        View::render('front/rubrique', [
            'title' => $category['name'],
            'category' => $category,
            'articles' => $articles,
        ]);
    }

    public function article(array $params): void
    {
        $article = Article::findBySlug($params['slug']);
        if (!$article) {
            http_response_code(404);
            View::render('404', ['title' => 'Article introuvable']);
            return;
        }
        $comments = Article::comments((int) $article['id']);
        $likesCount = Article::likesCount((int) $article['id']);
        $user = Auth::user();
        $hasLiked = $user ? Article::hasLike((int) $article['id'], (int) $user['id']) : false;

        View::render('front/article', [
            'title' => $article['title'],
            'article' => $article,
            'comments' => $comments,
            'likesCount' => $likesCount,
            'hasLiked' => $hasLiked,
        ]);
    }

    public function comment(array $params): void
    {
        Auth::requireLogin();
        if (!verify_csrf($_POST['csrf_token'] ?? null)) {
            flash('error', 'Jeton CSRF invalide.');
            redirect('/article/' . $params['slug']);
        }
        $article = Article::findBySlug($params['slug']);
        if (!$article) {
            http_response_code(404);
            View::render('404', ['title' => 'Article introuvable']);
            return;
        }
        $content = trim($_POST['content'] ?? '');
        if ($content === '') {
            flash('error', 'Le commentaire ne peut pas être vide.');
            redirect('/article/' . $params['slug']);
        }
        $user = Auth::user();
        Article::addComment((int) $article['id'], (int) $user['id'], $content);
        flash('success', 'Votre commentaire a été envoyé pour modération.');
        redirect('/article/' . $params['slug']);
    }

    public function like(array $params): void
    {
        Auth::requireLogin();
        if (!verify_csrf($_POST['csrf_token'] ?? null)) {
            flash('error', 'Jeton CSRF invalide.');
            redirect('/article/' . $params['slug']);
        }
        $article = Article::findBySlug($params['slug']);
        if (!$article) {
            http_response_code(404);
            View::render('404', ['title' => 'Article introuvable']);
            return;
        }
        $user = Auth::user();
        Article::addLike((int) $article['id'], (int) $user['id']);
        redirect('/article/' . $params['slug']);
    }

    public function boutique(): void
    {
        $editions = PdfEdition::all();
        View::render('front/boutique', [
            'title' => 'Journal PDF',
            'editions' => $editions,
        ]);
    }

    public function boutiqueDetail(array $params): void
    {
        $edition = PdfEdition::findBySlug($params['slug']);
        if (!$edition) {
            http_response_code(404);
            View::render('404', ['title' => 'Numéro introuvable']);
            return;
        }
        $hasAccess = false;
        $user = Auth::user();
        if ($user) {
            $hasAccess = Order::hasPaidOrder((int) $user['id'], (int) $edition['id']);
        }
        View::render('front/boutique-detail', [
            'title' => $edition['title'],
            'edition' => $edition,
            'hasAccess' => $hasAccess,
        ]);
    }

    public function purchase(array $params): void
    {
        Auth::requireLogin();
        if (!verify_csrf($_POST['csrf_token'] ?? null)) {
            flash('error', 'Jeton CSRF invalide.');
            redirect('/boutique/' . $params['slug']);
        }
        $edition = PdfEdition::findBySlug($params['slug']);
        if (!$edition) {
            http_response_code(404);
            View::render('404', ['title' => 'Numéro introuvable']);
            return;
        }
        $provider = $_POST['provider'] ?? 'mtn_momo';
        if (!in_array($provider, ['mtn_momo', 'orange_money'], true)) {
            $provider = 'mtn_momo';
        }
        $user = Auth::user();
        $orderId = Order::create((int) $user['id'], (int) $edition['id'], (int) $edition['price']);
        Payment::create($orderId, $provider, 'success', 'SIM-' . uniqid());
        Order::markPaid($orderId);
        flash('success', 'Paiement enregistré. Téléchargement disponible.');
        redirect('/boutique/' . $params['slug']);
    }

    public function download(array $params): void
    {
        Auth::requireLogin();
        $edition = PdfEdition::findById((int) $params['id']);
        if (!$edition) {
            http_response_code(404);
            View::render('404', ['title' => 'Fichier introuvable']);
            return;
        }
        $user = Auth::user();
        if (!Order::hasPaidOrder((int) $user['id'], (int) $edition['id'])) {
            flash('error', 'Accès réservé aux acheteurs.');
            redirect('/boutique/' . $edition['slug']);
        }
        Download::log((int) $user['id'], (int) $edition['id']);
        $file = $edition['pdf_path'];
        if (!$file || !file_exists($file)) {
            flash('error', 'Le fichier PDF est indisponible pour le moment.');
            redirect('/boutique/' . $edition['slug']);
        }
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        readfile($file);
        exit;
    }

    public function about(): void
    {
        View::render('front/about', ['title' => 'À propos']);
    }

    public function contact(): void
    {
        View::render('front/contact', ['title' => 'Contact']);
    }

    public function search(): void
    {
        $term = trim($_GET['q'] ?? '');
        $results = $term ? Article::search($term) : [];
        View::render('front/search', [
            'title' => 'Recherche',
            'term' => $term,
            'results' => $results,
        ]);
    }
}
