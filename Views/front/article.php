<section class="section">
    <div class="container narrow" data-article-slug="<?= htmlspecialchars($slug ?? '') ?>">
        <p class="eyebrow" id="article-category">Actualité</p>
        <h1 id="article-title">Chargement...</h1>
        <p class="muted" id="article-meta"></p>
        <div class="article-body" id="article-body">
            <p>Chargement du contenu...</p>
        </div>
        <div class="article-actions">
            <button class="btn btn-outline" type="button" id="article-like">J'aime</button>
            <button class="btn btn-outline" type="button" id="article-comment-anchor">Commenter</button>
            <a class="btn btn-ghost" id="share-whatsapp" href="#" target="_blank" rel="noopener">Partager WhatsApp</a>
            <a class="btn btn-ghost" id="share-facebook" href="#" target="_blank" rel="noopener">Partager Facebook</a>
        </div>
        <p class="muted" id="article-like-info">1 like par utilisateur et par article. Connexion requise pour liker ou commenter.</p>
    </div>
</section>

<section class="section alt">
    <div class="container narrow">
        <h2>Commentaires</h2>
        <p class="muted">Les commentaires sont modérés et publiés après validation.</p>
        <div class="alert alert-danger d-none" id="comment-alert" role="alert"></div>
        <div id="comment-list"></div>
        <div class="pagination">
            <button class="btn btn-outline" type="button" id="comment-prev">Précédent</button>
            <span id="comment-page">Page 1</span>
            <button class="btn btn-outline" type="button" id="comment-next">Suivant</button>
        </div>
        <div class="comment-form" id="comment-form">
            <textarea id="comment-content" placeholder="Écrire un commentaire..."></textarea>
            <button class="btn btn-primary" type="button" id="comment-submit">Publier</button>
        </div>
    </div>
</section>

<script>
const articleContainer = document.querySelector('[data-article-slug]');
const pathParts = window.location.pathname.split('/').filter(Boolean);
const derivedSlug = pathParts.length > 1 ? pathParts[pathParts.length - 1] : '';
const articleSlug = articleContainer?.dataset.articleSlug || derivedSlug;
const articleCategory = document.getElementById('article-category');
const articleTitle = document.getElementById('article-title');
const articleMeta = document.getElementById('article-meta');
const articleBody = document.getElementById('article-body');
const articleLike = document.getElementById('article-like');
const articleLikeInfo = document.getElementById('article-like-info');
const commentList = document.getElementById('comment-list');
const commentAlert = document.getElementById('comment-alert');
const commentPrev = document.getElementById('comment-prev');
const commentNext = document.getElementById('comment-next');
const commentPage = document.getElementById('comment-page');
const commentContent = document.getElementById('comment-content');
const commentSubmit = document.getElementById('comment-submit');
const commentAnchor = document.getElementById('article-comment-anchor');
const shareWhatsapp = document.getElementById('share-whatsapp');
const shareFacebook = document.getElementById('share-facebook');
let articleData = null;
let commentCurrentPage = 1;
let commentLastPage = 1;

function formatDate(value) {
    if (!value) {
        return '';
    }
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) {
        return value;
    }
    return date.toLocaleDateString('fr-FR', { year: 'numeric', month: 'long', day: 'numeric' });
}

function renderArticle(article) {
    articleData = article;
    articleCategory.textContent = article.category_name ?? 'Rubrique';
    articleTitle.textContent = article.title ?? 'Article';
    articleMeta.textContent = article.published_at
        ? `Publié le ${formatDate(article.published_at)}`
        : '';
    articleBody.innerHTML = article.content || `<p>${article.summary ?? ''}</p>`;
    articleLike.dataset.articleId = article.id ?? '';
    const likesCount = article.likes_count ?? 0;
    articleLike.textContent = `J'aime (${likesCount})`;

    const shareUrl = window.location.href;
    const shareText = article.title ?? 'EducationPriorité';
    const whatsappText = `${shareText} - ${shareUrl}`;
    shareWhatsapp.href = `https://wa.me/?text=${encodeURIComponent(whatsappText)}`;

    const facebookParams = new URLSearchParams({ u: shareUrl });
    if (article.image_path) {
        const absoluteImage = article.image_path.startsWith('http')
            ? article.image_path
            : `${window.location.origin}/${article.image_path.replace(/^\\//, '')}`;
        facebookParams.set('picture', absoluteImage);
    }
    shareFacebook.href = `https://www.facebook.com/sharer/sharer.php?${facebookParams.toString()}`;

    const description = article.summary || (article.content ? article.content.replace(/<[^>]+>/g, '').slice(0, 160) : '');
    const title = article.title ? `${article.title} | EducationPriorité` : 'EducationPriorité';
    document.title = title;
    const descriptionMeta = document.getElementById('meta-description');
    if (descriptionMeta && description) {
        descriptionMeta.setAttribute('content', description);
    }
    const ogTitle = document.getElementById('meta-og-title');
    if (ogTitle && title) {
        ogTitle.setAttribute('content', title);
    }
    const ogDescription = document.getElementById('meta-og-description');
    if (ogDescription && description) {
        ogDescription.setAttribute('content', description);
    }
    const ogType = document.getElementById('meta-og-type');
    if (ogType) {
        ogType.setAttribute('content', 'article');
    }
    const ogUrl = document.getElementById('meta-og-url');
    if (ogUrl) {
        ogUrl.setAttribute('content', window.location.href);
    }
    const ogImage = document.getElementById('meta-og-image');
    if (ogImage && article.image_path) {
        const absoluteImage = article.image_path.startsWith('http')
            ? article.image_path
            : `${window.location.origin}/${article.image_path.replace(/^\\//, '')}`;
        ogImage.setAttribute('content', absoluteImage);
    }
}

