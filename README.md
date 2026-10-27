# TuringInsights

Portal de recursos técnicos sobre transformación digital, analítica y desarrollo. El proyecto consiste en una aplicación fullstack que permite explorar un catálogo de artículos organizados por tema, filtrar, buscar y leer el contenido completo. Los usuarios con rol de administrador gestionan los recursos desde un panel privado.

El proyecto se desarrolló como parte del periodo de evaluación técnica para la vacante de Desarrollo de Software en Turing Inteligencia Artificial.

## Stack

| Capa | Tecnología |
|---|---|
| Backend | Laravel 11 (PHP 8.2+) |
| Base de datos | MySQL 8+ |
| Autenticación | JWT (tymon/jwt-auth) |
| Frontend | Vue 3 + Vite |
| Estilos | Tailwind CSS 4 |
| Iconos | Tabler Icons |
| Cliente HTTP | Axios |
| Enrutamiento SPA | Vue Router 4 |

## Estructura del repositorio

```text
turing-insights/
├── backend/     API REST en Laravel
├── frontend/    SPA en Vue 3
└── README.md
```

El proyecto se organiza como un monorepositorio con separación lógica por capas. El documento de la prueba permite uno o varios repositorios y marca la separación en repos como recomendable, no obligatoria.

## Requisitos

- PHP 8.2 o superior
- Composer 2
- Node.js 20 o superior
- npm 10 o superior
- MySQL 8 o superior
- Git

## Instalación

### Backend

Los comandos siguientes se ejecutan desde la carpeta `backend/`:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
php artisan migrate:fresh --seed
php artisan serve
```

La API queda disponible en `http://127.0.0.1:8000/api`.

### Frontend

Los comandos siguientes se ejecutan desde la carpeta `frontend/`:

```bash
npm install
cp .env.example .env
npm run dev
```

La aplicación queda disponible en `http://localhost:5173`.

## Variables de entorno

### Backend (`backend/.env`)

```env
APP_NAME=TuringInsights
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=turing_insights
DB_USERNAME=root
DB_PASSWORD=

JWT_SECRET=
```

La variable `JWT_SECRET` se genera automáticamente mediante el comando `php artisan jwt:secret`.

### Frontend (`frontend/.env`)

```env
VITE_API_URL=http://127.0.0.1:8000/api
```

## Base de datos

El esquema está compuesto por tres tablas en tercera forma normal (3NF).

### `users`

| Columna | Tipo | Restricciones |
|---|---|---|
| id | bigint unsigned | PK, autoincremental |
| name | varchar(255) | obligatorio |
| email | varchar(255) | único, obligatorio |
| email_verified_at | timestamp | nullable |
| password | varchar(255) | obligatorio, hasheado |
| role | enum('admin','user') | default 'user' |
| remember_token | varchar(100) | nullable |
| created_at, updated_at | timestamps | |

### `topics`

| Columna | Tipo | Restricciones |
|---|---|---|
| id | bigint unsigned | PK, autoincremental |
| name | varchar(255) | obligatorio |
| slug | varchar(255) | único, obligatorio |
| description | text | nullable |
| created_at, updated_at | timestamps | |

### `resources`

| Columna | Tipo | Restricciones |
|---|---|---|
| id | bigint unsigned | PK, autoincremental |
| title | varchar(255) | obligatorio |
| slug | varchar(255) | único, obligatorio |
| excerpt | varchar(500) | obligatorio |
| content | longtext | obligatorio |
| cover_url | varchar(255) | nullable |
| topic_id | bigint unsigned | FK a topics, restrictOnDelete |
| author_id | bigint unsigned | FK a users, restrictOnDelete |
| published_at | timestamp | nullable, indexado |
| created_at, updated_at | timestamps | |

### Relaciones

- `topics` 1:N `resources`
- `users` 1:N `resources` (como autor)

### Justificación de 3NF

- **`users`:** todos los atributos dependen directamente de la clave primaria `id`. `email` funciona como clave candidata única. No existen dependencias transitivas.
- **`topics`:** `name`, `slug` y `description` dependen de `id`. `slug` es clave candidata única. No hay redundancia.
- **`resources`:** `title`, `slug`, `excerpt`, `content`, `cover_url` y `published_at` dependen de `id`. `topic_id` y `author_id` son claves foráneas que referencian sus entidades; los nombres de tema y autor no se duplican dentro de `resources`.

### Decisiones técnicas

- **`published_at` indexado** para acelerar filtros y ordenamiento por fecha.
- **`restrictOnDelete`** en las claves foráneas para preservar integridad referencial: no se permite eliminar un tema o un usuario con recursos asociados.
- **`slug` autogenerado** a partir del título o nombre, garantizando unicidad.

## API

La API sigue el estilo REST y expone los siguientes endpoints sobre la base URL `http://127.0.0.1:8000/api`.

### Endpoints públicos

| Método | Ruta | Descripción |
|---|---|---|
| GET | `/resources` | Lista recursos publicados con paginación |
| GET | `/resources/{slug}` | Detalle de un recurso publicado |
| GET | `/topics` | Lista todos los temas |

### Endpoints de autenticación

| Método | Ruta | Auth | Descripción |
|---|---|---|---|
| POST | `/login` | No | Inicia sesión y devuelve un JWT |
| POST | `/logout` | Bearer | Invalida el token actual |
| GET | `/me` | Bearer | Devuelve el usuario autenticado |

### Endpoints de administración

Requieren token JWT con rol `admin`.

