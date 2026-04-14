# Chess Manager

Aplicacion Laravel para gestionar la liga de ajedrez de Mejorada del Campo.

## Docker

Arranque rapido:

```bash
docker compose up --build
```

La aplicacion quedara disponible en [http://localhost:8000](http://localhost:8000).

En el arranque del contenedor se ejecutan automaticamente:

- `php artisan key:generate`
- `php artisan migrate --seed`

## Usuario de acceso

- Email: `admin@chessmanager.test`
- Password: `password`

## Datos de prueba

El seeder crea:

- 12 alumnos de liga `local`
- 12 alumnos de liga `infantil`

Si necesitas relanzar los datos:

```bash
docker compose exec app php artisan migrate:fresh --seed
```
