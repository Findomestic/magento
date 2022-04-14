document.addEventListener('click', function (event) {

    if (event.target.matches('#findomestic-popup-link')) {
        togglePopup();
    }

    if (event.target.matches('.findomestic-popup-close')) {
        togglePopup();
    }

}, false);


function togglePopup(){
    var el = document.querySelector('.findomestic-popup');
    el.classList.toggle('show');
}
