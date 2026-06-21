@php
    $brand = config('mail-branding');
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', $brand['company_name'])</title>
</head>
<body style="margin: 0; padding: 0; background-color: {{ $brand['background'] }}; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: {{ $brand['background'] }};">
        <tr>
            <td align="center" style="padding: 32px 16px;">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="max-width: 600px; width: 100%; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">

                    {{-- Header --}}
                    <tr>
                        <td align="center" style="background-color: {{ $brand['primary'] }}; padding: 32px 24px;">
                            @if (isset($message) && file_exists($brand['logo_path']))
                                <img src="{{ $message->embed($brand['logo_path']) }}" alt="{{ $brand['company_name'] }}" width="280" style="display: block; max-width: 280px; width: 100%; height: auto; margin: 0 auto;">
                            @else
                                <p style="margin: 0; color: #ffffff; font-size: 24px; font-weight: 700; letter-spacing: 1px;">{{ $brand['company_name'] }}</p>
                            @endif
                        </td>
                    </tr>

                    {{-- Content --}}
                    <tr>
                        <td style="padding: 32px 24px; color: {{ $brand['text'] }}; font-size: 16px; line-height: 1.6;">
                            @yield('content')
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td align="center" style="padding: 24px; background-color: {{ $brand['background'] }}; border-top: 1px solid #E5E7EB;">
                            <p style="margin: 0 0 8px; color: {{ $brand['text_muted'] }}; font-size: 14px;">{{ $brand['slogan'] }}</p>
                            <p style="margin: 0; color: {{ $brand['text_muted'] }}; font-size: 12px;">&copy; {{ date('Y') }} {{ $brand['company_name'] }}. Todos los derechos reservados.</p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
