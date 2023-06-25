//lib js
import 'bootstrap';
import 'lazysizes';

import './header/suggestion'
import './homepage'
import '@voerro/calamansi-js/dist/calamansi.min.js';

//global scss
import "../css/main.scss";
import '@voerro/calamansi-js/dist/calamansi.min.css';
import "../css/detann.scss";

document.addEventListener('DOMContentLoaded', function () {
  let playerElm = document.getElementById('audio-detann-player')
  let players = Calamansi.autoload();
  if (players.length > 0) {
    console.log(players[0]);
    players[0]._options.defaultAlbumCover = playerElm.getAttribute('data-album-cover')
  }
});

