<section class="section">
    <div class="container">
        <div class="section-header">
            <h1>Gestion des utilisateurs</h1>
            <button class="btn btn-primary" type="button" id="admin-user-create">Nouvel utilisateur</button>
        </div>
        <p class="muted">Suivi des comptes, rôles et statuts d'activation.</p>
        <div id="admin-user-alert" class="alert alert-danger d-none" role="alert"></div>
        <table class="table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Rôle</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="admin-user-body"></tbody>
        </table>
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline" type="button" id="admin-user-prev">Précédent</button>
            <span class="muted" id="admin-user-page">Page 1</span>
            <button class="btn btn-outline" type="button" id="admin-user-next">Suivant</button>
        </div>
    </div>
</section>

<script>
    const userBody = document.getElementById('admin-user-body');
    const userAlert = document.getElementById('admin-user-alert');
    const userPage = document.getElementById('admin-user-page');
    const userPrev = document.getElementById('admin-user-prev');
    const userNext = document.getElementById('admin-user-next');
    const userCreate = document.getElementById('admin-user-create');
    let userCurrentPage = 1;
    let userLastPage = 1;
    let userRows = [];

    const renderUsers = (items) => {
        userBody.innerHTML = '';
        if (!items.length) {
            userBody.innerHTML = '<tr><td colspan="4" class="muted">Aucun utilisateur trouvé.</td></tr>';
            return;
        }
        items.forEach((item) => {
            const row = document.createElement('tr');
            const name = item.full_name ?? item.email ?? 'Utilisateur';
            const status = item.is_active ? 'Actif' : 'Inactif';
            row.innerHTML = `
                <td>${name}</td>
                <td>${item.role_id ?? '-'}</td>
                <td>${status}</td>
                <td>
                    <button class="btn btn-link p-0 admin-user-edit" data-id="${item.id}">Modifier</button>
                    <span class="muted">•</span>
                    <button class="btn btn-link text-danger p-0 admin-user-delete" data-id="${item.id}">Supprimer</button>
                </td>
            `;
            userBody.appendChild(row);
        });
    };

    const loadUsers = () => {
        userAlert.classList.add('d-none');
        window.apiFetch(`/api/admin/users?page=${userCurrentPage}&limit=10`, { credentials: 'same-origin' })
            .then((payload) => {
                if (!payload.ok) {
                    throw new Error(window.getApiErrorMessage(payload, 'Impossible de charger les utilisateurs.'));
                }
                userRows = payload.data?.data ?? payload.data ?? [];
                userLastPage = payload.data?.pagination?.pages ?? payload.pagination?.pages ?? 1;
                const currentPage = payload.data?.pagination?.page ?? payload.pagination?.page ?? userCurrentPage;
                userPage.textContent = `Page ${currentPage} / ${userLastPage}`;
                renderUsers(userRows);
            })
            .catch((error) => {
                userAlert.textContent = error.message;
                userAlert.classList.remove('d-none');
            });
    };

    const promptForPayload = (initial) => {
        const value = window.prompt('Entrez le JSON de l’utilisateur :', initial);
        if (!value) {
            return null;
        }
        try {
            return JSON.parse(value);
        } catch (error) {
            window.alert('JSON invalide. Veuillez réessayer.');
            return null;
        }
    };

    userCreate.addEventListener('click', () => {
        const payload = promptForPayload('{"full_name":"","email":"","password":"","role_id":2,"is_active":1}');
        if (!payload) {
            return;
        }
        window.apiFetch('/api/admin/users', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify(payload),
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error(window.getApiErrorMessage(response, 'Impossible de créer l\'utilisateur.'));
                }
                loadUsers();
            })
            .catch((error) => {
                userAlert.textContent = error.message;
                userAlert.classList.remove('d-none');
            });
    });

    userBody.addEventListener('click', (event) => {
        const target = event.target;
        if (target.classList.contains('admin-user-edit')) {
            const id = target.getAttribute('data-id');
            const current = userRows.find((item) => String(item.id) === id);
            const payload = promptForPayload(JSON.stringify(current ?? {}, null, 2));
            if (!payload) {
                return;
            }
            window.apiFetch(`/api/admin/users/${id}`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'same-origin',
                body: JSON.stringify(payload),
            })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error(window.getApiErrorMessage(response, 'Impossible de mettre à jour l\'utilisateur.'));
                    }
                    loadUsers();
                })
                .catch((error) => {
                    userAlert.textContent = error.message;
                    userAlert.classList.remove('d-none');
                });
        }
        if (target.classList.contains('admin-user-delete')) {
            const id = target.getAttribute('data-id');
            if (!window.confirm('Supprimer cet utilisateur ?')) {
                return;
            }
            window.apiFetch(`/api/admin/users/${id}`, {
                method: 'DELETE',
                credentials: 'same-origin',
            })
                .then((response) => {
                    if (!response.ok) {
                        throw new Error(window.getApiErrorMessage(response, 'Impossible de supprimer l\'utilisateur.'));
                    }
                    loadUsers();
                })
                .catch((error) => {
                    userAlert.textContent = error.message;
                    userAlert.classList.remove('d-none');
                });
        }
    });

    userPrev.addEventListener('click', () => {
        if (userCurrentPage > 1) {
            userCurrentPage -= 1;
            loadUsers();
        }
    });

    userNext.addEventListener('click', () => {
        if (userCurrentPage < userLastPage) {
            userCurrentPage += 1;
            loadUsers();
        }
    });

    loadUsers();
</script>
