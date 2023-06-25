//lib js
window.VIDEOJS_NO_DYNAMIC_STYLE = true
import 'bootstrap';
import 'lazysizes';

import './header/suggestion'
import './homepage'
import '@voerro/calamansi-js/dist/calamansi.min.js';
import videojs from "video.js";

//global scss
import "../css/main.scss";
import '@voerro/calamansi-js/dist/calamansi.min.css';
import '@videojs/themes/dist/forest/index.css';
import '@videojs/themes/dist/forest/index.css';
import "../css/detann.scss";


document.addEventListener('DOMContentLoaded', function () {
  let playerElm = document.getElementById('audio-detann-player')
  let playerElm2 = document.getElementById('video-detann-player')

  if (playerElm) {
    let players = Calamansi.autoload();
    if (players.length > 0) {
      players[0]._options.defaultAlbumCover = playerElm.getAttribute('data-album-cover')
      players[0]._options.playlists.default.map( (elm) => {
        elm.info.titleOrFilename = playerElm.getAttribute('data-file-name')
        elm.cover = playerElm.getAttribute('data-album-cover')
      } ) ;
    }
  }
  else if (playerElm2) {
    let players = videojs('video-detann-player', {
      fluid: true,
    });
  }


});

