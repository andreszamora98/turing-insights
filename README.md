# TuringInsights

Portal de recursos técnicos sobre transformación digital, analítica y desarrollo. El proyecto consiste en una aplicación fullstack que permite explorar un catálogo de artículos organizados por tema, filtrar, buscar y leer el contenido completo. Los usuarios con rol de administrador gestionan los recursos desde un panel privado. Incluye un formulario de contacto con validación en dos capas.

El proyecto se desarrolló como parte del periodo de evaluación técnica para la vacante de Desarrollo de Software en Turing Inteligencia Artificial.

## URLs en producción

| Servicio | URL |
|---|---|
| Frontend | https://turing-insights.vercel.app |
| Backend (API) | https://turing-insights-api.onrender.com |
| Health check | https://turing-insights-api.onrender.com/up |

> **Nota sobre el cold start:** el plan gratuito de Render suspende el backend tras 15 minutos de inactividad. La primera petición después de ese periodo puede tardar hasta 60 segundos. Las siguientes responden de inmediato.

## Stack

| Capa | Tecnología |
|---|---|
| Backend | Laravel 11 |
| PHP local | 8.4 |
| PHP en producción | 8.4 (imagen `php:8.4-apache`) |
| Base de datos local | MySQL 8 |
| Base de datos en producción | MySQL 8 gestionado en Aiven |
| Autenticación | JWT (tymon/jwt-auth) |
| Frontend | Vue 3 + Vite |
| Estilos | Tailwind CSS 4 |
| Iconos | Tabler Icons |
| Cliente HTTP | Axios |
| Enrutamiento SPA | Vue Router 4 |
| Contenedor | Docker multi-stage (Node 20 + PHP 8.4 Apache) |
| Despliegue backend | Render (Web Service, Docker, plan Free) |
| Despliegue frontend | Vercel (Static Site, plan Hobby) |

## Estructura del repositorio

```text
turing-insights/
├── backend/
│   ├── Dockerfile
│   ├── docker-entrypoint.sh
│   ├── public/images/covers/
│   └── ...
├── docs/postman/
│   ├── turing-insights.postman_collection.json
├── frontend/
│   ├── vercel.json
│   ├── public/images/
│   └── ...
└── README.md
```

El proyecto se organiza como un monorepositorio con separación lógica por capas. El documento de la prueba permite uno o varios repositorios y marca la separación en repos como recomendable, no obligatoria.

## Requisitos

- PHP 8.4
- Composer 2
- Node.js 20 o superior
- npm 10 o superior
- MySQL 8 o superior
- Git

## Instalación local

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
JWT_TTL=60

FRONTEND_URL=http://localhost:5173
```

`JWT_SECRET` se genera automáticamente con `php artisan jwt:secret`. `FRONTEND_URL` se usa en la configuración de CORS.

### Frontend (`frontend/.env`)

```env
VITE_API_URL=http://127.0.0.1:8000/api
```

### Variables en producción (Render)

La misma estructura de `backend/.env`, con estos cambios:

| Variable | Valor en producción |
|---|---|
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_URL` | `https://turing-insights-api.onrender.com` |
| `FRONTEND_URL` | `https://turing-insights.vercel.app` |
| `LOG_CHANNEL` | `stderr` |
| `SESSION_DRIVER` | `cookie` |
| `CACHE_STORE` | `file` |
| `QUEUE_CONNECTION` | `sync` |
| `APP_KEY` | mismo valor que en local |
| `JWT_SECRET` | mismo valor que en local |
| `DB_*` | credenciales de Aiven |

> **Importante:** `APP_KEY` y `JWT_SECRET` deben ser idénticos entre local y producción para que los tokens JWT generados en cualquiera de los dos entornos sean válidos en el otro.

## Base de datos

El esquema principal está compuesto por tres tablas en tercera forma normal (3NF), más una tabla auxiliar para el canal de contacto.

### `users`

| Columna | Tipo | Restricciones |
|---|---|---|
| `id` | bigint unsigned | PK, autoincremental |
| `name` | varchar(255) | obligatorio |
| `email` | varchar(255) | único, obligatorio |
| `email_verified_at` | timestamp | nullable |
| `password` | varchar(255) | obligatorio, hasheado |
| `role` | enum('admin','user') | default 'user' |
| `remember_token` | varchar(100) | nullable |
| `created_at`, `updated_at` | timestamps | |

### `topics`

