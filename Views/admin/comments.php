<section class="section">
    <div class="container">
        <h1>Modération des commentaires</h1>
        <p class="muted">Validez ou supprimez les commentaires avant publication.</p>
        <div id="admin-comment-alert" class="alert alert-danger d-none" role="alert"></div>
        <div id="admin-comment-list"></div>
        <div class="d-flex align-items-center gap-2 mt-3">
            <button class="btn btn-outline" type="button" id="admin-comment-prev">Précédent</button>
            <span class="muted" id="admin-comment-page">Page 1</span>
            <button class="btn btn-outline" type="button" id="admin-comment-next">Suivant</button>
        </div>
    </div>
</section>

<script>
    const commentList = document.getElementById('admin-comment-list');
    const commentAlert = document.getElementById('admin-comment-alert');
    const commentPage = document.getElementById('admin-comment-page');
    const commentPrev = document.getElementById('admin-comment-prev');
    const commentNext = document.getElementById('admin-comment-next');
    let commentCurrentPage = 1;
    let commentLastPage = 1;
    let commentRows = [];

    const renderComments = (items) => {
        commentList.innerHTML = '';
        if (!items.length) {
            commentList.innerHTML = '<p class="muted">Aucun commentaire trouvé.</p>';
            return;
        }
        items.forEach((item) => {
            const wrapper = document.createElement('div');
            wrapper.className = 'comment';
            wrapper.innerHTML = `
                <p><strong>Utilisateur ${item.user_id ?? '-'}</strong> • Article : ${item.article_id ?? '-'}</p>
                <p>${item.content ?? ''}</p>
                <button class="btn btn-primary admin-comment-approve" type="button" data-id="${item.id}">Approuver</button>
                <button class="btn btn-outline admin-comment-delete" type="button" data-id="${item.id}">Supprimer</button>
            `;
            commentList.appendChild(wrapper);
        });
    };

    const loadComments = () => {
        commentAlert.classList.add('d-none');
        fetch(`/api/admin/comments?page=${commentCurrentPage}&limit=10`, { credentials: 'same-origin' })
            .then((response) => {
                if (!response.ok) {
                    throw new Error('Impossible de charger les commentaires.');
                }
                return response.json();
            })
            .then((payload) => {
                commentRows = payload.data ?? [];
                commentLastPage = payload.pagination?.pages ?? 1;
                commentPage.textContent = `Page ${payload.pagination?.page ?? commentCurrentPage} / ${commentLastPage}`;
                renderComments(commentRows);
            })
            .catch((error) => {
                commentAlert.textContent = error.message;
                commentAlert.classList.remove('d-none');
            });
    };

    commentList.addEventListener('click', (event) => {
        const target = event.target;
        if (target.classList.contains('admin-comment-approve')) {
            const id = target.getAttribute('data-id');
            fetch(`/api/admin/comments/${id}`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'same-origin',
                body: JSON.stringify({ is_approved: 1 }),
            }).then(() => loadComments());
        }
        if (target.classList.contains('admin-comment-delete')) {
            const id = target.getAttribute('data-id');
            if (!window.confirm('Supprimer ce commentaire ?')) {
                return;
            }
            fetch(`/api/admin/comments/${id}`, {
                method: 'DELETE',
                credentials: 'same-origin',
            }).then(() => loadComments());
        }
    });

    commentPrev.addEventListener('click', () => {
        if (commentCurrentPage > 1) {
            commentCurrentPage -= 1;
            loadComments();
        }
    });

    commentNext.addEventListener('click', () => {
        if (commentCurrentPage < commentLastPage) {
            commentCurrentPage += 1;
            loadComments();
        }
    });

    loadComments();
</script>
