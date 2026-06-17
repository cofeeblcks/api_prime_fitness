# Plan de implementación — Consumo de API Prime Fitness

Documento único de referencia para integrar la API en el **frontend web (React.js)** y la **app móvil (Flutter)**.

---

## 1. Objetivo

Definir cómo consumir la API REST de Prime Fitness desde ambos clientes, incluyendo:

- Configuración de entorno y autenticación
- Arquitectura recomendada por plataforma
- Endpoints con request/response esperados
- Orden de implementación sugerido

---

## 2. Configuración base

| Concepto | Valor |
|----------|-------|
| URL base | `{BASE_URL}/api` |
| Ejemplo local | `http://localhost:8000/api` |
| Formato | JSON (`Content-Type: application/json`) |
| Autenticación | Bearer Token (Laravel Sanctum) |
| Header protegido | `Authorization: Bearer {access_token}` |
| Convención de campos | **camelCase** en request y response |

### Variables de entorno sugeridas

**React (.env)**

```env
VITE_API_BASE_URL=http://localhost:8000/api
```

**Flutter (dart-define o .env)**

```env
API_BASE_URL=http://localhost:8000/api
```

---

## 3. Convención de respuestas

Todas las respuestas siguen el mismo envelope.

### Éxito

```json
{
  "status": "success",
  "message": "Mensaje descriptivo",
  "data": {},
  "meta": {}
}
```

> `message` y `meta` se omiten si están vacíos.

### Error

```json
{
  "status": "error",
  "message": "Descripción del error",
  "errors": {
    "campo": ["Mensaje de validación"]
  }
}
```

### Códigos HTTP

| Código | Situación |
|--------|-----------|
| 200 | Operación exitosa |
| 401 | No autenticado, credenciales inválidas o sin permiso de módulo |
| 403 | Acceso prohibido |
| 404 | Recurso no encontrado |
| 422 | Error de validación |
| 500 | Error interno |

### Paginación

Cuando aplica, `meta.pagination`:

```json
{
  "pagination": {
    "total": 100,
    "count": 25,
    "per_page": 25,
    "current_page": 1,
    "total_pages": 4
  }
}
```

Parámetros: `page` (≥1), `per_page` (1–100). En listados de usuarios/miembros/entrenadores, si **no** se envía `per_page`, la API devuelve **todos** los registros sin paginar.

---

## 4. Arquitectura recomendada

### 4.1 React.js

```
src/
├── api/
│   ├── client.ts          # Axios/fetch + interceptores
│   ├── types/             # Interfaces TypeScript
│   └── services/          # authService, memberService, etc.
├── hooks/                 # useAuth, useMembers, usePlans...
├── context/               # AuthContext (token + usuario)
└── utils/
    └── apiError.ts        # Normalizar errores 401/422
```

**Cliente HTTP (Axios recomendado)**

```typescript
// client.ts
import axios from 'axios';

const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL,
  headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
});

api.interceptors.request.use((config) => {
  const token = localStorage.getItem('access_token');
  if (token) config.headers.Authorization = `Bearer ${token}`;
  return config;
});

api.interceptors.response.use(
  (res) => res,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('access_token');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

export default api;
```

**Patrón de servicio**

```typescript
// services/memberService.ts
import api from '../client';
import type { ApiResponse, User } from '../types';

export const memberService = {
  list: (params?: { search?: string; page?: number; per_page?: number }) =>
    api.get<ApiResponse<User[]>>('/members', { params }),

  create: (data: CreateMemberPayload) =>
    api.post<ApiResponse<User>>('/members', data),

  get: (id: number) => api.get<ApiResponse<User>>(`/members/${id}`),

  update: (id: number, data: Partial<CreateMemberPayload>) =>
    api.put<ApiResponse<User>>(`/members/${id}`, data),

  remove: (id: number) => api.delete<ApiResponse<[]>>(`/members/${id}`),
};
```

### 4.2 Flutter

```
lib/
├── core/
│   ├── api/
│   │   ├── api_client.dart       # Dio + interceptores
│   │   ├── api_response.dart     # Modelo genérico envelope
│   │   └── api_exception.dart
│   └── storage/
│       └── secure_storage.dart   # flutter_secure_storage para token
├── features/
│   ├── auth/
│   │   ├── data/auth_repository.dart
│   │   └── presentation/
│   ├── members/
│   └── ...
└── main.dart
```

**Cliente HTTP (Dio recomendado)**

