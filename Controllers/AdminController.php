<?php

class AdminController
{
    public function dashboard(): void
    {
        Auth::requireAdmin();
        $pdo = Database::connection();
        $stats = [
            'articles' => (int) $pdo->query('SELECT COUNT(*) FROM articles')->fetchColumn(),
            'users' => (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(),
            'comments' => (int) $pdo->query('SELECT COUNT(*) FROM comments')->fetchColumn(),
            'orders' => (int) $pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn(),
        ];
        View::render('admin/dashboard', [
            'title' => 'Tableau de bord',
            'stats' => $stats,
        ]);
    }

    public function articles(): void
    {
        Auth::requireAdmin();
        if (is_post()) {
            if (!verify_csrf($_POST['csrf_token'] ?? null)) {
                flash('error', 'Jeton CSRF invalide.');
                redirect('/admin/articles');
            }
            $status = $_POST['status'] ?? 'draft';
            $publishedAt = $_POST['published_at'] ?: ($status === 'published' ? date('Y-m-d H:i:s') : null);
            $data = [
                'title' => trim($_POST['title'] ?? ''),
                'slug' => trim($_POST['slug'] ?? ''),
                'summary' => trim($_POST['summary'] ?? ''),
                'content' => trim($_POST['content'] ?? ''),
                'image_path' => trim($_POST['image_path'] ?? ''),
                'category_id' => (int) ($_POST['category_id'] ?? 0),
                'author_id' => (int) (Auth::user()['id'] ?? 0),
                'status' => $status,
                'published_at' => $publishedAt,
            ];
            if ($data['title'] !== '' && $data['slug'] !== '' && $data['category_id'] > 0) {
                Article::create($data);
                flash('success', 'Article enregistré.');
            } else {
                flash('error', 'Veuillez compléter les champs obligatoires.');
            }
            redirect('/admin/articles');
        }
        $articles = Article::all();
        $categories = Category::all();
        View::render('admin/articles', [
            'title' => 'Articles',
            'articles' => $articles,
            'categories' => $categories,
        ]);
    }

    public function articleStatus(array $params): void
    {
        Auth::requireAdmin();
        if (!verify_csrf($_POST['csrf_token'] ?? null)) {
            flash('error', 'Jeton CSRF invalide.');
            redirect('/admin/articles');
        }
        $status = $_POST['status'] ?? 'draft';
        Article::updateStatus((int) $params['id'], $status);
        redirect('/admin/articles');
    }

    public function categories(): void
    {
        Auth::requireAdmin();
        if (is_post()) {
            if (!verify_csrf($_POST['csrf_token'] ?? null)) {
                flash('error', 'Jeton CSRF invalide.');
                redirect('/admin/categories');
            }
            $name = trim($_POST['name'] ?? '');
            $slug = trim($_POST['slug'] ?? '');
            if ($name && $slug) {
                Category::create($name, $slug);
                flash('success', 'Rubrique ajoutée.');
            } else {
                flash('error', 'Nom et slug requis.');
            }
            redirect('/admin/categories');
        }
        $categories = Category::all();
        View::render('admin/categories', [
            'title' => 'Rubriques',
            'categories' => $categories,
        ]);
    }

    public function comments(): void
    {
        Auth::requireAdmin();
        if (is_post()) {
            if (!verify_csrf($_POST['csrf_token'] ?? null)) {
                flash('error', 'Jeton CSRF invalide.');
                redirect('/admin/comments');
            }
            $commentId = (int) ($_POST['comment_id'] ?? 0);
            $action = $_POST['action'] ?? '';
            if ($commentId > 0) {
                if ($action === 'approve') {
                    Comment::updateStatus($commentId, 1);
                } elseif ($action === 'delete') {
                    Comment::delete($commentId);
                }
            }
            redirect('/admin/comments');
        }
        $comments = Comment::all();
        View::render('admin/comments', [
            'title' => 'Commentaires',
            'comments' => $comments,
        ]);
    }

    public function pdf(): void
    {
        Auth::requireAdmin();
        if (is_post()) {
            if (!verify_csrf($_POST['csrf_token'] ?? null)) {
                flash('error', 'Jeton CSRF invalide.');
                redirect('/admin/pdf');
            }
            $data = [
                'title' => trim($_POST['title'] ?? ''),
                'slug' => trim($_POST['slug'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'price' => (int) ($_POST['price'] ?? 0),
                'cover_path' => trim($_POST['cover_path'] ?? ''),
                'pdf_path' => trim($_POST['pdf_path'] ?? ''),
                'published_at' => $_POST['published_at'] ?: null,
            ];
            if ($data['title'] && $data['slug'] && $data['price'] > 0) {
                PdfEdition::create($data);
                flash('success', 'PDF ajouté.');
            } else {
                flash('error', 'Veuillez compléter les informations essentielles.');
            }
            redirect('/admin/pdf');
        }
        $editions = PdfEdition::all();
        View::render('admin/pdf', [
            'title' => 'Journal PDF',
            'editions' => $editions,
        ]);
    }

    public function pages(): void
    {
        Auth::requireAdmin();
        if (is_post()) {
            if (!verify_csrf($_POST['csrf_token'] ?? null)) {
                flash('error', 'Jeton CSRF invalide.');
                redirect('/admin/pages');
            }
            $data = [
                'title' => trim($_POST['title'] ?? ''),
                'slug' => trim($_POST['slug'] ?? ''),
                'content' => trim($_POST['content'] ?? ''),
                'is_published' => (int) ($_POST['is_published'] ?? 1),
            ];
            if ($data['title'] && $data['slug']) {
                Page::create($data);
                flash('success', 'Page ajoutée.');
            } else {
                flash('error', 'Titre et slug requis.');
            }
            redirect('/admin/pages');
        }
        $pages = Page::all();
        View::render('admin/pages', [
            'title' => 'Pages',
            'pages' => $pages,
        ]);
    }

    public function media(): void
    {
        Auth::requireAdmin();
        if (is_post()) {
            if (!verify_csrf($_POST['csrf_token'] ?? null)) {
                flash('error', 'Jeton CSRF invalide.');
                redirect('/admin/media');
            }
            $path = trim($_POST['file_path'] ?? '');
            $alt = trim($_POST['alt_text'] ?? '');
            if ($path) {
                Media::create($path, $alt);
                flash('success', 'Média ajouté.');
            } else {
                flash('error', 'Chemin du fichier requis.');
            }
            redirect('/admin/media');
        }
        $media = Media::all();
        View::render('admin/media', [
            'title' => 'Médias',
            'media' => $media,
        ]);
    }

    public function users(): void
    {
        Auth::requireAdmin();
        if (is_post()) {
            if (!verify_csrf($_POST['csrf_token'] ?? null)) {
                flash('error', 'Jeton CSRF invalide.');
                redirect('/admin/users');
            }
            $userId = (int) ($_POST['user_id'] ?? 0);
            $action = $_POST['action'] ?? '';
            if ($userId > 0) {
                if ($action === 'disable') {
                    User::updateStatus($userId, 0);
                }
                if ($action === 'enable') {
                    User::updateStatus($userId, 1);
                }
            }
            redirect('/admin/users');
        }
        $users = User::all();
        View::render('admin/users', [
            'title' => 'Utilisateurs',
            'users' => $users,
        ]);
    }

    public function orders(): void
    {
        Auth::requireAdmin();
        $orders = Order::all();
        View::render('admin/orders', [
            'title' => 'Commandes',
            'orders' => $orders,
        ]);
    }
}
