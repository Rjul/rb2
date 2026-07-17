{{-- defer : le script s'exécute avant DOMContentLoaded (où init() est appelé) sans bloquer le rendu --}}
<script src="/tarteaucitronjs/tarteaucitron.js" defer></script>
<script type="text/javascript">
    // const initTAC = function () {
    document.addEventListener('DOMContentLoaded', function () {
        tarteaucitron.init({
            "privacyUrl": "", /* Privacy policy url */
            "bodyPosition": "bottom", /* or top to bring it as first element for accessibility */

            "hashtag": "#tarteaucitron", /* Open the panel with this hashtag */
            "cookieName": "tarteaucitron", /* Cookie name */

            "orientation": "middle", /* Banner position (top - bottom) */

            "groupServices": false, /* Group services by category */
            "showDetailsOnClick": true, /* Click to expand the description */
            "serviceDefaultState": "wait", /* Default state (true - wait - false) */

            "showAlertSmall": false, /* Show the small banner on bottom right */
            "cookieslist": false, /* Show the cookie list */

            "closePopup": false, /* Show a close X on the banner */

            "showIcon": true, /* Show cookie icon to manage cookies */
            //"iconSrc": "", /* Optionnal: URL or base64 encoded image */
            "iconPosition": "BottomRight", /* BottomRight, BottomLeft, TopRight and TopLeft */

            "adblocker": false, /* Show a Warning if an adblocker is detected */

            "DenyAllCta" : true, /* Show the deny all button */
            "AcceptAllCta" : true, /* Show the accept all button when highPrivacy on */
            "highPrivacy": true, /* HIGHLY RECOMMANDED Disable auto consent */

            "handleBrowserDNTRequest": false, /* If Do Not Track == 1, disallow all */

            "removeCredit": false, /* Remove credit link */
            "moreInfoLink": true, /* Show more info link */

            "useExternalCss": false, /* If false, the tarteaucitron.css file will be loaded */
            "useExternalJs": false, /* If false, the tarteaucitron.js file will be loaded */

            //"cookieDomain": ".my-multisite-domaine.fr", /* Shared cookie for multisite */

            "readmoreLink": "", /* Change the default readmore link */

            "mandatory": true, /* Show a message about mandatory cookies */
            "mandatoryCta": true /* Show the disabled accept button when mandatory on */
        });

        tarteaucitron.user.multiplegtagUa = ['G-S2PNWJT6CK', 'GTM-TPVQC5K8'];
        (tarteaucitron.job = tarteaucitron.job || []).push('multiplegtag');

    });

    // Front v2 (SPA wire:navigate) : chaque navigation Livewire remplace le <body>,
    // ce qui ferait disparaître le bandeau/l'icône tarteaucitron et stopperait le
    // comptage des pages vues. On ré-accroche donc la racine du bandeau après chaque
    // navigation et on renvoie une page vue à GA (si le consentement a été donné).
    // Sans effet sur le legacy : les événements livewire:* n'y sont jamais émis.
    if (!window.__tacSpaBound) {
        window.__tacSpaBound = true;
        var __tacRoot = null;
        var __tacFirstLoad = true;

        // Avant le remplacement du body : on garde une référence au bandeau.
        document.addEventListener('livewire:navigate', function () {
            __tacRoot = document.getElementById('tarteaucitronRoot') || __tacRoot;
        });

        document.addEventListener('livewire:navigated', function () {
            if (__tacFirstLoad) { __tacFirstLoad = false; return; } // page vue initiale déjà comptée par init()
            if (__tacRoot && !document.body.contains(__tacRoot)) {
                document.body.appendChild(__tacRoot);
            }
            if (typeof gtag === 'function' && typeof tarteaucitron !== 'undefined') {
                (tarteaucitron.user.multiplegtagUa || []).forEach(function (id) {
                    gtag('config', id, { page_path: window.location.pathname + window.location.search });
                });
            }
        });
    }

</script>