```dart
// api_client.dart
import 'package:dio/dio.dart';

class ApiClient {
  final Dio _dio;

  ApiClient({required String baseUrl, TokenStorage? storage})
      : _dio = Dio(BaseOptions(
          baseUrl: baseUrl,
          headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
        )) {
    _dio.interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) async {
        final token = await storage?.readToken();
        if (token != null) options.headers['Authorization'] = 'Bearer $token';
        handler.next(options);
      },
      onError: (error, handler) {
        if (error.response?.statusCode == 401) {
          storage?.clearToken();
        }
        handler.next(error);
      },
    ));
  }

  Dio get dio => _dio;
}
```

**Modelo de respuesta genérico**

```dart
class ApiResponse<T> {
  final String status;
  final String? message;
  final T? data;
  final Map<String, dynamic>? meta;

  ApiResponse({required this.status, this.message, this.data, this.meta});

  bool get isSuccess => status == 'success';
}
```

---

## 5. Control de acceso por módulo

Tras el login, el objeto `user.role.modules` indica qué secciones puede ver el usuario. El middleware `module.access` bloquea rutas sin permiso (401).

| Módulo (route) | Endpoint API | Uso típico |
|----------------|--------------|------------|
| `users` | `/users` | Administración de usuarios |
| `members` | `/members` | Gestión de miembros |
| `trainers` | `/trainers` | Gestión de entrenadores |
| `plans` | `/plans` | Catálogo de planes |
| `payments` | `/payments` | Membresías / suscripciones |
| `access-control` | `/access-control` | Registro de ingreso al gym |

**React:** ocultar rutas del menú según `user.role.modules[].route`.  
**Flutter:** filtrar ítems del `BottomNavigationBar` / `Drawer` con la misma lógica.

### Roles del sistema

| ID | Nombre |
|----|--------|
| 1 | Administrador |
| 2 | Recepcionista |
| 3 | Entrenador |
| 4 | Miembro |

---

## 6. Autenticación

### 6.1 Login

```http
POST /api/auth/login
Content-Type: application/json
```

**Request**

```json
{
  "email": "admin@example.com",
  "password": "password"
}
```

**Response 200**

```json
{
  "status": "success",
  "data": {
    "access_token": "1|xxxxxxxx",
    "token_type": "Bearer",
    "user": {
      "id": 1,
      "initials": "AD",
      "fullName": "Admin Usuario",
      "email": "admin@example.com",
      "role": {
        "id": 1,
        "name": "Administrador",
        "modules": [
          { "id": 1, "name": "Usuarios", "icon": "users", "route": "users" },
          { "id": 2, "name": "Miembros", "icon": "members", "route": "members" }
        ]
      }
    }
  }
}
```

**Response 401**

```json
{
  "status": "error",
  "message": "Credenciales no validas."
}
```

**Implementación**

| Paso | React | Flutter |
|------|-------|---------|
| 1 | Pantalla login con email/password | Igual |
| 2 | Guardar `access_token` en localStorage | Guardar en `flutter_secure_storage` |
| 3 | Guardar `user` en AuthContext | Guardar en provider/Bloc |
| 4 | Redirigir al dashboard | Navegar a `HomeScreen` |
| 5 | Construir menú desde `role.modules` | Igual |

### 6.2 Recuperación de contraseña

#### Solicitar OTP

```http
POST /api/auth/password/request-reset
```

**Request**

```json
{ "email": "usuario@example.com" }
```

**Response 200**

```json
{
  "status": "success",
  "message": "Se ha enviado un código de recuperación a tu correo electrónico."
}
```

#### Restablecer contraseña

```http
POST /api/auth/password/reset
```

**Request**

```json
{
  "email": "usuario@example.com",
  "otp": "123456",
  "password": "nuevaClave123"
}
```

**Response 200**

```json
{
  "status": "success",
  "message": "La contraseña ha sido actualizada correctamente."
}
```

---

## 7. Lookups (sin autenticación)

