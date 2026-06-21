@extends('emails.layouts.prime-fitness')

@section('title', 'Código de recuperación de contraseña')

@section('content')
    <p style="margin: 0 0 16px;">Hola,</p>

    <p style="margin: 0 0 24px;">Recibimos una solicitud para restablecer la contraseña de tu cuenta. Usa el siguiente código de verificación:</p>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td align="center" style="padding: 24px; background-color: rgba(98, 31, 191, 0.08); border: 2px solid {{ config('mail-branding.primary') }}; border-radius: 8px;">
                <p style="margin: 0; font-size: 36px; font-weight: 700; letter-spacing: 8px; font-family: 'Courier New', Courier, monospace; color: {{ config('mail-branding.primary') }};">{{ $otp }}</p>
            </td>
        </tr>
    </table>

    <p style="margin: 24px 0 16px; color: {{ config('mail-branding.text_muted') }}; font-size: 14px;">
        Este código expira en <strong>{{ $expirationMinutes }} minutos</strong>.
    </p>

    <p style="margin: 0; color: {{ config('mail-branding.text_muted') }}; font-size: 14px;">
        Si no solicitaste restablecer tu contraseña, puedes ignorar este correo. Tu cuenta permanecerá segura.
    </p>
@endsection
