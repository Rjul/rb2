@props(['title' => 'Radio Bastides'])
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
</head>
{{-- Emails : styles INLINE obligatoires (clients mail), tables pour la mise en page. --}}
<body style="margin:0;padding:0;background:#F5F9F9;font-family:'Segoe UI',Arial,sans-serif;color:#14181d;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F5F9F9;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #E4EAEA;">
                    <tr>
                        <td style="background:#001C41;padding:22px 28px;">
                            <a href="{{ url('/') }}" style="text-decoration:none;color:#ffffff;font-size:22px;font-weight:700;letter-spacing:0.3px;">Radio&nbsp;Bastides</a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px 28px;">
                            {{ $slot }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 28px;background:#F5F9F9;border-top:1px solid #E4EAEA;color:#5E6975;font-size:12px;line-height:1.6;">
                            Radio Bastides — la radio associative de Villeneuve-sur-Lot.<br>
                            Vous recevez cet email suite à une action sur radiobastides.fr. Si vous n'êtes pas à l'origine de cette demande, ignorez ce message.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
