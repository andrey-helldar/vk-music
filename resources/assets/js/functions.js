/**
 * Проверяем находится ли элемент в зоне видимости.
 * @param element
 * @returns {boolean}
 */
function isScrolledIntoView(element) {
    var docViewTop = $(window).scrollTop();
    var docViewBottom = docViewTop + $(window).height();

    var elemTop = $(element).offset().top;
    var elemBottom = elemTop + $(element).height();

    return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
}

/**
 * Загрузка аудио на страницах без автозагрузки)
 */
function loadAudios(codeElem = '.audio-auto-loading') {
    if ($('*').is(codeElem)) {
        app.loadAudios();
    }
}

$(document).ready(
    function () {
        $('select').material_select();
        $('.modal-trigger').leanModal();
        $(".button-collapse").sideNav();
        $('.character-counter').characterCounter();
        $('.tooltipped').tooltip({
            delay: 50
        });

        Materialize.updateTextFields();
        loadAudios();


        $(window).scroll(function () {

            /**
             * Загрузка аудио по списку.
             * @type {any}
             */
            //var moreAudio = $('.more-audio');
            //
            //if (isScrolledIntoView(moreAudio)) {
            //    moreAudio.click();
            //}
        });
    }
);