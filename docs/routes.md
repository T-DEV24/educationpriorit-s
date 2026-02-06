# Liste des routes

## Routes web (views)

| Route | Vue | Remarques |
| --- | --- | --- |
| `/` | `front/home` | Accueil |
| `/rubriques` | `front/rubriques` | Rubriques |
| `/rubriques/actualite` | `front/rubrique` | Rubrique = Actualité |
| `/rubriques/campus` | `front/rubrique` | Rubrique = Campus |
| `/rubriques/focus` | `front/rubrique` | Rubrique = Focus / Dossiers |
| `/rubriques/zoom` | `front/rubrique` | Rubrique = Zoom |
| `/rubriques/reportages` | `front/rubrique` | Rubrique = Reportages |
| `/rubriques/enquetes` | `front/rubrique` | Rubrique = Enquêtes |
| `/rubriques/interviews` | `front/rubrique` | Rubrique = Interviews |
| `/article` | `front/article` | Article |
| `/boutique` | `front/boutique` | Boutique PDF |
| `/boutique/numero` | `front/boutique-detail` | Détail PDF |
| `/a-propos` | `front/about` | À propos |
| `/contact` | `front/contact` | Contact |
| `/recherche` | `front/search` | Recherche |
| `/login` | `user/login` | Alias de `/connexion` |
| `/connexion` | `user/login` | Canonique historique |
| `/register` | `user/register` | Alias de `/inscription` |
| `/inscription` | `user/register` | Canonique historique |
| `/profil` | `user/profile` | Profil |
| `/dashboard` | `user/dashboard` | Alias de `/profil/accueil` |
| `/profil/accueil` | `user/dashboard` | Tableau de bord |
| `/profil/achats` | `user/purchases` | Historique des achats |
| `/profil/commentaires` | `user/comments` | Mes commentaires |
| `/profil/mot-de-passe` | `user/password` | Mot de passe |
| `/admin` | `admin/dashboard` | Tableau de bord admin |
| `/admin/articles` | `admin/articles` | Admin articles |
| `/admin/categories` | `admin/categories` | Admin rubriques |
| `/admin/pages` | `admin/pages` | Admin pages |
| `/admin/medias` | `admin/media` | Admin médiathèque |
| `/admin/pdf` | `admin/pdf` | Admin PDF |
| `/admin/commandes` | `admin/orders` | Admin commandes |
| `/admin/utilisateurs` | `admin/users` | Admin utilisateurs |
| `/admin/commentaires` | `admin/comments` | Admin commentaires |
| `/admin/stats` | `admin/stats` | Admin statistiques |
| `/article/{slug}` | `front/article` | Route dynamique |
| `/rubriques/{slug}` | `front/rubrique` | Route dynamique |

## Routes API

### Auth

| Méthode | Route | Contrôleur | Méthode |
| --- | --- | --- | --- |
| POST | `/api/auth/register` | `AuthController` | `register` |
| POST | `/api/auth/login` | `AuthController` | `login` |
| POST | `/api/auth/logout` | `AuthController` | `logout` |
| POST | `/api/auth/password` | `AuthController` | `updatePassword` |

### Media

| Méthode | Route | Contrôleur | Méthode |
| --- | --- | --- | --- |
| POST | `/api/media/upload` | `MediaController` | `upload` |

### Shop

| Méthode | Route | Contrôleur | Méthode |
| --- | --- | --- | --- |
| GET | `/api/shop/pdfs` | `ShopController` | `listPdf` |
| POST | `/api/shop/purchase` | `ShopController` | `purchase` |
| POST | `/api/shop/confirm` | `ShopController` | `confirm` |
| GET | `/api/shop/verify/{id}` | `ShopController` | `verify` |
| GET | `/api/shop/download/{id}` | `ShopController` | `download` |

### Admin (REST)

