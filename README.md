# Chess Manager

Aplicación Laravel para gestionar la liga de ajedrez de Mejorada del Campo.

## Docker

Arranque rápido:

```bash
docker compose up --build
```

La aplicación quedará disponible en [http://localhost:8000](http://localhost:8000).

En el arranque del contenedor se ejecutan automáticamente:

- `php artisan key:generate`
- `php artisan migrate --seed`

## Usuario de acceso

- Usuario: `admin`
- Contraseña: `admin`

## Base limpia

Si quieres dejar la base de datos vacía, excepto el usuario `admin`, usa:

```bash
docker compose exec app php artisan migrate:fresh --seed
```

Eso ejecuta el `DatabaseSeeder`, que ahora solo crea el usuario administrador.

## Datos de demo

Si quieres cargar datos de prueba para enseñar o revisar la aplicación, usa:

```bash
docker compose exec app php artisan db:seed --class=DemoDataSeeder
```

Ese seeder añade:

- alumnos de liga `local`
- alumnos de liga `infantil`
- temporada `Enero-Abril 2026`
- enfrentamientos de demo en ambas ligas
- clasificación inicial
- pagos de ejemplo con casos reales: `pagado`, `pendiente`, `exento` y `ausencia`

## Flujo recomendado

Entorno limpio:

```bash
docker compose exec app php artisan migrate:fresh --seed
```

Entorno con demo:

```bash
docker compose exec app php artisan migrate:fresh --seed
docker compose exec app php artisan db:seed --class=DemoDataSeeder
```
