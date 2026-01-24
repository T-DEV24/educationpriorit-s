<?php

class AuthController
{
    public function login(): void
    {
        if (Auth::check()) {
            redirect('/profil');
        }
        if (is_post()) {
            if (!verify_csrf($_POST['csrf_token'] ?? null)) {
                flash('error', 'Jeton CSRF invalide.');
                redirect('/connexion');
            }
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $user = $email ? User::findByEmail($email) : null;
            if (!$user || !password_verify($password, $user['password_hash'])) {
                flash('error', 'Identifiants invalides.');
                redirect('/connexion');
            }
            if ((int) $user['is_active'] !== 1) {
                flash('error', 'Votre compte est désactivé.');
                redirect('/connexion');
            }
            Auth::login((int) $user['id']);
            redirect('/profil');
        }

        View::render('user/login', ['title' => 'Connexion']);
    }

    public function register(): void
    {
        if (Auth::check()) {
            redirect('/profil');
        }
        if (is_post()) {
            if (!verify_csrf($_POST['csrf_token'] ?? null)) {
                flash('error', 'Jeton CSRF invalide.');
                redirect('/inscription');
            }
            $fullName = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            if ($fullName === '' || $email === '' || $password === '') {
                flash('error', 'Veuillez remplir tous les champs.');
                redirect('/inscription');
            }
            if (User::findByEmail($email)) {
                flash('error', 'Cet email est déjà utilisé.');
                redirect('/inscription');
            }
            $roleId = User::roleIdByName('Lecteur') ?? User::roleIdByName('Rédacteur');
            $userId = User::create([
                'role_id' => $roleId,
                'full_name' => $fullName,
                'email' => $email,
                'password_hash' => password_hash($password, PASSWORD_BCRYPT),
                'is_active' => 1,
            ]);
            Auth::login($userId);
            redirect('/profil');
        }

        View::render('user/register', ['title' => 'Inscription']);
    }

    public function logout(): void
    {
        Auth::logout();
        redirect('/');
    }
}
