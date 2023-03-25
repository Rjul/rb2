const initSearch = () => {
  const facetsElms = document.getElementsByClassName('check-box__elm-search-engin');
  Array.from(facetsElms).forEach(elm => {
    elm.addEventListener('click', (e) => callSearch(e))
  })
}
const callSearch = (e) => {
  if (e.target.classList.contains('check-box__elm-duration-search-engin')) {
    removeOthersInputDuration(e);
  }
  let formElm = document.getElementById('searchEngineForm');
  setTimeout(function () {
    formElm.submit();
  }, 100)
}

const removeOthersInputDuration = (e) => {
  Array.from(document.getElementsByClassName('check-box__elm-duration-search-engin')).forEach(function (elm) {
    if (e.target !== elm) {
      desactiveCheckBox(elm)
    }
  })
}

const desactiveCheckBox = (elm) => {
  elm.value = false;
}
document.addEventListener('DOMContentLoaded', () => initSearch())
