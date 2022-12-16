import '../../css/layout/_suggestion.scss'

const fetchSuggestion = async function(query) {
  return await fetch('/api/recherche/suggestion?query='+encodeURI(query) , {
    method: "get",
  }).then((response) => response.text() )

}


const initSuggestion = function () {
  let inputSuggestion = document.getElementById('input-suggestion');
  let placeSuggestion = document.getElementById('place-suggestion');
  let maskPopupElm    = document.getElementById('mask-popup');
  console.log(inputSuggestion);

  if (inputSuggestion !== undefined) {
    inputSuggestion.addEventListener('input', function(e) {
      if (inputSuggestion.value.length > 1 && inputSuggestion.value.length < 55) {
        fetchSuggestion(inputSuggestion.value).then((response) => {
          placeSuggestion.innerHTML = response;
          maskPopupElm.classList.add('active');
          maskPopupElm.addEventListener('click', (e) => {
            e.stopPropagation();
            placeSuggestion.innerHTML = '';
            maskPopupElm.classList.remove('active');

          })
        })
      } else {
        placeSuggestion.innerHTML = '';
        maskPopupElm.classList.remove('active');
      }
    })
  }
}

document.addEventListener('DOMContentLoaded', initSuggestion)