| Méthode | Route | Contrôleur | Méthode |
| --- | --- | --- | --- |
| GET | `/api/admin/dashboard` | `AdminDashboardController` | `index` |
| GET | `/api/admin/articles` | `AdminArticleController` | `index` |
| GET | `/api/admin/articles/{id}` | `AdminArticleController` | `show` |
| POST | `/api/admin/articles` | `AdminArticleController` | `store` |
| PUT/PATCH | `/api/admin/articles/{id}` | `AdminArticleController` | `update` |
| DELETE | `/api/admin/articles/{id}` | `AdminArticleController` | `destroy` |
| GET | `/api/admin/categories` | `AdminCategoryController` | `index` |
| GET | `/api/admin/categories/{id}` | `AdminCategoryController` | `show` |
| POST | `/api/admin/categories` | `AdminCategoryController` | `store` |
| PUT/PATCH | `/api/admin/categories/{id}` | `AdminCategoryController` | `update` |
| DELETE | `/api/admin/categories/{id}` | `AdminCategoryController` | `destroy` |
| GET | `/api/admin/tags` | `AdminTagController` | `index` |
| GET | `/api/admin/tags/{id}` | `AdminTagController` | `show` |
| POST | `/api/admin/tags` | `AdminTagController` | `store` |
| PUT/PATCH | `/api/admin/tags/{id}` | `AdminTagController` | `update` |
| DELETE | `/api/admin/tags/{id}` | `AdminTagController` | `destroy` |
| GET | `/api/admin/pages` | `AdminPageController` | `index` |
| GET | `/api/admin/pages/{id}` | `AdminPageController` | `show` |
| POST | `/api/admin/pages` | `AdminPageController` | `store` |
| PUT/PATCH | `/api/admin/pages/{id}` | `AdminPageController` | `update` |
| DELETE | `/api/admin/pages/{id}` | `AdminPageController` | `destroy` |
| GET | `/api/admin/media` | `AdminMediaController` | `index` |
| DELETE | `/api/admin/media/{id}` | `AdminMediaController` | `destroy` |
| GET | `/api/admin/orders` | `AdminOrderController` | `index` |
| GET | `/api/admin/stats` | `AdminStatsController` | `index` |
| GET | `/api/admin/users` | `AdminUserController` | `index` |
| GET | `/api/admin/users/{id}` | `AdminUserController` | `show` |
| POST | `/api/admin/users` | `AdminUserController` | `store` |
| PUT/PATCH | `/api/admin/users/{id}` | `AdminUserController` | `update` |
| DELETE | `/api/admin/users/{id}` | `AdminUserController` | `destroy` |
| GET | `/api/admin/comments` | `AdminCommentController` | `index` |
| GET | `/api/admin/comments/{id}` | `AdminCommentController` | `show` |
| POST | `/api/admin/comments` | `AdminCommentController` | `store` |
| PUT/PATCH | `/api/admin/comments/{id}` | `AdminCommentController` | `update` |
| DELETE | `/api/admin/comments/{id}` | `AdminCommentController` | `destroy` |
| GET | `/api/admin/pdf-editions` | `AdminPdfController` | `index` |
| GET | `/api/admin/pdf-editions/{id}` | `AdminPdfController` | `show` |
| POST | `/api/admin/pdf-editions` | `AdminPdfController` | `store` |
| PUT/PATCH | `/api/admin/pdf-editions/{id}` | `AdminPdfController` | `update` |
| DELETE | `/api/admin/pdf-editions/{id}` | `AdminPdfController` | `destroy` |
| GET | `/api/admin/pdfs` | `AdminPdfController` | `index` |

### Resources (REST)