Usar al cargar formularios (selects, autocompletado).

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/lookups/identification-types` | Tipos de documento |
| GET | `/lookups/roles` | Roles (incluye `modules`) |
| GET | `/lookups/plans` | Solo planes activos |
| GET | `/lookups/suscription-types` | Tipos de membresía |

**Query params:** `page`, `per_page` (default 25, máx. 100)

### GET /lookups/identification-types

**Response 200**

```json
{
  "status": "success",
  "message": "Tipos de identificación obtenidos correctamente",
  "data": [
    { "id": 1, "name": "Cédula de ciudadanía", "abbreviation": "CC" }
  ],
  "meta": { "pagination": { "total": 5, "count": 5, "per_page": 25, "current_page": 1, "total_pages": 1 } }
}
```

### GET /lookups/roles

**Response 200** — cada rol incluye módulos activos:

```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "name": "Administrador",
      "modules": [
        { "id": 1, "name": "Usuarios", "icon": "users", "route": "users" }
      ]
    }
  ]
}
```

### GET /lookups/plans

**Response 200**

```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "code": "PLAN-001",
      "name": "Plan Mensual",
      "description": "Acceso ilimitado por 30 días",
      "price": 49.99,
      "isActive": true,
      "createdAt": "2026-06-17T00:00:00.000000Z",
      "updatedAt": "2026-06-17T00:00:00.000000Z"
    }
  ]
}
```

### GET /lookups/suscription-types

**Response 200**

```json
{
  "status": "success",
  "data": [
    { "id": 1, "name": "Diaria" },
    { "id": 3, "name": "Mensual" }
  ]
}
```

---

## 8. Usuarios

> Requiere: `Authorization` + módulo `users`

Base: `/users`

### GET /users — Listar

**Query params**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `search` | string | Nombre, apellido o identificación |
| `roles` | int[] | Filtrar por IDs de rol (`roles[]=1&roles[]=2`) |
| `page` | int | Página |
| `per_page` | int | Items por página (sin valor = todos) |

**Response 200**

```json
{
  "status": "success",
  "message": "Usuarios obtenidos correctamente",
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
      "role": { "id": 2, "name": "Recepcionista" },
      "status": { "id": 1, "name": "Activo", "color": "#00ff00" },
      "createdAt": "2026-06-17T00:00:00.000000Z",
      "updatedAt": "2026-06-17T00:00:00.000000Z"
    }
  ]
}
```

### POST /users — Crear

**Request**

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
  "roleId": 2,
  "identificationTypeId": 1
}
```

| Campo | Tipo | Requerido | Notas |
|-------|------|-----------|-------|
| `sex` | int | Sí | `1` = Masculino, `2` = Femenino |
| `photo` | file | No | `multipart/form-data` si se envía imagen |
| `password` | string | Sí (crear) | Mín. 6 caracteres |

**Response 200** — objeto `User` en `data`.

### GET /users/{id} — Ver

**Response 200** — objeto `User` en `data`.

### PUT /users/{id} — Actualizar

Mismos campos que crear. `password` opcional. `email` debe existir en BD si se envía.

### DELETE /users/{id} — Eliminar

**Response 200**

```json
{
  "status": "success",
  "message": "Usuario eliminado correctamente"
}
```

---

## 9. Miembros

> Requiere: `Authorization` + módulo `members`  
> El rol Miembro (`roleId: 4`) se asigna automáticamente; no enviar `roleId`.

Base: `/members`

Misma estructura de **User** que usuarios. Endpoints: `GET`, `POST`, `GET/{id}`, `PUT/{id}`, `DELETE/{id}`.

### POST /members — Crear

**Request**

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

### GET /members — Listar

**Query params:** `search`, `page`, `per_page`

**Response 200** — array de `User` (ver sección 8).

---

## 10. Entrenadores

> Requiere: `Authorization` + módulo `trainers`  
> El rol Entrenador (`roleId: 3`) se asigna automáticamente.

Base: `/trainers`

Idéntico a miembros en estructura y endpoints.

### POST /trainers — Crear

**Request**

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

---

## 11. Planes

> Requiere: `Authorization` + módulo `plans`

Base: `/plans`

### GET /plans — Listar

**Query params:** `search`, `is_active` (boolean), `page`, `per_page`

**Response 200**

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
        { "id": 1, "item": "Acceso ilimitado", "isActive": true, "createdAt": "...", "updatedAt": "..." }
      ],
      "createdAt": "2026-06-17T00:00:00.000000Z",
      "updatedAt": "2026-06-17T00:00:00.000000Z"
    }
  ]
}
```

### POST /plans — Crear

**Request**

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

### PUT /plans/{id} — Actualizar

Incluir `id` en detalles existentes. Los no enviados se eliminan.

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

### DELETE /plans/{id}

Soft delete. Response sin `data`.

---

## 12. Membresías (Suscripciones)

> Requiere: `Authorization` + módulo `payments`  
> La ruta es `/payments` aunque el recurso sea una suscripción.

Base: `/payments`

### GET /payments — Listar

**Query params**

| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| `user_id` | int | Filtrar por miembro |
| `plan_id` | int | Filtrar por plan |
| `status_id` | int | 5=activa, 6=suspendida, 7=cancelada |
| `search` | string | Buscar por código |
| `page`, `per_page` | int | Paginación |

**Response 200**

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
        "identification": "1234567890",
        "email": "juan@example.com",
        "phone": "3001234567"
      },
      "plan": {
        "id": 1,
        "code": "PLAN-001",
        "name": "Plan Mensual",
        "description": "...",
        "price": 49.99,
        "isActive": true
      },
      "type": { "id": 3, "name": "Mensual" },
      "status": { "id": 5, "name": "Membresía activa", "color": "#00ff00" },
      "createdAt": "2026-06-17T00:00:00.000000Z",
      "updatedAt": "2026-06-17T00:00:00.000000Z"
    }
  ]
}
```

