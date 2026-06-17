# Prime Fitness API

API REST para la gestión de un gimnasio: usuarios, miembros, entrenadores, planes, membresías y control de acceso.

## Stack

- PHP 8.4
- Laravel 13
- Laravel Sanctum (autenticación por token)
- MySQL

## Módulos del sistema

| Módulo | Ruta API | Descripción |
|--------|----------|-------------|
| Usuarios | `/api/users` | CRUD de usuarios del sistema |
| Miembros | `/api/members` | CRUD de miembros (usuarios con rol Miembro) |
| Entrenadores | `/api/trainers` | CRUD de entrenadores (usuarios con rol Entrenador) |
| Planes | `/api/plans` | CRUD de planes y sus detalles |
| Membresías | `/api/payments` | CRUD de suscripciones/membresías |
| Control de acceso | `/api/access-control` | Registro y consulta de accesos al gimnasio |

El acceso a cada módulo depende del rol del usuario autenticado. Los permisos se definen en la tabla `modules` y se validan con el middleware `module.access`.

## Requisitos

- PHP >= 8.3
- Composer
- MySQL
- Extensiones PHP: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`

## Instalación

```bash
# Clonar e instalar dependencias
composer install

# Configurar entorno
cp .env.example .env
php artisan key:generate

# Base de datos
php artisan migrate --seed

# Servidor de desarrollo
php artisan serve
```

## Documentación

La documentación completa de la API está en la carpeta [`docs/`](docs/README.md):

- [**Índice y convenciones**](docs/README.md) — autenticación, formato de respuestas y códigos HTTP
- [Miembros](docs/api/members.md)
- [Entrenadores](docs/api/trainers.md)
- [Planes](docs/api/plans.md)
- [Membresías](docs/api/subscriptions.md)
- [Control de acceso](docs/api/access-control.md)

### Colección Postman

Importa [`docs/postman/prime-fitness-api.postman_collection.json`](docs/postman/prime-fitness-api.postman_collection.json) para probar los endpoints. Ejecuta primero **Auth > Login** para obtener el token automáticamente.

## Autenticación rápida

```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "admin@example.com",
  "password": "password"
}
```

Incluye el token en las peticiones protegidas:

```http
Authorization: Bearer {access_token}
```

## Desarrollo

```bash
# Formatear código
vendor/bin/pint --dirty

# Ejecutar tests
php artisan test --compact

# Ver rutas API
php artisan route:list --path=api
```

## Licencia

Este proyecto es software de código abierto bajo la [licencia MIT](https://opensource.org/licenses/MIT).
