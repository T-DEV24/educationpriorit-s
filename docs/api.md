# Documentation API — EducationPriorités

> Base URL: `/api`

Cette documentation décrit les endpoints disponibles, les payloads attendus, les codes d’erreur communs et la pagination.

## Principes généraux

### Format JSON
- Requêtes JSON : utilisez `Content-Type: application/json`.
- Réponses : JSON avec `data` ou `error`.

### Codes d’erreur fréquents
- `400` : Requête invalide (ex: identifiant absent ou invalide).
- `401` : Connexion requise (utilisateur non authentifié).
- `403` : Accès interdit (droits insuffisants / admin requis).
- `404` : Ressource introuvable.
- `405` : Méthode non autorisée.
- `409` : Conflit (ex: email déjà utilisé, like déjà existant).
- `422` : Validation échouée (champs requis manquants).
- `500` : Erreur serveur.

### Pagination
Les endpoints listés supportent généralement :
- `page` (défaut: 1)
- `limit` (défaut: 12 ou 20 selon la ressource)

**Exemple** :
```
GET /api/articles?page=1&limit=12
```

Réponse typique :
```json
{
  "data": [/* items */],
  "pagination": {
    "page": 1,
    "limit": 12,
    "total": 120,
    "pages": 10
  }
}
```

---

## Authentification

### Inscription
```
POST /api/auth/register
```
Payload :
```json
{
  "full_name": "Jean Dupont",
  "email": "jean@example.com",
  "password": "secret"
}
```
Réponse `201` :
```json
{
  "data": {
    "id": 12,
    "full_name": "Jean Dupont",
    "email": "jean@example.com",
    "role_id": 2,
    "is_active": 1
  }
}
```

### Connexion
```
POST /api/auth/login
```
Payload :
```json
{
  "email": "jean@example.com",
  "password": "secret"
}
```
Réponse `200` :
```json
{
  "message": "Connexion réussie.",
  "data": {
    "id": 12,
    "full_name": "Jean Dupont",
    "email": "jean@example.com",
    "role_id": 2,
    "is_active": 1
  }
}
```

### Déconnexion
```
POST /api/auth/logout
```
Réponse `200` :
```json
{ "message": "Déconnexion réussie." }
```

---

## Articles

### Liste (pagination)
```
GET /api/articles?page=1&limit=12
```

### Détail
```
GET /api/articles/{id}
```

### Création (admin requis)
```
POST /api/articles
```
Payload :
```json
{
  "title": "Titre",
  "content": "Contenu",
  "category_id": 1,
  "status": "draft"
}
```

### Mise à jour (admin requis)
```
PATCH /api/articles/{id}
```
Payload :
```json
{
  "title": "Nouveau titre"
}
```

### Suppression (admin requis)
```
DELETE /api/articles/{id}
```

---

## Catégories

### Liste
```
GET /api/categories?page=1&limit=12
```

### Détail
```
GET /api/categories/{id}
```

### Création / Mise à jour / Suppression (admin requis)
```
POST /api/categories
PATCH /api/categories/{id}
DELETE /api/categories/{id}
```
Payload exemple :
```json
{
  "name": "Actualité",
  "slug": "actualite"
}
```

---

## Commentaires

### Liste
```
GET /api/comments?page=1&limit=20
```

### Création (connexion requise)
```
POST /api/comments
```
Payload :
```json
{
  "article_id": 123,
  "content": "Merci pour cet article."
}
```

### Modération admin
```
PATCH /api/admin/comments/{id}
```
Payload :
```json
{ "is_approved": 1 }
```

---

## Likes

### Ajouter un like (connexion requise)
```
POST /api/likes
```
Payload :
```json
{ "article_id": 123 }
```
Erreur `409` si l’utilisateur a déjà liké l’article.

---

## Boutique PDF

### Liste des PDF
```
GET /api/shop/pdfs?page=1&limit=12
```

### Achat (connexion requise)
```
POST /api/shop/purchase
```
Payload :
```json
{ "pdf_edition_id": 10 }
```

### Vérification d’accès (connexion requise)
```
GET /api/shop/verify/{pdfId}
```
Réponse :
```json
{ "data": { "pdf_edition_id": 10, "has_access": true } }
```

### Téléchargement sécurisé (connexion requise + achat)
```
GET /api/shop/download/{pdfId}
```

---

## Admin

### Dashboard (compteurs)
```
GET /api/admin/dashboard
```
Réponse :
```json
{
  "data": {
    "users": 120,
    "articles": 56,
    "comments": 240,
    "pdf_editions": 18,
    "orders": 40,
    "downloads": 90
  }
}
```

### CRUD Admin (articles, users, comments, pdf)
```
GET /api/admin/articles?page=1&limit=10
POST /api/admin/articles
PATCH /api/admin/articles/{id}
DELETE /api/admin/articles/{id}
```

```
GET /api/admin/users?page=1&limit=10
POST /api/admin/users
PATCH /api/admin/users/{id}
DELETE /api/admin/users/{id}
```

```
GET /api/admin/comments?page=1&limit=10
PATCH /api/admin/comments/{id}
DELETE /api/admin/comments/{id}
```

```
GET /api/admin/pdf-editions?page=1&limit=10
POST /api/admin/pdf-editions
PATCH /api/admin/pdf-editions/{id}
DELETE /api/admin/pdf-editions/{id}
```

---

## Médias & Pages

### Médias
```
GET /api/media?page=1&limit=100
```

### Pages
```
GET /api/pages?page=1&limit=100
```