| Método | Ruta | Descripción |
|---|---|---|
| POST | `/resources` | Crea un recurso |
| PUT | `/resources/{id}` | Actualiza un recurso |
| DELETE | `/resources/{id}` | Elimina un recurso |
| POST | `/topics` | Crea un tema |
| PUT | `/topics/{id}` | Actualiza un tema |
| DELETE | `/topics/{id}` | Elimina un tema (409 si tiene recursos) |

### Parámetros del listado de recursos

| Parámetro | Tipo | Descripción |
|---|---|---|
| topic | string | Filtra por slug del tema |
| search | string | Busca en título y extracto |
| sort | enum | `recent` (default) u `old` |
| per_page | integer | Tamaño de página entre 1 y 50 (default 6) |
| page | integer | Número de página |

### Ejemplo de respuesta

```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "title": "Introducción a Vue 3 y la Composition API",
      "slug": "introduccion-a-vue-3-y-la-composition-api",
      "excerpt": "Aprende cómo la Composition API organiza la lógica...",
      "cover_url": "https://picsum.photos/seed/.../800/450",
      "topic": { "id": 1, "name": "Desarrollo web", "slug": "desarrollo-web" },
      "author": { "id": 1, "name": "Administrador Turing" },
      "published_at": "2026-10-26T09:00:00.000000Z"
    }
  ],
  "last_page": 2,
  "per_page": 6,
  "total": 9
}
```

### Códigos de estado

| Código | Significado |
|---|---|
| 200 | OK |
| 201 | Recurso creado |
| 204 | Recurso eliminado |
| 401 | No autenticado o token inválido |
| 403 | Autenticado pero sin permisos de administrador |
| 404 | Recurso no encontrado |
| 409 | Conflicto (tema con recursos asociados) |
| 422 | Error de validación |

## Autenticación

La API utiliza JWT (JSON Web Tokens) mediante el paquete `tymon/jwt-auth`. El flujo es el siguiente:

1. El cliente envía `email` y `password` a `POST /api/login`.
2. La API devuelve un `access_token` firmado.
3. El cliente incluye el token en cada petición protegida con el header `Authorization: Bearer <token>`.
4. El middleware `auth:api` valida el token antes de permitir el acceso.
5. El middleware `admin` verifica que el usuario tenga rol `admin` para las rutas de escritura.
6. El frontend almacena el token en `localStorage` y lo adjunta automáticamente mediante un interceptor de Axios.

### Roles

| Capacidad | Admin | User |
|---|---|---|
| Ver catálogo público | Sí | Sí |
| Ver detalle de recurso | Sí | Sí |
| Filtrar y buscar | Sí | Sí |
| Acceder a `/admin` | Sí | No |
| Crear, editar, eliminar recursos | Sí | No |
| Crear, editar, eliminar temas | Sí | No |

### Credenciales de prueba

| Rol | Correo | Contraseña |
|---|---|---|
| Admin | admin@turing-ia.com | password |
| User | user@turing-ia.com | password |

Las credenciales están disponibles después de ejecutar `php artisan migrate:fresh --seed`.

## Patrones y prácticas aplicadas

- **MVC:** estructura base de Laravel con modelos, controladores y rutas separadas.
- **Form Requests:** `StoreResourceRequest` y `UpdateResourceRequest` encapsulan la validación de entrada.
- **Middleware:** `auth:api` y `EnsureUserIsAdmin` protegen rutas por autenticación y rol.
- **Scopes de Eloquent:** `scopePublished` filtra recursos publicados de forma reutilizable.
- **Selección de columnas y relaciones:** los controladores limitan los datos expuestos en cada respuesta.
- **Interceptores de Axios:** manejo centralizado del token y de errores 401 en el frontend.
- **Route Guards de Vue Router:** protección de rutas por autenticación y rol en el cliente.

## Colección Postman

La colección de pruebas se encuentra en `backend/docs/postman/turing-insights.postman_collection.json`.

El flujo de uso es el siguiente:

1. Importar el archivo en Postman.
2. Configurar la variable `base_url` con el valor `http://127.0.0.1:8000/api`.
3. Ejecutar **Login admin** para guardar el token en `jwt_token`.
4. Ejecutar **Login user** para guardar el token de usuario en `jwt_token_user`.

La colección incluye pruebas de endpoints públicos, autenticación JWT y operaciones de administración, incluyendo casos de error (401, 403, 404, 409, 422).

## Comandos útiles

### Backend

| Comando | Descripción |
|---|---|
| `php artisan migrate:fresh --seed` | Recrea la base de datos con datos de prueba |
| `php artisan db:show --counts` | Muestra el conteo de tablas |
| `php artisan route:list --path=api` | Lista las rutas de la API |
| `php artisan config:clear` | Limpia la caché de configuración |

### Frontend

| Comando | Descripción |
|---|---|
| `npm run dev` | Servidor de desarrollo |
| `npm run build` | Compila para producción |
| `npm run preview` | Previsualiza el build |

## Estado del proyecto

- [x] Modelo de datos en 3NF
- [x] API REST con filtros, búsqueda y paginación
- [x] Autenticación JWT
- [x] Autorización por rol
- [x] CRUD de recursos y temas
- [x] Catálogo público con filtros y "Cargar más"
- [x] Vista de detalle de recurso
- [x] Login y dashboard administrativo
- [x] Diseño responsive
- [x] Colección Postman documentada

## Autor

Proyecto desarrollado como parte del periodo de evaluación técnica de Turing Inteligencia Artificial.
