<section class="section">
    <div class="container narrow">
        <h1>Changer le mot de passe</h1>
        <form class="form-grid" id="password-form">
            <label>
                Mot de passe actuel
                <input type="password" id="password-current" placeholder="Mot de passe actuel" required>
            </label>
            <label>
                Nouveau mot de passe
                <input type="password" id="password-new" placeholder="Nouveau mot de passe" required>
            </label>
            <button class="btn btn-primary" type="submit">Mettre à jour</button>
        </form>
        <div class="alert alert-success d-none" id="password-success" role="alert"></div>
        <div class="alert alert-danger d-none" id="password-alert" role="alert"></div>
    </div>
</section>

<script>
const passwordForm = document.getElementById('password-form');
const passwordCurrent = document.getElementById('password-current');
const passwordNew = document.getElementById('password-new');
const passwordAlert = document.getElementById('password-alert');
const passwordSuccess = document.getElementById('password-success');

passwordForm.addEventListener('submit', event => {
    event.preventDefault();
    passwordAlert.classList.add('d-none');
    passwordSuccess.classList.add('d-none');
    window.apiFetch('/api/auth/password', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({
            current_password: passwordCurrent.value,
            new_password: passwordNew.value,
        }),
    })
        .then((payload) => {
            if (!payload.ok) {
                throw new Error(window.getApiErrorMessage(payload, 'Impossible de mettre à jour le mot de passe.'));
            }
            passwordCurrent.value = '';
            passwordNew.value = '';
            passwordSuccess.textContent = 'Mot de passe mis à jour.';
            passwordSuccess.classList.remove('d-none');
        })
        .catch(error => {
            passwordAlert.textContent = error.message;
            passwordAlert.classList.remove('d-none');
        });
});
</script>
