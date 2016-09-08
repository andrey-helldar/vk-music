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

$(document).ready(function () {
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
});