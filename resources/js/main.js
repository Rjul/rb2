//lib js
import 'bootstrap';
import 'lazysizes';



//global scss
import "../css/main.scss";

// Anime text annonce sous header
let containerAnnonces = document.querySelector("#header_annonce");
let text = document.querySelector("#header_annonce span");
if (containerAnnonces) {
    if (containerAnnonces.clientWidth < text.clientWidth) {
        text.classList.add("animate");
    }
}