| Columna | Tipo | Restricciones |
|---|---|---|
| `id` | bigint unsigned | PK, autoincremental |
| `name` | varchar(255) | obligatorio |
| `slug` | varchar(255) | único, obligatorio |
| `description` | text | nullable |
| `created_at`, `updated_at` | timestamps | |

### `resources`

| Columna | Tipo | Restricciones |
|---|---|---|
| `id` | bigint unsigned | PK, autoincremental |
| `title` | varchar(255) | obligatorio |
| `slug` | varchar(255) | único, obligatorio |
| `excerpt` | varchar(500) | obligatorio |
| `content` | longtext | obligatorio |
| `cover_url` | varchar(255) | nullable |
| `topic_id` | bigint unsigned | FK a `topics`, `restrictOnDelete` |
| `author_id` | bigint unsigned | FK a `users`, `restrictOnDelete` |
| `published_at` | timestamp | nullable, indexado |
| `created_at`, `updated_at` | timestamps | |

### `contact_messages`

Tabla auxiliar para el formulario de contacto. No forma parte del modelo editorial y no afecta el 3NF del catálogo.

| Columna | Tipo | Restricciones |
|---|---|---|
| `id` | bigint unsigned | PK, autoincremental |
| `name` | varchar(100) | obligatorio |
| `email` | varchar(150) | obligatorio |
| `subject` | varchar(30) | obligatorio, validado en aplicación |
| `message` | text | obligatorio |
| `ip_address` | varchar(45) | nullable |
| `created_at`, `updated_at` | timestamps | |

### Relaciones

- `topics` 1:N `resources`
- `users` 1:N `resources` (como autor)

### Justificación de 3NF

- **users:** todos los atributos dependen directamente de la clave primaria `id`. `email` funciona como clave candidata única. No existen dependencias transitivas.
- **topics:** `name`, `slug` y `description` dependen de `id`. `slug` es clave candidata única. No hay redundancia.
- **resources:** `title`, `slug`, `excerpt`, `content`, `cover_url` y `published_at` dependen de `id`. `topic_id` y `author_id` son claves foráneas que referencian sus entidades; los nombres de tema y autor no se duplican dentro de `resources`.
- **contact_messages:** cada atributo depende de `id`. No tiene dependencias transitivas. Se mantiene separada del catálogo para no mezclar la lógica editorial con la operativa.

### Decisiones técnicas

- `published_at` indexado para acelerar filtros y ordenamiento por fecha.
- `restrictOnDelete` en las claves foráneas del catálogo para preservar integridad referencial.
- `slug` autogenerado a partir del título o nombre, garantizando unicidad.
- `ip_address` en `contact_messages` para trazabilidad de los mensajes recibidos.

## API

La API sigue el estilo REST y expone los siguientes endpoints sobre la base URL `http://127.0.0.1:8000/api` en local o `https://turing-insights-api.onrender.com/api` en producción.

### Endpoints públicos

| Método | Ruta | Descripción |
|---|---|---|
| GET | `/resources` | Lista recursos publicados con paginación |
| GET | `/topics` | Lista todos los temas |
| POST | `/contact` | Envía un mensaje de contacto (throttle: 5 por minuto) |

### Endpoints de autenticación

| Método | Ruta | Auth | Descripción |
|---|---|---|---|
| POST | `/login` | No | Inicia sesión y devuelve un JWT |
| POST | `/logout` | Bearer | Invalida el token actual |
| GET | `/me` | Bearer | Devuelve el usuario autenticado |

### Endpoints que requieren autenticación

| Método | Ruta | Auth | Descripción |
|---|---|---|---|
| GET | `/resources/{slug}` | Bearer | Detalle de un recurso publicado |

> **Nota:** el detalle del recurso requiere autenticación. El catálogo público muestra las tarjetas con extracto, pero el contenido completo solo está disponible para usuarios autenticados, lo que da sentido al rol `user`.

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
| `topic` | string | Filtra por slug del tema |
| `search` | string | Busca en título y extracto |
| `sort` | enum | `recent` (default) u `old` |
| `per_page` | integer | Tamaño de página entre 1 y 50 (default 6) |
| `page` | integer | Número de página |

### Parámetros del formulario de contacto

| Campo | Tipo | Reglas |
|---|---|---|
| `name` | string | obligatorio, máximo 100 |
| `email` | string | obligatorio, formato email, máximo 150 |
| `subject` | enum | obligatorio, valores: `consulta`, `contenido`, `error`, `otro` |
| `message` | string | obligatorio, mínimo 10, máximo 1000 |

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
      "cover_url": "http://127.0.0.1:8000/images/covers/introduccion-vue-3.jpg",
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
| 429 | Demasiadas peticiones (throttle del contacto) |

