@php
    $badges = ['audio' => 'Audio', 'video' => 'Vidéo', 'text' => 'Article'];
    $ctas   = ['audio' => 'Écouter', 'video' => 'Voir', 'text' => 'Lire'];
    $hero   = $emissions->first();
    $rest   = $emissions->slice(1);
    $heroImg = $hero?->image ?: 'https://picsum.photos/seed/rb-nl-hero/1120/560';
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light only">
    <title>Radio Bastides — la sélection de la semaine</title>
</head>
<body style="margin:0;padding:0;background:#F5F9F9;-webkit-text-size-adjust:100%;font-family:'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#14181d;">
    {{-- Texte d'aperçu (caché) affiché par les clients mail dans la liste des messages --}}
    <div style="display:none;max-height:0;overflow:hidden;opacity:0;mso-hide:all;">
        La sélection « à la une » de la semaine sur Radio Bastides — émissions, chroniques et musiques d'ici.
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F5F9F9;padding:24px 12px;">
        <tr><td align="center">
            <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

                {{-- ============ EN-TÊTE (comme le site : fond blanc + logo couleur + fine bordure) ============ --}}
                <tr><td style="background:#ffffff;border-radius:18px 18px 0 0;padding:24px 30px;border-bottom:1px solid #E4EAEA;" align="center">
                    <a href="{{ route('v2.home') }}" style="text-decoration:none;">
                        <img src="{{ url('/imgs/logo.png') }}" width="200" height="41" alt="Radio Bastides"
                             style="display:inline-block;border:0;width:200px;height:41px;">
                    </a>
                </td></tr>

                {{-- ============ TITRE (style « titre de section » du site : sur-titre vert + titre navy) ============ --}}
                <tr><td style="background:#ffffff;padding:28px 30px 18px;">
                    <div style="color:#1E6541;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;">À la une cette semaine</div>
                    <div style="color:#001C41;font-size:27px;font-weight:700;line-height:1.2;margin-top:7px;">La sélection de la semaine</div>
                    <div style="color:#5E6975;font-size:15px;line-height:1.6;margin-top:9px;">Les rendez-vous à ne pas manquer, à écouter où et quand vous voulez.</div>
                </td></tr>

                {{-- ============ ÉMISSION VEDETTE ============ --}}
                @if ($hero)
                <tr><td style="background:#ffffff;padding:0;">
                    <a href="{{ $hero->canonicalUrl() }}" style="text-decoration:none;display:block;">
                        <img src="{{ $heroImg }}" width="600" alt="{{ $hero->name }}"
                             style="display:block;width:100%;max-width:600px;height:auto;border:0;">
                    </a>
                </td></tr>
                <tr><td style="background:#ffffff;padding:24px 30px 8px;">
                    @if ($hero->programme)
                        <div style="color:#1E6541;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:1px;">{{ $hero->programme->name }}</div>
                    @endif
                    <div style="font-size:26px;font-weight:700;line-height:1.2;color:#001C41;margin-top:6px;">
                        <a href="{{ $hero->canonicalUrl() }}" style="color:#001C41;text-decoration:none;">{{ $hero->name }}</a>
                    </div>
                    @if ($hero->description)
                        <div style="font-size:15px;line-height:1.6;color:#5E6975;margin-top:10px;">{{ \Illuminate\Support\Str::limit(strip_tags($hero->description), 180) }}</div>
                    @endif
                    <div style="margin-top:18px;">
                        <a href="{{ $hero->canonicalUrl() }}" style="display:inline-block;background:#1E6541;color:#ffffff;text-decoration:none;font-size:15px;font-weight:700;border-radius:10px;padding:12px 24px;">
                            {{ $ctas[$hero->media_type] ?? 'Découvrir' }} l'émission
                        </a>
                    </div>
                </td></tr>
                <tr><td style="background:#ffffff;padding:8px 30px 4px;"><div style="border-top:1px solid #E4EAEA;height:1px;line-height:1px;">&nbsp;</div></td></tr>
                @endif

                {{-- ============ AUTRES ÉMISSIONS ============ --}}
                @foreach ($rest as $e)
                    @php $img = $e->image ?: 'https://picsum.photos/seed/rb-nl-' . $e->id . '/320/320'; @endphp
                    <tr><td style="background:#ffffff;padding:16px 30px;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"><tr>
                            <td width="132" valign="top" style="width:132px;padding-right:16px;">
                                <a href="{{ $e->canonicalUrl() }}" style="text-decoration:none;">
                                    <img src="{{ $img }}" width="132" height="99" alt="{{ $e->name }}"
                                         style="display:block;width:132px;height:99px;object-fit:cover;border-radius:10px;border:0;">
                                </a>
                            </td>
                            <td valign="top">
                                @if ($e->programme)
                                    <div style="color:#1E6541;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;">{{ $e->programme->name }}</div>
                                @endif
                                <div style="font-size:17px;font-weight:700;line-height:1.25;color:#001C41;margin-top:3px;">
                                    <a href="{{ $e->canonicalUrl() }}" style="color:#001C41;text-decoration:none;">{{ $e->name }}</a>
                                </div>
                                <div style="margin-top:6px;font-size:12px;color:#5E6975;">
                                    <span style="display:inline-block;background:#F5F9F9;border:1px solid #E4EAEA;border-radius:20px;padding:2px 10px;color:#1E6541;font-weight:700;">{{ $badges[$e->media_type] ?? 'Émission' }}</span>
                                    @if ($e->duration)
                                        <span style="margin-left:6px;">{{ (int) round($e->duration) }} min</span>
                                    @endif
                                </div>
                            </td>
                        </tr></table>
                    </td></tr>
                @endforeach

                {{-- ============ CTA ACCUEIL ============ --}}
                <tr><td style="background:#ffffff;padding:22px 30px 30px;text-align:center;">
                    <div style="border-top:1px solid #E4EAEA;height:1px;line-height:1px;margin-bottom:24px;">&nbsp;</div>
                    <a href="{{ route('v2.home') }}" style="display:inline-block;background:#001C41;color:#ffffff;text-decoration:none;font-size:15px;font-weight:700;border-radius:10px;padding:13px 28px;">
                        Découvrir toutes nos émissions
                    </a>
                    <div style="margin-top:16px;font-size:13px;">
                        <a href="{{ route('v2.categories') }}" style="color:#1E6541;text-decoration:none;font-weight:600;">Catégories</a>
                        <span style="color:#E4EAEA;">&nbsp;·&nbsp;</span>
                        <a href="{{ route('v2.programmes') }}" style="color:#1E6541;text-decoration:none;font-weight:600;">Programmes</a>
                        <span style="color:#E4EAEA;">&nbsp;·&nbsp;</span>
                        <a href="{{ route('v2.themes') }}" style="color:#1E6541;text-decoration:none;font-weight:600;">Thèmes</a>
                    </div>
                </td></tr>

                {{-- ============ PIED DE PAGE (structure du footer du site) ============ --}}
                <tr><td style="background:#001C41;border-radius:0 0 18px 18px;padding:34px 30px;text-align:center;">
                    <img src="{{ url('/storage/img/blanco-peqe.png') }}" width="176" height="43" alt="Radio Bastides"
                         style="display:inline-block;border:0;width:176px;height:43px;">
                    <div style="color:#6CB786;font-size:13px;line-height:1.6;margin-top:12px;">La radio de Médias Citoyens en Villeneuvois.</div>

                    <a href="https://mediascitoyens.fr/" style="display:inline-block;margin-top:18px;text-decoration:none;">
                        <img src="{{ url('/storage/img/media-citoyens-blanco.png') }}" width="118" height="48" alt="Médias Citoyens en Villeneuvois"
                             style="display:block;border:0;width:118px;height:48px;">
                    </a>

                    <div style="margin-top:20px;">
                        <a href="https://www.facebook.com/radiobastides" style="text-decoration:none;">
                            <img src="{{ url('/storage/img/fb.png') }}" width="34" height="32" alt="Facebook" style="border:0;width:34px;height:32px;">
                        </a>
                        <span style="display:inline-block;width:10px;">&nbsp;</span>
                        <a href="https://www.instagram.com/radiobastides/" style="text-decoration:none;">
                            <img src="{{ url('/storage/img/ig.png') }}" width="34" height="32" alt="Instagram" style="border:0;width:34px;height:32px;">
                        </a>
                    </div>

                    <div style="margin-top:16px;font-size:13px;">
                        <a href="mailto:contactez-nous@mediascitoyens.fr" style="color:#ffffff;text-decoration:none;">contactez-nous@mediascitoyens.fr</a>
                    </div>

                    <div style="margin-top:22px;border-top:1px solid rgba(255,255,255,0.14);padding-top:16px;font-size:12px;color:#8b97a8;line-height:1.6;">
                        Vous recevez cet email car vous êtes inscrit à la newsletter de Radio Bastides.<br>
                        <a href="{{ $unsubscribeUrl }}" style="color:#8b97a8;text-decoration:underline;">Se désinscrire</a>
                    </div>
                </td></tr>

            </table>
        </td></tr>
    </table>
</body>
</html>
