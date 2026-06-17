# Control de acceso

Base: `GET|POST {{base_url}}/api/access-control`

Requiere autenticación y acceso al módulo `access-control`.

## Listar registros de acceso

```http
GET /api/access-control
Authorization: Bearer {{token}}
```

### Query params

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `status_id` | integer | 3=permitido, 4=denegado |
| `search` | string | Busca por nombre o identificación del miembro |
| `per_page` | integer | Paginación (1-100) |
| `page` | integer | Página actual |

### Respuesta

```json
{
  "status": "success",
  "message": "Accesos obtenidos correctamente",
  "data": [
    {
      "id": 1,
      "user": {
        "id": 10,
        "firstName": "Juan",
        "lastName": "Pérez",
        "identification": "1234567890"
      },
      "status": { "id": 3, "name": "Acceso permitido", "color": "#00ff00" },
      "createdAt": "2026-06-17T08:30:00.000000Z",
      "updatedAt": "2026-06-17T08:30:00.000000Z"
    }
  ]
}
```

## Registrar acceso

Valida si un miembro tiene membresía activa para la fecha indicada y registra el intento de acceso.

```http
POST /api/access-control
Authorization: Bearer {{token}}
Content-Type: application/json
```

### Body

```json
{
  "identification": "1234567890",
  "date": "2026-06-17"
}
```

| Campo | Requerido | Descripción |
|-------|-----------|-------------|
| `identification` | Sí | Documento de identidad del miembro |
| `date` | No | Fecha a validar (default: hoy) |

### Criterios de acceso permitido

1. Existe un usuario con rol Miembro y esa identificación
2. El usuario está activo (`status_id: 1`)
3. Tiene una membresía con estado activo (`status_id: 5`)
4. La fecha está entre `startDate` y `endDate` de la membresía

### Respuesta — acceso permitido

```json
{
  "status": "success",
  "message": "Acceso creado correctamente",
  "data": {
    "access": {
      "id": 1,
      "status": { "id": 3, "name": "Acceso permitido" },
      "createdAt": "2026-06-17T08:30:00.000000Z"
    },
    "member": {
      "id": 10,
      "firstName": "Juan",
      "lastName": "Pérez",
      "identification": "1234567890"
    },
    "subscription": {
      "id": 1,
      "code": "SUB-001",
      "startDate": "2026-06-01",
      "endDate": "2026-07-01"
    }
  }
}
```

### Respuesta — acceso denegado (miembro sin membresía activa)

HTTP 200 con `subscription: null` y status de acceso denegado:

```json
{
  "status": "success",
  "message": "Acceso creado correctamente",
  "data": {
    "access": {
      "id": 2,
      "status": { "id": 4, "name": "Acceso denegado" }
    },
    "member": { "id": 10, "firstName": "Juan", "lastName": "Pérez" },
    "subscription": null
  }
}
```

### Respuesta — miembro no encontrado

HTTP 404:

```json
{
  "status": "error",
  "message": "No se encontró un miembro con la identificación proporcionada."
}
```
