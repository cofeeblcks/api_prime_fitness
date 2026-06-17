# Entrenadores

Base: `GET|POST|PUT|DELETE {{base_url}}/api/trainers`

Requiere autenticación y acceso al módulo `trainers`.

Los entrenadores son usuarios con rol **Entrenador** (`role_id: 3`). El rol se fuerza automáticamente en creación y actualización.

## Listar entrenadores

```http
GET /api/trainers
Authorization: Bearer {{token}}
```

### Query params

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `search` | string | Busca por nombre, apellido o identificación |
| `per_page` | integer | Paginación (1-100) |
| `page` | integer | Página actual |

## Crear entrenador

```http
POST /api/trainers
Authorization: Bearer {{token}}
Content-Type: application/json
```

### Body

```json
{
  "firstName": "Carlos",
  "lastName": "Gómez",
  "email": "carlos@example.com",
  "phone": "3009876543",
  "birthdate": "1985-06-20",
  "sex": 1,
  "identification": "9876543210",
  "height": 1.80,
  "password": "secret123",
  "identificationTypeId": 1
}
```

> `roleId` no es necesario; se asigna automáticamente como Entrenador.

## Ver entrenador

```http
GET /api/trainers/{id}
Authorization: Bearer {{token}}
```

## Actualizar entrenador

```http
PUT /api/trainers/{id}
Authorization: Bearer {{token}}
Content-Type: application/json
```

## Eliminar entrenador

```http
DELETE /api/trainers/{id}
Authorization: Bearer {{token}}
```