### POST /payments — Crear

**Request**

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

| Validación | Regla |
|------------|-------|
| `userId` | Debe ser usuario con rol Miembro (4) |
| `endDate` | ≥ `startDate` |
| `statusId` | 5, 6 o 7 |

### GET /payments/{id} — Ver detalle

Incluye `payments` (pagos asociados) si existen:

```json
{
  "status": "success",
  "data": {
    "id": 1,
    "code": "SUB-001",
    "startDate": "2026-06-01",
    "endDate": "2026-07-01",
    "price": 49.99,
    "user": { "id": 10, "firstName": "Juan", "lastName": "Pérez", "identification": "1234567890" },
    "plan": { "id": 1, "code": "PLAN-001", "name": "Plan Mensual" },
    "type": { "id": 3, "name": "Mensual" },
    "status": { "id": 5, "name": "Membresía activa" },
    "payments": [
      {
        "id": 1,
        "code": "PAY-001",
        "startDate": "2026-06-01",
        "endDate": "2026-07-01",
        "paymentDate": "2026-06-01",
        "status": { "id": 9, "name": "Pagado" },
        "createdAt": "...",
        "updatedAt": "..."
      }
    ]
  }
}
```

### PUT /payments/{id} — Actualizar

Mismos campos que crear.

### DELETE /payments/{id}

Soft delete.

### Estados de membresía

| ID | Nombre |
|----|--------|
| 5 | Membresía activa |
| 6 | Membresía suspendida |
| 7 | Membresía cancelada |

---

## 13. Control de acceso

> Requiere: `Authorization` + módulo `access-control`  
> Solo `index` y `store` (no hay update/delete).

Base: `/access-control`

### GET /access-control — Historial

**Query params:** `status_id` (3=permitido, 4=denegado), `search`, `page`, `per_page`

**Response 200**

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

### POST /access-control — Registrar ingreso

Valida membresía activa del miembro para la fecha indicada.

**Request**

```json
{
  "identification": "1234567890",
  "date": "2026-06-17"
}
```

| Campo | Requerido | Descripción |
|-------|-----------|-------------|
| `identification` | Sí | Documento del miembro |
| `date` | No | Fecha a validar (default: hoy) |

**Criterios de acceso permitido**

1. Usuario con rol Miembro y esa identificación
2. Usuario activo (`status_id: 1`)
3. Membresía activa (`status_id: 5`)
4. Fecha dentro de `startDate`–`endDate`

**Response 200 — Acceso permitido**

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

**Response 200 — Acceso denegado** (miembro existe pero sin membresía válida)

```json
{
  "status": "success",
  "message": "Acceso creado correctamente",
  "data": {
    "access": {
      "id": 2,
      "status": { "id": 4, "name": "Acceso denegado" }
    },
    "member": { "id": 10, "firstName": "Juan", "lastName": "Pérez", "identification": "1234567890" },
    "subscription": null
  }
}
```

**Response 404 — Miembro no encontrado**

```json
{
  "status": "error",
  "message": "No se encontró un miembro con la identificación proporcionada."
}
```

> **UI móvil (Flutter):** pantalla de escaneo/ingreso de documento ideal para este endpoint.  
> **UI web (React):** pantalla de recepción con input de identificación y feedback visual (verde/rojo).

---

## 14. Tipos de referencia

### TypeScript (React)

