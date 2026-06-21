# Contacto (landing)

Endpoint público para el formulario de contacto de la landing page.

## Enviar mensaje

```http
POST /api/contact
Content-Type: application/json
```

### Body

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `name` | string | Sí | Nombre del remitente |
| `email` | string | Sí | Correo del remitente |
| `phone` | string | No | Teléfono de contacto |
| `message` | string | Sí | Mensaje (máx. 2000 caracteres) |
| `companyId` | integer | No | ID de la empresa destino (default: `1`) |

### Ejemplo

```json
{
  "name": "Juan Pérez",
  "email": "juan@example.com",
  "phone": "3001234567",
  "message": "Quiero información sobre el plan mensual",
  "companyId": 1
}
```

### Respuesta exitosa

```json
{
  "status": "success",
  "message": "Tu mensaje ha sido enviado correctamente. Te contactaremos pronto."
}
```

### Errores comunes

- **422** — Validación fallida o empresa sin correos configurados
- **429** — Rate limit excedido (máx. 5 solicitudes por minuto)

### Notas

- El correo se envía al primer email registrado en la empresa (`company_emails`).
- El mensaje queda registrado en `contacts` con estado **Solicitud** (`status_id: 10`).
- En desarrollo con `MAIL_MAILER=log`, el contenido aparece en `storage/logs/laravel.log`.
- Gestión y seguimiento: ver [contacts.md](contacts.md).
