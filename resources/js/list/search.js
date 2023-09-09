const initSearch = () => {
  const facetsElms = document.getElementsByClassName('check-box__elm-search-engin');
  Array.from(facetsElms).forEach(elm => {
    elm.addEventListener('click', (e) => callSearch(e))
  })
  const openBtnSidebarElm = document.getElementById('searchEngineButton');
  if (openBtnSidebarElm) {
    openBtnSidebarElm.addEventListener('click', (e) => {
      e.preventDefault();
      const sidebarElm = document.getElementById('search-aside');
      console.log(openBtnSidebarElm.dataset.open);
      if (openBtnSidebarElm.dataset.open == 'true') {
        console.log('open');
        sidebarElm.classList.remove('d-none');
        openBtnSidebarElm.dataset.open = false;
        changeBtnType('open');
      }
      else {
        sidebarElm.classList.add('d-none');
        openBtnSidebarElm.dataset.open = true;
        changeBtnType('closed');
      }

    })
  }
}
const changeBtnType = (state) => {
  let closedBtn = document.getElementById('show-closed');
  let openBtn = document.getElementById('show-opened');
  if (state == 'open') {
    closedBtn.classList.remove('d-none');
    openBtn.classList.add('d-none');
  }
  else {
    closedBtn.classList.add('d-none');
    openBtn.classList.remove('d-none');
  }

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
