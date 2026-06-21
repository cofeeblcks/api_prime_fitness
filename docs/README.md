# Prime Fitness API — Documentación

## URL base

```
{{base_url}}/api
```

Configura la variable `base_url` en Postman (ejemplo: `http://localhost:8000`).

## Autenticación

La API usa **Laravel Sanctum** con tokens Bearer.

### Login

```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "admin@example.com",
  "password": "password"
}
```

Respuesta exitosa:

```json
{
  "status": "success",
  "data": {
    "access_token": "1|...",
    "token_type": "Bearer",
    "user": { }
  }
}
```

Incluye el token en todas las peticiones protegidas:

```http
Authorization: Bearer {{token}}
```

## Convención de respuestas

### Éxito

```json
{
  "status": "success",
  "message": "Mensaje descriptivo",
  "data": { },
  "meta": { }
}
```

### Error

```json
{
  "status": "error",
  "message": "Descripción del error",
  "errors": { }
}
```

### Códigos HTTP comunes

| Código | Descripción |
|--------|-------------|
| 200 | Operación exitosa |
| 401 | No autenticado o sin permiso de módulo |
| 403 | Acceso prohibido |
| 404 | Recurso no encontrado |
| 422 | Error de validación |
| 500 | Error interno del servidor |

## Control de acceso por módulo

Cada recurso requiere que el rol del usuario autenticado tenga acceso al módulo correspondiente (`module.access` middleware).

| Módulo | Ruta API | Middleware |
|--------|----------|------------|
| Usuarios | `/users` | `users` |
| Miembros | `/members` | `members` |
| Entrenadores | `/trainers` | `trainers` |
| Planes | `/plans` | `plans` |
| Membresías | `/payments` | `payments` |
| Control de acceso | `/access-control` | `access-control` |
| Empresa | `/companies` | `company` |
| Contactos | `/contacts` | `contacts` |

## Lookups (sin autenticación)

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/lookups/identification-types` | Tipos de identificación |
| GET | `/lookups/roles` | Roles del sistema |
| GET | `/lookups/plans` | Planes activos |
| GET | `/lookups/suscription-types` | Tipos de membresía |
| GET | `/lookups/link-types` | Tipos de enlace (redes sociales) |
| GET | `/lookups/contact-statuses` | Estados de contacto (Solicitud, Contestado) |

Parámetros opcionales: `page`, `per_page` (máx. 100).

## Plan de consumo (React + React Native)

- [Plan de implementación — consumo de API](api-consumption-plan.md)

## Documentación por módulo

- [Miembros](api/members.md)
- [Entrenadores](api/trainers.md)
- [Planes](api/plans.md)
- [Membresías](api/subscriptions.md)
- [Control de acceso](api/access-control.md)
- [Empresa](api/company.md)
- [Contacto (landing)](api/contact.md)
- [Contactos (gestión)](api/contacts.md)

## Endpoints públicos

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/companies/{id}` | Información completa de la empresa (sin autenticación) |
| POST | `/contact` | Formulario de contacto de la landing (throttle 5/min) |

## Colección Postman

Importa el archivo [`postman/prime-fitness-api.postman_collection.json`](postman/prime-fitness-api.postman_collection.json).

Variables de colección:

| Variable | Descripción |
|----------|-------------|
| `base_url` | URL del servidor (ej. `http://localhost:8000`) |
| `token` | Token Bearer (se guarda automáticamente al hacer login) |
