# Empresa

Base: `GET|POST|PUT|DELETE {{base_url}}/api/companies`

La consulta de una empresa (`GET /api/companies/{id}`) es **pública** y no requiere autenticación. El resto de operaciones requiere autenticación y acceso al módulo `company`.

## Ver empresa (público)

```http
GET /api/companies/{id}
Accept: application/json
```

### Respuesta

```json
{
  "status": "success",
  "message": "Empresa obtenida correctamente",
  "data": {
    "id": 1,
    "name": "Prime Fitness",
    "slogan": "Tu gimnasio en línea",
    "logo": "http://localhost:8000/storage/logo.png",
    "address": "Calle 16 29-22, Molinos bajos, Floridablanca, Santander",
    "description": "Somos un gimnasio en línea...",
    "links": [
      {
        "id": 1,
        "username": "primefitness",
        "link": "https://www.facebook.com/primefitness",
        "linkType": { "id": 1, "name": "Facebook", "icon": "facebook" },
        "createdAt": "2026-06-20T00:00:00.000000Z",
        "updatedAt": "2026-06-20T00:00:00.000000Z"
      }
    ],
    "emails": [
      {
        "id": 1,
        "email": "info@primefitness.com",
        "createdAt": "2026-06-20T00:00:00.000000Z",
        "updatedAt": "2026-06-20T00:00:00.000000Z"
      }
    ],
    "phones": [
      {
        "id": 1,
        "phone": "3178546923",
        "createdAt": "2026-06-20T00:00:00.000000Z",
        "updatedAt": "2026-06-20T00:00:00.000000Z"
      }
    ],
    "services": [
      {
        "id": 1,
        "description": "Ofrecemos una amplia gama de servicios...",
        "createdAt": "2026-06-20T00:00:00.000000Z",
        "updatedAt": "2026-06-20T00:00:00.000000Z"
      }
    ],
    "coordinates": {
      "id": 1,
      "latitude": 7.0622088,
      "longitude": -73.0973668
    },
    "createdAt": "2026-06-20T00:00:00.000000Z",
    "updatedAt": "2026-06-20T00:00:00.000000Z"
  }
}
```

## Listar empresas

```http
GET /api/companies
Authorization: Bearer {{token}}
```

## Crear empresa

```http
POST /api/companies
Authorization: Bearer {{token}}
Content-Type: multipart/form-data
```

### Body

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `name` | string | Sí | Nombre de la empresa |
| `slogan` | string | No | Eslogan |
| `logo` | file | Sí | Imagen del logo |
| `address` | string | Sí | Dirección |
| `description` | string | Sí | Descripción general |
| `links` | array | No | Enlaces de redes sociales |
| `links.*.username` | string | Sí* | Usuario o identificador del enlace |
| `links.*.link` | string | Sí* | URL del enlace |
| `links.*.linkTypeId` | integer | Sí* | ID del tipo de enlace (`GET /api/lookups/link-types`) |
| `emails` | array | No | Correos de contacto |
| `emails.*.email` | string | Sí* | Correo electrónico |
| `phones` | array | No | Teléfonos de contacto |
| `phones.*.phone` | string | Sí* | Número telefónico |
| `services` | array | No | Servicios ofrecidos |
| `services.*.description` | string | Sí* | Descripción del servicio |
| `coordinates` | object | No | Ubicación geográfica de la empresa |
| `coordinates.latitude` | number | Sí** | Latitud (-90 a 90) |
| `coordinates.longitude` | number | Sí** | Longitud (-180 a 180) |

\* Requerido cuando se envía el arreglo correspondiente.
\*\* Requerido cuando se envía `coordinates` con valores; enviar vacío elimina la ubicación en actualización.

### Ejemplo (JSON con arrays anidados vía form-data)

Para `POST` y `PUT` con logo, usa `multipart/form-data`. Los arreglos anidados se envían con notación de corchetes:

```
name=Prime Fitness
slogan=Tu gimnasio en línea
logo=<archivo>
address=Calle 16 29-22...
description=Somos un gimnasio...
links[0][username]=primefitness
links[0][link]=https://www.facebook.com/primefitness
links[0][linkTypeId]=1
emails[0][email]=info@primefitness.com
phones[0][phone]=3178546923
services[0][description]=Planes personalizados
services[1][description]=Asesoría nutricional
coordinates[latitude]=7.0622088
coordinates[longitude]=-73.0973668
```

## Actualizar empresa

```http
PUT /api/companies/{id}
Authorization: Bearer {{token}}
Content-Type: multipart/form-data
```

Mismos campos que creación. Todos son opcionales en actualización, excepto los elementos dentro de cada arreglo cuando se envía el arreglo.

### Sincronización de relaciones

Al enviar `links`, `emails`, `phones`, `services` o `coordinates`, la API sincroniza los registros:

- Elementos con `id` existente se actualizan.
- Elementos sin `id` se crean.
- Registros existentes no incluidos en el arreglo se eliminan (soft delete).

### Ejemplo de actualización con relaciones

```
name=Prime Fitness
links[0][id]=1
links[0][username]=primefitness
links[0][link]=https://www.facebook.com/primefitness
links[0][linkTypeId]=1
links[1][username]=primefitness.co
links[1][link]=https://www.instagram.com/primefitness
links[1][linkTypeId]=2
emails[0][id]=1
emails[0][email]=contacto@primefitness.com
phones[0][id]=1
phones[0][phone]=3178546923
services[0][id]=1
services[0][description]=Entrenamiento personalizado
services[1][description]=Nuevo servicio de yoga
```

## Eliminar empresa

```http
DELETE /api/companies/{id}
Authorization: Bearer {{token}}
```

Soft delete. Respuesta sin `data`.
