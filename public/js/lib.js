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

$(document).ready(
    function () {
        $('select').material_select();
        $('.modal-trigger').leanModal();
        $(".button-collapse").sideNav();
        $('.tooltipped').tooltip({delay: 50});

        Materialize.updateTextFields();


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
//# sourceMappingURL=lib.js.map
