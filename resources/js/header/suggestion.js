const fetchSuggestion = async function(query) {
  return await fetch('/api/recherche/suggestion?query='+encodeURI(query) , {
    method: "get",
  }).then((response) => response.text() )

}


const initSuggestion = function () {
  let inputSuggestion = document.getElementById('input-suggestion');
  let placeSuggestion = document.getElementById('place-suggestion');
  console.log(inputSuggestion);

  if (inputSuggestion !== undefined) {
    inputSuggestion.addEventListener('input', function(e) {
      if (inputSuggestion.value.length > 3 && inputSuggestion.value.length < 55) {
        fetchSuggestion(inputSuggestion.value).then((response) => {
          placeSuggestion.innerHTML = response;
        })
      }
    })
  }
}

document.addEventListener('DOMContentLoaded', initSuggestion())