## Autenticación

La API utiliza JWT (JSON Web Tokens) mediante el paquete `tymon/jwt-auth`. El flujo es el siguiente:

1. El cliente envía `email` y `password` a `POST /api/login`.
2. La API devuelve un `access_token` firmado.
3. El cliente incluye el token en cada petición protegida con el header `Authorization: Bearer <token>`.
4. El middleware `auth:api` valida el token antes de permitir el acceso.
5. El middleware `admin` verifica que el usuario tenga rol `admin` para las rutas de escritura.
6. El frontend almacena el token en `localStorage` y lo adjunta automáticamente mediante un interceptor de Axios.
7. Ante una respuesta 401, el interceptor limpia el token y redirige al login.

### Roles

| Capacidad | Admin | User |
|---|---|---|
| Ver catálogo público | Sí | Sí |
| Ver detalle de recurso | Sí | Sí |
| Filtrar y buscar | Sí | Sí |
| Enviar mensaje de contacto | Sí | Sí |
| Acceder a `/admin` | Sí | No |
| Crear, editar, eliminar recursos | Sí | No |
| Crear, editar, eliminar temas | Sí | No |

### Credenciales de prueba

| Rol | Correo | Contraseña |
|---|---|---|
| Admin | admin@turing-ia.com | password |
| User | user@turing-ia.com | password |

Las credenciales están disponibles después de ejecutar `php artisan migrate:fresh --seed` en local, o al iniciar el contenedor en producción (el entrypoint ejecuta seeders idempotentes).

## Frontend

### Vistas

| Ruta | Vista | Descripción |
|---|---|---|
| `/` | Home | Catálogo público con hero, temas, filtros, grid de recursos y sección informativa |
| `/recursos/:slug` | ResourceDetail | Artículo completo con portada, metadatos y contenido |
| `/login` | LoginView | Formulario de inicio de sesión con JWT |
| `/admin` | AdminDashboard | Panel de gestión con paginación, búsqueda, creación, edición y eliminación |
| `/acerca-de` | AboutView | Página informativa del proyecto |
| `/contacto` | ContactView | Formulario de contacto con validación en dos capas |

### Componentes destacados

- **AppHeader:** navegación dinámica con menú hamburguesa en móvil y header sticky.
- **TopicCards:** tarjetas de temas con iconos Tabler, filtran el catálogo al hacer clic.
- **FilterBar:** búsqueda, filtros y ordenamiento con debounce.
- **ResourceCard:** tarjeta de recurso con imagen fallback, etiqueta de tema, título, extracto, autor y fecha.
- **InfoSection:** bloque informativo con tres pilares (Aprende, Descubre, Construye).
- **RevealOnScroll:** wrapper con `IntersectionObserver` que revela contenido al hacer scroll.

### Composables y utilidades

- **useAuth:** estado reactivo de autenticación, login, logout y refresh de perfil.
- **useSectionNav:** navegación coordinada a secciones (`#recursos`, `#temas`) con compensación de altura del header sticky.
- **services/api.js:** instancia de Axios con interceptores de request (adjunta el token) y response (maneja 401).

### Diseño y experiencia

- **Responsive:** breakpoints en 375px, 768px y 1280px. Catálogo en 3/2/1 columnas.
- **Animaciones:** transiciones de página, revelado al scroll, skeletons de carga, spinners en botones y `TransitionGroup` en listas.
- **Accesibilidad:** `prefers-reduced-motion` respetado; botones con `aria-label` en móvil.
- **Imágenes:** portadas locales servidas por el backend con fallback `/images/fallback-cover.jpg` cuando `cover_url` es nulo o falla la carga.

## Patrones y prácticas aplicadas

