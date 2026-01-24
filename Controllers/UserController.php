<?php

class UserController
{
    public function profile(): void
    {
        Auth::requireLogin();
        $user = Auth::user();
        View::render('user/profile', [
            'title' => 'Mon profil',
            'user' => $user,
        ]);
    }

    public function comments(): void
    {
        Auth::requireLogin();
        $user = Auth::user();
        $comments = Comment::byUser((int) $user['id']);
        View::render('user/comments', [
            'title' => 'Mes commentaires',
            'comments' => $comments,
        ]);
    }

    public function purchases(): void
    {
        Auth::requireLogin();
        $user = Auth::user();
        $orders = Order::listByUser((int) $user['id']);
        View::render('user/purchases', [
            'title' => 'Mes achats',
            'orders' => $orders,
        ]);
    }

    public function password(): void
    {
        Auth::requireLogin();
        if (is_post()) {
            if (!verify_csrf($_POST['csrf_token'] ?? null)) {
                flash('error', 'Jeton CSRF invalide.');
                redirect('/mot-de-passe');
            }
            $current = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $user = Auth::user();
            if (!$user || !password_verify($current, $user['password_hash'])) {
                flash('error', 'Mot de passe actuel incorrect.');
                redirect('/mot-de-passe');
            }
            User::updatePassword((int) $user['id'], password_hash($newPassword, PASSWORD_BCRYPT));
            flash('success', 'Mot de passe mis à jour.');
            redirect('/profil');
        }

        View::render('user/password', ['title' => 'Changer le mot de passe']);
    }
}
