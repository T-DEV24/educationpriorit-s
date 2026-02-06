<section class="section">
    <div class="container">
        <h1>Mon profil</h1>
        <div class="profile-grid">
            <div class="info-card">
                <h3>Informations</h3>
                <div class="alert alert-danger d-none" id="profile-alert" role="alert"></div>
                <p id="profile-name">Nom : ...</p>
                <p id="profile-email">Email : ...</p>
                <p id="profile-status">Statut : ...</p>
                <button class="btn btn-outline" type="button" id="profile-logout">Se déconnecter</button>
            </div>
            <div class="info-card">
                <h3>Historique & actions</h3>
                <a class="link" href="/profil/achats">Historique des achats</a>
                <a class="link" href="/profil/commentaires">Mes commentaires</a>
                <a class="link" href="/profil/mot-de-passe">Changer le mot de passe</a>
            </div>
        </div>
    </div>
</section>

<script>
const profileAlert = document.getElementById('profile-alert');
const profileName = document.getElementById('profile-name');
const profileEmail = document.getElementById('profile-email');
const profileStatus = document.getElementById('profile-status');
const profileLogout = document.getElementById('profile-logout');

function loadProfile() {
    profileAlert.classList.add('d-none');
    window.apiFetch('/api/users?me=1', { credentials: 'same-origin' })
        .then((payload) => {
            if (!payload.ok) {
                throw new Error(window.getApiErrorMessage(payload, 'Impossible de charger le profil.'));
            }
            return payload.data?.data ?? payload.data;
        })
        .then((user) => {
            profileName.textContent = `Nom : ${user.full_name ?? '-'}`;
            profileEmail.textContent = `Email : ${user.email ?? '-'}`;
            profileStatus.textContent = `Statut : ${(user.is_active ?? 0) === 1 ? 'Compte activé' : 'Compte désactivé'}`;
        })
        .catch(error => {
            profileAlert.textContent = error.message;
            profileAlert.classList.remove('d-none');
        });
}

profileLogout.addEventListener('click', () => {
    window.apiFetch('/api/auth/logout', {
        method: 'POST',
        credentials: 'same-origin',
    })
        .finally(() => {
            window.clearAuthToken();
            window.location.href = '/login';
        });
});

loadProfile();
</script>
