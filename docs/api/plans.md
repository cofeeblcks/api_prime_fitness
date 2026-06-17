# Planes

Base: `GET|POST|PUT|DELETE {{base_url}}/api/plans`

Requiere autenticación y acceso al módulo `plans`.

## Listar planes

```http
GET /api/plans
Authorization: Bearer {{token}}
```

### Query params

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `search` | string | Busca por nombre o código |
| `is_active` | boolean | Filtra por estado activo |
| `per_page` | integer | Paginación (1-100) |
| `page` | integer | Página actual |

### Respuesta

```json
{
  "status": "success",
  "message": "Planes obtenidos correctamente",
  "data": [
    {
      "id": 1,
      "code": "PLAN-001",
      "name": "Plan Mensual",
      "description": "Acceso ilimitado por 30 días",
      "price": 49.99,
      "isActive": true,
      "details": [
        { "id": 1, "item": "Acceso ilimitado", "isActive": true }
      ],
      "createdAt": "2026-06-17T00:00:00.000000Z",
      "updatedAt": "2026-06-17T00:00:00.000000Z"
    }
  ]
}
```

## Crear plan

```http
POST /api/plans
Authorization: Bearer {{token}}
Content-Type: application/json
```

### Body

```json
{
  "code": "PLAN-001",
  "name": "Plan Mensual",
  "description": "Acceso ilimitado por 30 días",
  "price": 49.99,
  "isActive": true,
  "details": [
    { "item": "Acceso ilimitado", "isActive": true },
    { "item": "Clases grupales", "isActive": true }
  ]
}
```

## Ver plan

```http
GET /api/plans/{id}
Authorization: Bearer {{token}}
```

## Actualizar plan

```http
PUT /api/plans/{id}
Authorization: Bearer {{token}}
Content-Type: application/json
```

Para actualizar detalles existentes, incluye el `id` del detalle:

```json
{
  "code": "PLAN-001",
  "name": "Plan Mensual Plus",
  "description": "Acceso ilimitado actualizado",
  "price": 59.99,
  "isActive": true,
  "details": [
    { "id": 1, "item": "Acceso ilimitado", "isActive": true },
    { "item": "Nutrición básica", "isActive": true }
  ]
}
```

Los detalles no incluidos en el array serán eliminados.

## Eliminar plan

```http
DELETE /api/plans/{id}
Authorization: Bearer {{token}}
```

## Lookup de planes activos

```http
GET /api/lookups/plans?per_page=25
```

No requiere autenticación.
