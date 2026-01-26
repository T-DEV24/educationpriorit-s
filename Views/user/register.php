<section class="section">
    <div class="container narrow">
        <h1>Inscription</h1>
        <form class="form-grid" id="register-form">
            <label>
                Nom complet
                <input type="text" id="register-name" placeholder="Votre nom" required>
            </label>
            <label>
                Email
                <input type="email" id="register-email" placeholder="Votre email" required>
            </label>
            <label>
                Mot de passe
                <input type="password" id="register-password" placeholder="Créer un mot de passe" required>
            </label>
            <button class="btn btn-primary" type="submit">Créer mon compte</button>
        </form>
        <div class="alert alert-danger d-none" id="register-alert" role="alert"></div>
        <div class="info-card">
            <h3>Activation du compte</h3>
            <p class="muted">Après inscription, un email d’activation est envoyé pour valider votre compte.</p>
        </div>
        <p class="muted">Déjà inscrit ? <a class="link" href="/connexion">Se connecter</a></p>
    </div>
</section>

<script>
const registerForm = document.getElementById('register-form');
const registerName = document.getElementById('register-name');
const registerEmail = document.getElementById('register-email');
const registerPassword = document.getElementById('register-password');
const registerAlert = document.getElementById('register-alert');

registerForm.addEventListener('submit', event => {
    event.preventDefault();
    registerAlert.classList.add('d-none');
    fetch('/api/auth/register', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({
            full_name: registerName.value.trim(),
            email: registerEmail.value.trim(),
            password: registerPassword.value,
        }),
    })
        .then(async response => {
            const payload = await response.json();
            if (!response.ok) {
                throw new Error(payload.error || 'Impossible de créer le compte.');
            }
            return payload;
        })
        .then(() => {
            window.location.href = '/profil';
        })
        .catch(error => {
            registerAlert.textContent = error.message;
            registerAlert.classList.remove('d-none');
        });
});
</script>
