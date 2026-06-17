# Membresías

Base: `GET|POST|PUT|DELETE {{base_url}}/api/payments`

Requiere autenticación y acceso al módulo `payments`.

> La ruta API es `/payments` para alinearse con el módulo en base de datos. El recurso representa suscripciones (`suscriptions`).

## Listar membresías

```http
GET /api/payments
Authorization: Bearer {{token}}
```

### Query params

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `user_id` | integer | Filtra por miembro |
| `plan_id` | integer | Filtra por plan |
| `status_id` | integer | Filtra por estado (5=activa, 6=suspendida, 7=cancelada) |
| `search` | string | Busca por código |
| `per_page` | integer | Paginación (1-100) |
| `page` | integer | Página actual |

### Respuesta

```json
{
  "status": "success",
  "message": "Membresías obtenidas correctamente",
  "data": [
    {
      "id": 1,
      "code": "SUB-001",
      "startDate": "2026-06-01",
      "endDate": "2026-07-01",
      "price": 49.99,
      "user": {
        "id": 10,
        "firstName": "Juan",
        "lastName": "Pérez",
        "identification": "1234567890"
      },
      "plan": { "id": 1, "code": "PLAN-001", "name": "Plan Mensual" },
      "type": { "id": 3, "name": "Mensual" },
      "status": { "id": 5, "name": "Membresía activa" },
      "createdAt": "2026-06-17T00:00:00.000000Z",
      "updatedAt": "2026-06-17T00:00:00.000000Z"
    }
  ]
}
```

## Crear membresía

```http
POST /api/payments
Authorization: Bearer {{token}}
Content-Type: application/json
```

### Body

```json
{
  "code": "SUB-001",
  "startDate": "2026-06-01",
  "endDate": "2026-07-01",
  "price": 49.99,
  "userId": 10,
  "planId": 1,
  "suscriptionTypeId": 3,
  "statusId": 5
}
```

### Validaciones

- `userId` debe ser un usuario con rol Miembro
- `endDate` debe ser >= `startDate`
- `statusId` debe ser 5 (activa), 6 (suspendida) o 7 (cancelada)

## Ver membresía

```http
GET /api/payments/{id}
Authorization: Bearer {{token}}
```

Incluye pagos asociados si existen.

## Actualizar membresía

```http
PUT /api/payments/{id}
Authorization: Bearer {{token}}
Content-Type: application/json
```

## Eliminar membresía

```http
DELETE /api/payments/{id}
Authorization: Bearer {{token}}
```

## Lookup de tipos de membresía

```http
GET /api/lookups/suscription-types?per_page=25
```

No requiere autenticación.
