<x-mail.layout title="Votre code de vérification">
    <h1 style="margin:0 0 12px;font-size:22px;color:#001C41;">Confirmez votre adresse email</h1>

    <p style="margin:0 0 20px;font-size:15px;line-height:1.6;color:#14181d;">
        @if ($name)Bonjour {{ $name }},<br>@endif
        Voici votre code pour activer votre compte&nbsp;:
    </p>

    <div style="text-align:center;margin:26px 0;">
        <span style="display:inline-block;font-size:34px;letter-spacing:10px;font-weight:700;color:#1E6541;background:#F5F9F9;border:2px dashed #6CB786;border-radius:12px;padding:16px 12px 16px 22px;">{{ $code }}</span>
    </div>

    <p style="margin:0;font-size:14px;line-height:1.6;color:#5E6975;">
        Ce code expire dans {{ $ttl }}&nbsp;minutes. Ne le communiquez à personne — Radio Bastides ne vous le demandera jamais.
    </p>
</x-mail.layout>
