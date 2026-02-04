<section class="section">
    <div class="container narrow">
        <h1>Connexion</h1>
        <form class="form-grid" id="login-form">
            <label>
                Email
                <input type="email" id="login-email" placeholder="Votre email" required>
            </label>
            <label>
                Mot de passe
                <input type="password" id="login-password" placeholder="Votre mot de passe" required>
            </label>
            <button class="btn btn-primary" type="submit">Se connecter</button>
        </form>
        <div class="alert alert-danger d-none" id="login-alert" role="alert"></div>
        <p class="muted">Votre compte doit être activé pour accéder à votre profil et à vos achats.</p>
        <p class="muted">Pas encore de compte ? <a class="link" href="/inscription">Créer un compte</a></p>
    </div>
</section>

<script>
const loginForm = document.getElementById('login-form');
const loginEmail = document.getElementById('login-email');
const loginPassword = document.getElementById('login-password');
const loginAlert = document.getElementById('login-alert');

loginForm.addEventListener('submit', event => {
    event.preventDefault();
    loginAlert.classList.add('d-none');
    window.apiFetch('/api/auth/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({
            email: loginEmail.value.trim(),
            password: loginPassword.value,
        }),
    })
        .then((payload) => {
            if (!payload.ok) {
                throw new Error(window.getApiErrorMessage(payload, 'Impossible de se connecter.'));
            }
            window.storeAuthToken(payload.data?.token ?? payload.token);
            const roleId = Number(payload.data?.role_id ?? payload.data?.data?.role_id ?? 0);
            window.location.href = roleId === 1 ? '/admin' : '/profil/accueil';
        })
        .catch(error => {
            loginAlert.textContent = error.message;
            loginAlert.classList.remove('d-none');
        });
});
</script>