```typescript
export interface ApiResponse<T> {
  status: 'success' | 'error';
  message?: string;
  data?: T;
  meta?: { pagination?: PaginationMeta };
  errors?: Record<string, string[]>;
}

export interface PaginationMeta {
  total: number;
  count: number;
  per_page: number;
  current_page: number;
  total_pages: number;
}

export interface User {
  id: number;
  initials: string;
  firstName: string;
  lastName: string;
  email: string;
  phone: string;
  birthDate: string;
  age: number;
  sex: 'Masculino' | 'Femenino';
  identification: string;
  photo: string | null;
  height: number | null;
  identificationType: IdentificationType;
  role: Role;
  status: Status;
  createdAt: string;
  updatedAt: string;
}

export interface Plan {
  id: number;
  code: string;
  name: string;
  description: string;
  price: number;
  isActive: boolean;
  details?: PlanDetail[];
  createdAt: string;
  updatedAt: string;
}

export interface Subscription {
  id: number;
  code: string;
  startDate: string;
  endDate: string;
  price: number;
  user: UserSummary;
  plan: Plan;
  type: { id: number; name: string };
  status: Status;
  payments?: Payment[];
}
```

### Dart (Flutter)

```dart
class User {
  final int id;
  final String firstName;
  final String lastName;
  final String email;
  final String identification;
  // fromJson / toJson para cada modelo...

  factory User.fromJson(Map<String, dynamic> json) => User(
    id: json['id'],
    firstName: json['firstName'],
    lastName: json['lastName'],
    email: json['email'],
    identification: json['identification'],
  );
}
```

> Recomendación: usar `json_serializable` o `freezed` en Flutter y generar modelos desde los ejemplos de este documento.

---

## 15. Plan de implementación por fases

### Fase 1 — Infraestructura (ambas plataformas)

| # | Tarea | React | Flutter |
|---|-------|-------|---------|
| 1 | Configurar cliente HTTP + interceptores | Axios | Dio |
| 2 | Modelo `ApiResponse<T>` genérico | TS interface | Dart class |
| 3 | Manejo de errores 401/422 | Interceptor + toast | Interceptor + SnackBar |
| 4 | Variables de entorno | `VITE_API_BASE_URL` | `--dart-define` |

### Fase 2 — Autenticación

| # | Tarea | Endpoints |
|---|-------|-----------|
| 1 | Pantalla login | `POST /auth/login` |
| 2 | Persistencia de token | — |
| 3 | Menú dinámico por módulos | Datos de `user.role.modules` |
| 4 | Recuperación de contraseña | `POST /auth/password/*` |

### Fase 3 — Lookups y formularios

| # | Tarea | Endpoints |
|---|-------|-----------|
| 1 | Cargar tipos de identificación | `GET /lookups/identification-types` |
| 2 | Cargar roles (solo admin) | `GET /lookups/roles` |
| 3 | Selectores de planes y tipos membresía | `GET /lookups/plans`, `GET /lookups/suscription-types` |

### Fase 4 — CRUD principal (según rol)

| Módulo | Pantallas | Endpoints |
|--------|-----------|-----------|
| Miembros | Lista, crear, editar, detalle | `/members` |
| Entrenadores | Lista, crear, editar | `/trainers` |
| Planes | Lista, crear, editar con detalles | `/plans` |
| Membresías | Lista, crear, editar, detalle con pagos | `/payments` |
| Usuarios | Solo admin | `/users` |

### Fase 5 — Control de acceso

| Plataforma | Pantalla | Endpoint |
|------------|----------|----------|
| React (recepción) | Input documento + historial | `POST /access-control`, `GET /access-control` |
| Flutter (portería) | Escáner / teclado numérico | `POST /access-control` |

### Fase 6 — Pulido

- Paginación y búsqueda en tablas
- Loading states y empty states
- Reintento en errores de red
- Cache de lookups (React Query / Flutter cache)
- Subida de foto con `multipart/form-data` en usuarios

---

## 16. Checklist de integración

### Por endpoint

- [ ] Request body usa **camelCase**
- [ ] Header `Authorization` en rutas protegidas
- [ ] Manejo de `status: "error"` y `errors` por campo (422)
- [ ] Paginación lee `meta.pagination`
- [ ] Fechas en formato `YYYY-MM-DD`
- [ ] `sex` se envía como entero (`1`/`2`), se recibe como string (`"Masculino"`/`"Femenino"`)

### Por plataforma

**React**

- [ ] React Query o SWR para cache de listados
- [ ] Rutas protegidas con guard de autenticación
- [ ] Formularios con validación client-side alineada a la API

**Flutter**

- [ ] `flutter_secure_storage` para token
- [ ] Repository pattern por feature
- [ ] State management (Bloc/Riverpod/Provider) por módulo

---

## 17. Recursos adicionales

| Recurso | Ubicación |
|---------|-----------|
| Colección Postman | `docs/postman/prime-fitness-api.postman_collection.json` |
| Documentación por módulo | `docs/api/*.md` |
| README general API | `docs/README.md` |

---

*Última actualización: junio 2026 — API Laravel 13 + Sanctum*
