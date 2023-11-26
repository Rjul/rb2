// import 'bootstrap';
import "../../css/admin/admin.scss";

//surchargé le js admin ici (jpense pas mais bon)

//--------------------------------------//
//      DURATION EMISIONS ADMIN         //
//--------------------------------------//
let inputElm = document.getElementsByName('emission[duration]')[0];
if (inputElm) {
  inputElm.addEventListener('change', (e) => {
    let imploded = e.target.value.split('.');
    if ( imploded[1] > 59 && imploded[1] < 89 ) {
      imploded[0]++
      imploded[1] = 0
      e.target.value = imploded.concat()
    } else if (imploded[1] > 88) {
      imploded[1] = 59
      e.target.value = imploded.concat()
    }
  })
}
