# Laravel Task API

Proyecto pequeño para practicar GitHub Actions con Laravel, pruebas automatizadas, Docker y despliegue en Render.

## API

- `GET /api/tasks` lista tareas.
- `POST /api/tasks` crea una tarea.
- `PATCH /api/tasks/{id}` actualiza una tarea.
- `DELETE /api/tasks/{id}` elimina una tarea.
- `GET /up` health check para Render.

La API esta organizada de forma comun en Laravel:

- `routes/api.php` define las URLs.
- `app/Http/Controllers/TaskController.php` contiene la logica de cada endpoint.
- `app/Models/Task.php` representa la tabla `tasks`.
- `database/migrations/*_create_tasks_table.php` define la estructura de la tabla.

Ejemplo:

```bash
curl -X POST http://localhost:8000/api/tasks \
  -H "Content-Type: application/json" \
  -d '{"title":"Aprender GitHub Actions","description":"Correr tests y desplegar"}'
```

## Local

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
php artisan serve
```

Pruebas:

```bash
php artisan test
```

## Docker

```bash
docker build -t laravel-task-api .
docker run --rm -p 8080:8080 -e APP_ENV=production laravel-task-api
```

## GitHub Actions

El workflow `.github/workflows/ci.yml` hace:

- instala PHP 8.3
- instala dependencias con Composer
- prepara `.env`
- ejecuta pruebas
- construye la imagen Docker

El job `deploy` del mismo workflow llama un Deploy Hook de Render cuando haces push a `main` o cuando lo ejecutas manualmente. Ese job corre despues de tests y build Docker.

## Render

1. Sube este proyecto a GitHub.
2. En Render, crea un nuevo **Web Service** desde tu repo.
3. Elige **Docker** como runtime.
4. Usa el health check `/up`.
5. Copia el **Deploy Hook** de Render.
6. En GitHub, ve a `Settings > Secrets and variables > Actions`.
7. Crea el secreto `RENDER_DEPLOY_HOOK_URL` con esa URL.

Para una demo de curso, SQLite es suficiente. Para una app real, usa PostgreSQL en Render y cambia las variables `DB_*`.