function loadArticleBySlug(slug) {
    return window.apiFetch(`/api/articles?slug=${encodeURIComponent(slug)}`)
        .then((payload) => {
            if (!payload.ok) {
                throw new Error(window.getApiErrorMessage(payload, 'Article introuvable.'));
            }
            return payload.data?.data ?? payload.data;
        });
}

function loadFallbackArticle() {
    return window.apiFetch('/api/articles?limit=1')
        .then((payload) => {
            if (!payload.ok) {
                return null;
            }
            const data = payload.data?.data ?? payload.data ?? [];
            return data[0] ?? null;
        });
}

function loadLikeStatus(articleId) {
    if (!articleId) {
        return;
    }
    window.apiFetch(`/api/likes?article_id=${articleId}`, { credentials: 'same-origin' })
        .then((payload) => {
            const data = payload.data?.data ?? payload.data ?? {};
            articleLike.textContent = `J'aime (${data.count ?? 0})`;
            if (data.liked) {
                articleLike.classList.add('btn-primary');
                articleLike.classList.remove('btn-outline');
            } else {
                articleLike.classList.add('btn-outline');
                articleLike.classList.remove('btn-primary');
            }
        })
        .catch(() => {});
}

function renderComments(comments) {
    commentList.innerHTML = '';
    if (comments.length === 0) {
        commentList.innerHTML = '<p class="muted">Aucun commentaire pour le moment.</p>';
        return;
    }
    comments.forEach(comment => {
        const wrapper = document.createElement('div');
        wrapper.className = 'comment';
        const meta = document.createElement('p');
        meta.innerHTML = `<strong>${comment.user_name ?? 'Lecteur'}</strong> • ${formatDate(comment.created_at)}`;
        const content = document.createElement('p');
        content.textContent = comment.content ?? '';
        wrapper.append(meta, content);
        commentList.appendChild(wrapper);
    });
}

function loadComments() {
    if (!articleData?.id) {
        return;
    }
    commentAlert.classList.add('d-none');
    const params = new URLSearchParams({
        article_id: String(articleData.id),
        page: String(commentCurrentPage),
        limit: '5',
    });
    window.apiFetch(`/api/comments?${params.toString()}`)
        .then((payload) => {
            if (!payload.ok) {
                throw new Error(window.getApiErrorMessage(payload, 'Impossible de charger les commentaires.'));
            }
            const comments = payload.data?.data ?? payload.data ?? [];
            const pagination = payload.data?.pagination ?? payload.pagination ?? {};
            commentLastPage = pagination.pages ?? 1;
            commentPage.textContent = `Page ${pagination.page ?? commentCurrentPage} / ${commentLastPage}`;
            renderComments(comments);
            commentPrev.disabled = commentCurrentPage <= 1;
            commentNext.disabled = commentCurrentPage >= commentLastPage;
        })
        .catch(error => {
            commentAlert.textContent = error.message;
            commentAlert.classList.remove('d-none');
        });
}

articleLike.addEventListener('click', () => {
    const articleId = articleLike.dataset.articleId;
    if (!articleId) {
        return;
    }
    window.apiFetch('/api/likes', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ article_id: Number(articleId) }),
        credentials: 'same-origin',
    })
        .then((payload) => {
            if (!payload.ok && payload.status !== 409) {
                throw new Error(window.getApiErrorMessage(payload, 'Impossible de liker.'));
            }
        })
        .then(() => loadLikeStatus(articleId))
        .catch(() => {
            articleLikeInfo.textContent = 'Impossible de liker. Connectez-vous pour aimer cet article.';
        });
});

commentSubmit.addEventListener('click', () => {
    const content = commentContent.value.trim();
    if (!content || !articleData?.id) {
        return;
    }
    commentAlert.classList.add('d-none');
    window.apiFetch('/api/comments', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ article_id: Number(articleData.id), content }),
        credentials: 'same-origin',
    })
        .then((payload) => {
            if (!payload.ok) {
                throw new Error(window.getApiErrorMessage(payload, 'Connexion requise pour commenter.'));
            }
        })
        .then(() => {
            commentContent.value = '';
            loadComments();
        })
        .catch(error => {
            commentAlert.textContent = error.message;
            commentAlert.classList.remove('d-none');
        });
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

commentAnchor.addEventListener('click', () => {
    document.getElementById('comment-form')?.scrollIntoView({ behavior: 'smooth' });
});

const articleLoader = articleSlug ? loadArticleBySlug(articleSlug) : loadFallbackArticle();
articleLoader
    .then(article => {
        if (!article) {
            articleTitle.textContent = 'Article introuvable';
            articleBody.innerHTML = '<p>Revenez plus tard pour consulter nos derniers articles.</p>';
            return;
        }
        renderArticle(article);
        loadLikeStatus(article.id);
        loadComments();
    })
    .catch(() => {
        articleTitle.textContent = 'Article introuvable';
    });
</script>
