@extends('emails.layouts.prime-fitness')

@section('title', "Nuevo contacto — {$companyName}")

@section('content')
    <h2 style="margin: 0 0 24px; font-size: 20px; font-weight: 600; color: {{ config('mail-branding.text') }};">
        Nuevo mensaje de contacto
    </h2>

    <p style="margin: 0 0 24px; color: {{ config('mail-branding.text_muted') }};">
        Has recibido un nuevo mensaje desde la landing de <strong>{{ $companyName }}</strong>.
    </p>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border: 1px solid #E5E7EB; border-radius: 8px; overflow: hidden;">
        <tr>
            <td style="padding: 12px 16px; background-color: {{ config('mail-branding.background') }}; border-bottom: 1px solid #E5E7EB; width: 120px;">
                <strong style="color: {{ config('mail-branding.text') }};">Nombre</strong>
            </td>
            <td style="padding: 12px 16px; border-bottom: 1px solid #E5E7EB; color: {{ config('mail-branding.text') }};">
                {{ $senderName }}
            </td>
        </tr>
        <tr>
            <td style="padding: 12px 16px; background-color: {{ config('mail-branding.background') }}; border-bottom: 1px solid #E5E7EB;">
                <strong style="color: {{ config('mail-branding.text') }};">Correo</strong>
            </td>
            <td style="padding: 12px 16px; border-bottom: 1px solid #E5E7EB;">
                <a href="mailto:{{ $senderEmail }}" style="color: {{ config('mail-branding.primary') }}; text-decoration: none;">{{ $senderEmail }}</a>
            </td>
        </tr>
        @if ($senderPhone)
            <tr>
                <td style="padding: 12px 16px; background-color: {{ config('mail-branding.background') }}; border-bottom: 1px solid #E5E7EB;">
                    <strong style="color: {{ config('mail-branding.text') }};">Teléfono</strong>
                </td>
                <td style="padding: 12px 16px; border-bottom: 1px solid #E5E7EB; color: {{ config('mail-branding.text') }};">
                    {{ $senderPhone }}
                </td>
            </tr>
        @endif
        <tr>
            <td style="padding: 12px 16px; background-color: {{ config('mail-branding.background') }}; vertical-align: top;">
                <strong style="color: {{ config('mail-branding.text') }};">Mensaje</strong>
            </td>
            <td style="padding: 12px 16px; color: {{ config('mail-branding.text') }}; white-space: pre-wrap;">{{ $inquiryMessage }}</td>
        </tr>
    </table>
@endsection
