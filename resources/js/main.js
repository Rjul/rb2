//lib js
import 'bootstrap';
import 'lazysizes';

import './header/suggestion'
import './homepage'

//global scss
import "../css/main.scss";
import Alpine from 'alpinejs'

window.Alpine = Alpine

Alpine.start()

// Anime text annonce sous header
let containerAnnonces = document.querySelector("#header_annonce");
let text = document.querySelector("#header_annonce span");
if (containerAnnonces) {
    if (containerAnnonces.clientWidth < text.clientWidth) {
        text.classList.add("animate");
    }
}


