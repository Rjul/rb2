import '../../css/layout/_suggestion.scss'

const fetchSuggestion = async function(query) {
  return await fetch('/api/recherche/suggestion?query='+encodeURI(query) , {
    method: "get",
  }).then((response) => response.text() )
}

const locationSearch = function () {
  let inputSuggestion = document.getElementById('input-suggestion');
  let searchBtnElm    = document.getElementById('search-btn');
  if (searchBtnElm !== undefined) {
    searchBtnElm.addEventListener('click', (e) => {
      if (inputSuggestion.value.length > 1 && inputSuggestion.value.length < 55) {
        window.location.href = '/recherche?query='+inputSuggestion.value;
      }
    })
    inputSuggestion.addEventListener("keypress", ({key}) => {
      if (key === "Enter" && inputSuggestion.value.length > 1 && inputSuggestion.value.length < 55) {
        window.location.href = '/recherche?query='+inputSuggestion.value;
      }
    })
  }
}

const initSuggestion = function () {
  let inputSuggestionElm = document.getElementById('input-suggestion');
  let placeSuggestionElm = document.getElementById('place-suggestion');
  let searchBtnElm    = document.getElementById('search-btn');
  let btnSearchSuggestionElm = document.getElementById('btn__search-suggestion');

  if (btnSearchSuggestionElm !== undefined && inputSuggestionElm !== undefined && placeSuggestionElm !== undefined) {
    btnSearchSuggestionElm.addEventListener('click', (e) => {
      setTimeout(() => {
        inputSuggestionElm.focus();

      }, 500);
    })
  }
  if (inputSuggestionElm !== undefined) {
    inputSuggestionElm.addEventListener('input', function(e) {
      if (inputSuggestionElm.value.length > 1 && inputSuggestionElm.value.length < 55) {
        fetchSuggestion(inputSuggestionElm.value).then((response) => {
          placeSuggestionElm.innerHTML = response;
            e.stopPropagation();
          })
      } else {
        placeSuggestionElm.innerHTML = '';
      }
    })
  }
}
document.addEventListener('DOMContentLoaded', () => {
  initSuggestion()
  locationSearch()
})
