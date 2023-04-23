import '../../css/home/home.scss';



const initWaveSection = () => {
  let selectorElms = document.querySelectorAll('.btn-home_tag_card-selector');
  let cardElms = document.querySelectorAll('.card-home_tag_card-to-display');

  if (selectorElms && cardElms) {
    selectorElms.forEach((selectorElm) => {
      selectorElm.addEventListener('click', (event) => {
        if (event.target.datasets.get('aria-expanded') === 'false') {
          cardElms.forEach((cardElm) => {
            cardElm.classList.remove('show');
          });
          event.target.classList.add('show');
        }
        else {
          setTimeout(() => {
            event.target.classList.add('show');
          }, 200);
        }
      });
    });
  }
}