| Méthode | Route | Contrôleur | Méthode |
| --- | --- | --- | --- |
| GET | `/api/users` | `UsersController` | `index` |
| GET | `/api/users/{id}` | `UsersController` | `show` |
| POST | `/api/users` | `UsersController` | `store` |
| PUT/PATCH | `/api/users/{id}` | `UsersController` | `update` |
| DELETE | `/api/users/{id}` | `UsersController` | `destroy` |
| GET | `/api/articles` | `ArticleController` | `index` |
| GET | `/api/articles/{id}` | `ArticleController` | `show` |
| POST | `/api/articles` | `ArticleController` | `store` |
| PUT/PATCH | `/api/articles/{id}` | `ArticleController` | `update` |
| DELETE | `/api/articles/{id}` | `ArticleController` | `destroy` |
| GET | `/api/categories` | `CategoryController` | `index` |
| GET | `/api/categories/{id}` | `CategoryController` | `show` |
| POST | `/api/categories` | `CategoryController` | `store` |
| PUT/PATCH | `/api/categories/{id}` | `CategoryController` | `update` |
| DELETE | `/api/categories/{id}` | `CategoryController` | `destroy` |
| GET | `/api/comments` | `CommentController` | `index` |
| GET | `/api/comments/{id}` | `CommentController` | `show` |
| POST | `/api/comments` | `CommentController` | `store` |
| PUT/PATCH | `/api/comments/{id}` | `CommentController` | `update` |
| DELETE | `/api/comments/{id}` | `CommentController` | `destroy` |
| GET | `/api/likes` | `LikeController` | `index` |
| GET | `/api/likes/{id}` | `LikeController` | `show` |
| POST | `/api/likes` | `LikeController` | `store` |
| PUT/PATCH | `/api/likes/{id}` | `LikeController` | `update` |
| DELETE | `/api/likes/{id}` | `LikeController` | `destroy` |
| GET | `/api/tags` | `TagController` | `index` |
| GET | `/api/tags/{id}` | `TagController` | `show` |
| POST | `/api/tags` | `TagController` | `store` |
| PUT/PATCH | `/api/tags/{id}` | `TagController` | `update` |
| DELETE | `/api/tags/{id}` | `TagController` | `destroy` |
| GET | `/api/pdf-editions` | `PdfEditionsController` | `index` |
| GET | `/api/pdf-editions/{id}` | `PdfEditionsController` | `show` |
| POST | `/api/pdf-editions` | `PdfEditionsController` | `store` |
| PUT/PATCH | `/api/pdf-editions/{id}` | `PdfEditionsController` | `update` |
| DELETE | `/api/pdf-editions/{id}` | `PdfEditionsController` | `destroy` |
| GET | `/api/orders` | `OrdersController` | `index` |
| GET | `/api/orders/{id}` | `OrdersController` | `show` |
| POST | `/api/orders` | `OrdersController` | `store` |
| PUT/PATCH | `/api/orders/{id}` | `OrdersController` | `update` |
| DELETE | `/api/orders/{id}` | `OrdersController` | `destroy` |
| GET | `/api/downloads` | `DownloadsController` | `index` |
| GET | `/api/downloads/{id}` | `DownloadsController` | `show` |
| POST | `/api/downloads` | `DownloadsController` | `store` |
| PUT/PATCH | `/api/downloads/{id}` | `DownloadsController` | `update` |
| DELETE | `/api/downloads/{id}` | `DownloadsController` | `destroy` |
| GET | `/api/pages` | `PagesController` | `index` |
| GET | `/api/pages/{id}` | `PagesController` | `show` |
| POST | `/api/pages` | `PagesController` | `store` |
| PUT/PATCH | `/api/pages/{id}` | `PagesController` | `update` |
| DELETE | `/api/pages/{id}` | `PagesController` | `destroy` |
| GET | `/api/media` | `MediaController` | `index` |
| GET | `/api/media/{id}` | `MediaController` | `show` |
| POST | `/api/media` | `MediaController` | `store` |
| PUT/PATCH | `/api/media/{id}` | `MediaController` | `update` |
| DELETE | `/api/media/{id}` | `MediaController` | `destroy` |