- **MVC:** estructura base de Laravel con modelos, controladores y rutas separadas.
- **Form Requests:** `StoreResourceRequest`, `UpdateResourceRequest` y `ContactRequest` encapsulan la validación de entrada.
- **Middleware:** `auth:api` y `EnsureUserIsAdmin` protegen rutas por autenticación y rol.
- **Scopes de Eloquent:** `scopePublished` filtra recursos publicados de forma reutilizable.
- **Selección de columnas y relaciones:** los controladores limitan los datos expuestos en cada respuesta.
- **Interceptores de Axios:** manejo centralizado del token y de errores 401 en el frontend.
- **Route Guards de Vue Router:** protección de rutas por autenticación y rol en el cliente.
- **Validación en dos capas:** cliente (feedback inmediato) y servidor (autoridad final).
- **Rate limiting:** `throttle:5,1` en el endpoint de contacto.
- **Trust proxies:** `$middleware->trustProxies(at: '*')` para detectar HTTPS y IP real detrás del proxy de Render.
- **Seeders idempotentes:** `firstOrCreate` / `updateOrCreate` permiten ejecutar seeders en cada arranque sin duplicar datos.

## Colección Postman

La colección de pruebas se encuentra en `docs/postman/turing-insights.postman_collection.json`.

### Flujo de uso

1. Importar el archivo en Postman.
2. Configurar la variable `base_url` con `http://127.0.0.1:8000/api` en local o `https://turing-insights-api.onrender.com/api` en producción.
3. Ejecutar **Login admin** para guardar el token en `jwt_token`.
4. Ejecutar **Login user** para guardar el token de usuario en `jwt_token_user`.
5. Ejecutar los requests protegidos. El token expira a los 60 minutos (`JWT_TTL`).

### Carpetas

| Carpeta | Contenido |
|---|---|
| 01 · Público | Listado, paginación, filtros, búsqueda, orden y listado de temas |
| 02 · Auth · JWT | Login admin/user, Me, Show by slug con token, 401 sin token, Logout |
| 03 · Resources · Admin | CRUD de recursos y temas con token admin, 403 con token user |
| 04 · Contacto | Envío de mensajes, validación 422 y throttle 429 |

## Despliegue

### Arquitectura

| Componente | Plataforma | Plan |
|---|---|---|
| Frontend | Vercel | Hobby (gratuito) |
| Backend | Render | Free (Docker) |
| Base de datos | Aiven | Free (MySQL) |

### Backend (Render)

- **Root Directory:** `backend`
- **Runtime:** Docker
- **Dockerfile:** `backend/Dockerfile` (multi-stage Node 20 + PHP 8.4 Apache)
- **Entrypoint:** `backend/docker-entrypoint.sh` (migraciones, seeders y arranque de Apache)
- **Health Check Path:** `/up`

### Frontend (Vercel)

- **Root Directory:** `frontend`
- **Framework:** Vite
- **Build Command:** `npm run build`
- **Output Directory:** `dist`
- **Rewrites:** `frontend/vercel.json` redirige todas las rutas a `index.html` para el manejo de rutas de Vue Router.

### Base de datos (Aiven)

- MySQL 8 gestionado.
- Credenciales configuradas como variables de entorno en Render (`DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).
- Puerto TLS de Aiven.

### Flujo de despliegue

1. Push a `main` en GitHub.
2. Render detecta el cambio y reconstruye la imagen Docker.
3. Al iniciar el contenedor, el entrypoint ejecuta migraciones y seeders.
4. Vercel detecta el cambio y reconstruye el frontend.
5. Ambos servicios quedan disponibles en sus URLs públicas.

## Comandos útiles

### Backend

| Comando | Descripción |
|---|---|
| `php artisan migrate:fresh --seed` | Recrea la base de datos con datos de prueba |
| `php artisan db:show --counts` | Muestra el conteo de tablas |
| `php artisan route:list --path=api` | Lista las rutas de la API |
| `php artisan config:clear` | Limpia la caché de configuración |
| `php artisan jwt:secret` | Genera un nuevo `JWT_SECRET` |

### Docker (backend)

| Comando | Descripción |
|---|---|
| `docker build -t turing-insights-api backend/` | Construye la imagen |
| `docker run -p 8000:80 --env-file backend/.env turing-insights-api` | Ejecuta el contenedor en local |

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
- [x] Detalle de recurso protegido por autenticación
- [x] Catálogo público con filtros y "Cargar más"
- [x] Login y dashboard administrativo con paginación
- [x] Formulario de contacto con validación en dos capas y rate limiting
- [x] Páginas informativas (Acerca de, Contacto)
- [x] Diseño responsive
- [x] Animaciones y transiciones con `prefers-reduced-motion`
- [x] Portadas locales con imagen fallback
- [x] Colección Postman documentada
- [x] Despliegue en producción (Vercel + Render + Aiven)

## Autor

Proyecto desarrollado como parte del periodo de evaluación técnica de Turing Inteligencia Artificial.
