<section class="section">
    <div class="container">
        <div class="section-header">
            <h1>Médiathèque</h1>
            <button class="btn btn-primary" type="button" id="media-upload-btn">Ajouter un média</button>
        </div>
        <div class="alert alert-danger d-none" id="media-alert" role="alert"></div>
        <div class="info-card mb-4">
            <form class="form-grid" id="media-form">
                <label>
                    Fichier
                    <input type="file" id="media-file" required>
                </label>
                <label>
                    Texte alternatif
                    <input type="text" id="media-alt">
                </label>
                <button class="btn btn-primary" type="submit">Téléverser</button>
            </form>
        </div>
        <div class="card-grid" id="media-grid"></div>
    </div>
</section>

<script>
const mediaAlert = document.getElementById('media-alert');
const mediaForm = document.getElementById('media-form');
const mediaFile = document.getElementById('media-file');
const mediaAlt = document.getElementById('media-alt');
const mediaGrid = document.getElementById('media-grid');
const mediaUploadBtn = document.getElementById('media-upload-btn');

function renderMedia(item) {
    const card = document.createElement('div');
    card.className = 'card';
    const filePath = item.file_path ?? '';
    const filename = filePath.split('/').pop();
    card.innerHTML = `
        <h3>${filename ?? 'Fichier'}</h3>
        <p class="muted">${item.alt_text ?? ''}</p>
        <div class="card-actions">
            <a class="link" href="/${filePath}" target="_blank">Voir</a>
            <span class="muted">•</span>
            <button class="btn btn-link text-danger p-0 media-delete" data-id="${item.id}">Supprimer</button>
        </div>
    `;
    return card;
}

function loadMedia() {
    return window.apiFetch('/api/admin/media?limit=50', { credentials: 'same-origin' })
        .then((payload) => {
            if (!payload.ok) {
                throw new Error(window.getApiErrorMessage(payload, 'Impossible de charger la médiathèque.'));
            }
            const items = payload.data?.data ?? payload.data ?? [];
            mediaGrid.innerHTML = '';
            if (!items.length) {
                mediaGrid.innerHTML = '<p class="muted">Aucun média disponible.</p>';
                return;
            }
            items.forEach((item) => mediaGrid.appendChild(renderMedia(item)));
        });
}

mediaForm.addEventListener('submit', (event) => {
    event.preventDefault();
    mediaAlert.classList.add('d-none');
    const file = mediaFile.files[0];
    if (!file) {
        mediaAlert.textContent = 'Veuillez sélectionner un fichier.';
        mediaAlert.classList.remove('d-none');
        return;
    }
    const formData = new FormData();
    formData.append('file', file);
    formData.append('alt_text', mediaAlt.value.trim());
    window.apiFetch('/api/media/upload', {
        method: 'POST',
        credentials: 'same-origin',
        body: formData,
    })
        .then((payload) => {
            if (!payload.ok) {
                throw new Error(window.getApiErrorMessage(payload, 'Impossible de téléverser le média.'));
            }
            mediaFile.value = '';
            mediaAlt.value = '';
            return loadMedia();
        })
        .catch((error) => {
            mediaAlert.textContent = error.message;
            mediaAlert.classList.remove('d-none');
        });
});

mediaGrid.addEventListener('click', (event) => {
    const target = event.target;
    if (!target.classList.contains('media-delete')) {
        return;
    }
    const id = target.dataset.id;
    if (!window.confirm('Supprimer ce média ?')) {
        return;
    }
    window.apiFetch(`/api/admin/media/${id}`, { method: 'DELETE', credentials: 'same-origin' })
        .then(() => loadMedia())
        .catch((error) => {
            mediaAlert.textContent = error.message;
            mediaAlert.classList.remove('d-none');
        });
});

mediaUploadBtn.addEventListener('click', () => {
    mediaForm.scrollIntoView({ behavior: 'smooth' });
});

loadMedia();
</script>
