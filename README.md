# TuringInsights

Portal de recursos técnicos sobre transformación digital, analítica y desarrollo.

## Stack
- Laravel 11 (PHP 8.2+)
- MySQL
- Vue 3 + Vite + Tailwind (frontend, próxima iteración)

## Requisitos
- PHP 8.2+
- Composer
- MySQL 8+

## Instalación
1. `composer install`
2. Copia `.env.example` a `.env` y configura la BD.
3. `php artisan key:generate`
4. `php artisan migrate:fresh --seed`
5. `php artisan serve`

## Endpoints
| Método | Ruta | Descripción |
|---|---|---|
| GET | `/api/resources` | Lista recursos publicados (paginado) |
| GET | `/api/resources/{slug}` | Detalle de un recurso publicado |

### Filtros del listado
- `?topic=slug` — filtra por tema
- `?search=texto` — busca en título y extracto
- `?sort=recent|old` — ordena por fecha
- `?per_page=N` — tamaño de página (1–50, default 6)

## Base de datos
- `users` (id, name, email, password, role, timestamps)
- `topics` (id, name, slug, description, timestamps)
- `resources` (id, title, slug, excerpt, content, cover_url, topic_id FK, author_id FK, published_at, timestamps)

## Colección Postman
Importa `docs/postman/turing-insights.postman_collection.json` en Postman.
Configura la variable `base_url` (por defecto `http://127.0.0.1:8000/api`).

### Credenciales de prueba
- admin@turing-ia.com / password
- user@turing-ia.com / password