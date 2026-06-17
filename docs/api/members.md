# Miembros

Base: `GET|POST|PUT|DELETE {{base_url}}/api/members`

Requiere autenticación y acceso al módulo `members`.

Los miembros son usuarios con rol **Miembro** (`role_id: 4`). El rol se fuerza automáticamente en creación y actualización.

## Listar miembros

```http
GET /api/members
Authorization: Bearer {{token}}
```

### Query params

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `search` | string | Busca por nombre, apellido o identificación |
| `per_page` | integer | Paginación (1-100). Sin valor devuelve todos |
| `page` | integer | Página actual |

### Respuesta

```json
{
  "status": "success",
  "message": "Miembros obtenidos correctamente",
  "data": [
    {
      "id": 1,
      "initials": "JP",
      "firstName": "Juan",
      "lastName": "Pérez",
      "email": "juan@example.com",
      "phone": "3001234567",
      "birthDate": "1990-01-15",
      "age": 36,
      "sex": "Masculino",
      "identification": "1234567890",
      "photo": null,
      "height": 1.75,
      "identificationType": { "id": 1, "name": "Cédula", "abbreviation": "CC" },
      "role": { "id": 4, "name": "Miembro" },
      "status": { "id": 1, "name": "Activo", "color": "#00ff00" },
      "createdAt": "2026-06-17T00:00:00.000000Z",
      "updatedAt": "2026-06-17T00:00:00.000000Z"
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

## Crear miembro

```http
POST /api/members
Authorization: Bearer {{token}}
Content-Type: application/json
```

### Body

```json
{
  "firstName": "Juan",
  "lastName": "Pérez",
  "email": "juan@example.com",
  "phone": "3001234567",
  "birthdate": "1990-01-15",
  "sex": 1,
  "identification": "1234567890",
  "height": 1.75,
  "password": "secret123",
  "identificationTypeId": 1
}
```

> `roleId` no es necesario; se asigna automáticamente como Miembro.

## Ver miembro

```http
GET /api/members/{id}
Authorization: Bearer {{token}}
```

## Actualizar miembro

```http
PUT /api/members/{id}
Authorization: Bearer {{token}}
Content-Type: application/json
```

Mismos campos que creación. El rol se mantiene como Miembro.

## Eliminar miembro

```http
DELETE /api/members/{id}
Authorization: Bearer {{token}}
```

Soft delete. Respuesta sin `data`.
