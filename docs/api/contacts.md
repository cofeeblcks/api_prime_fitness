# Contactos

Base: `GET|PUT|DELETE {{base_url}}/api/contacts`

Requiere autenticación y acceso al módulo `contacts`.

Los mensajes del formulario público (`POST /api/contact`) se persisten en la tabla `contacts` con estado inicial **Solicitud**.

## Estados

| ID | Nombre | Descripción |
|----|--------|-------------|
| `10` | Solicitud | Mensaje recibido, pendiente de respuesta |
| `11` | Contestado | Mensaje atendido |

Lookup: `GET /api/lookups/contact-statuses`

## Listar contactos

```http
GET /api/contacts
Authorization: Bearer {{token}}
```

### Query params

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `status_id` | integer | Filtrar por estado |
| `company_id` | integer | Filtrar por empresa |
| `search` | string | Buscar en nombre, correo, teléfono o mensaje |
| `startDate` | date | Fecha inicial (`created_at`) |
| `endDate` | date | Fecha final (`created_at`) |
| `per_page` | integer | Paginación |

### Respuesta

```json
{
  "status": "success",
  "message": "Contactos obtenidos correctamente",
  "data": [
    {
      "id": 1,
      "name": "Juan Pérez",
      "email": "juan@example.com",
      "phone": "3001234567",
      "message": "Quiero información sobre el plan mensual",
      "response": null,
      "respondedAt": null,
      "company": { "id": 1, "name": "Prime Fitness" },
      "status": { "id": 10, "name": "Solicitud", "color": "#F39C12" },
      "createdAt": "2026-06-20T00:00:00.000000Z",
      "updatedAt": "2026-06-20T00:00:00.000000Z"
    }
  ],
  "meta": {
    "pagination": {
      "total": 1,
      "count": 1,
      "per_page": 25,
      "current_page": 1,
      "total_pages": 1
    }
  }
}
```

## Ver contacto

```http
GET /api/contacts/{id}
Authorization: Bearer {{token}}
```

## Actualizar contacto

Marcar como contestado y registrar la respuesta interna del staff.

```http
PUT /api/contacts/{id}
Authorization: Bearer {{token}}
Content-Type: application/json
```

### Body

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `statusId` | integer | No* | `10` Solicitud, `11` Contestado |
| `response` | string | No | Nota de respuesta o seguimiento (máx. 2000) |

\* Al menos uno de los campos debe enviarse en actualización.

### Ejemplo

```json
{
  "statusId": 11,
  "response": "Se envió información del plan mensual por correo."
}
```

Al marcar como **Contestado**, se registra automáticamente `respondedAt`.

## Eliminar contacto

```http
DELETE /api/contacts/{id}
Authorization: Bearer {{token}}
```

Soft delete.

## Formulario público (landing)

Ver [contact.md](contact.md) — `POST /api/contact` (sin autenticación).
