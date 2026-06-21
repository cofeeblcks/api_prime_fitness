<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo contacto</title>
</head>
<body style="font-family: sans-serif; line-height: 1.6; color: #111827;">
    <h2>Nuevo mensaje de contacto — {{ $companyName }}</h2>

    <p><strong>Nombre:</strong> {{ $senderName }}</p>
    <p><strong>Correo:</strong> {{ $senderEmail }}</p>
    @if ($senderPhone)
        <p><strong>Teléfono:</strong> {{ $senderPhone }}</p>
    @endif

    <p><strong>Mensaje:</strong></p>
    <p style="white-space: pre-wrap;">{{ $inquiryMessage }}</p>
</body>
</html>
