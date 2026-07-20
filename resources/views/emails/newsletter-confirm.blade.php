<x-mail.layout title="Confirmez votre inscription à la newsletter">
    <h1 style="margin:0 0 12px;font-size:22px;color:#001C41;">Confirmez votre inscription</h1>

    <p style="margin:0 0 22px;font-size:15px;line-height:1.6;color:#14181d;">
        Vous souhaitez recevoir la newsletter de Radio Bastides. Dernière étape&nbsp;:
        cliquez sur le bouton ci-dessous pour confirmer votre adresse.
    </p>

    <div style="text-align:center;margin:26px 0;">
        <a href="{{ $url }}" style="display:inline-block;background:#1E6541;color:#ffffff;text-decoration:none;font-size:16px;font-weight:700;border-radius:12px;padding:14px 28px;">
            Confirmer mon inscription
        </a>
    </div>

    <p style="margin:0 0 6px;font-size:13px;line-height:1.6;color:#5E6975;">
        Si le bouton ne fonctionne pas, copiez-collez ce lien dans votre navigateur&nbsp;:
    </p>
    <p style="margin:0;font-size:13px;line-height:1.6;word-break:break-all;">
        <a href="{{ $url }}" style="color:#1E6541;">{{ $url }}</a>
    </p>

    <p style="margin:22px 0 0;font-size:13px;color:#5E6975;">
        Ce lien expire dans {{ $ttlHours }}&nbsp;heures. Si vous n'êtes pas à l'origine de cette demande, ignorez cet email.
    </p>
</x-mail.layout>
