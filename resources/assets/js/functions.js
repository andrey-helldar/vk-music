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
//function loadAudios(codeElem = '.audio-auto-loading') {
//    if ($('*').is(codeElem)) {
//        appFunc.loadAudios();
//    }
//}

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
        //loadAudios();


        //$(window).scroll(function () {

        /**
         * Загрузка аудио по списку.
         * @type {any}
         */
        //var moreAudio = $('.more-audio');
        //
        //if (isScrolledIntoView(moreAudio)) {
        //    moreAudio.click();
        //}
        //});
    }
);

window.appFunc = {
    debug: true, // Global parameter to settings of Vue.js.
    trans: [], // Перевод элементов.
    page:  0, // Номер текущей страницы. Нумерация начинается с нуля.

    // Стили всплывающих уведомлений.
    toast: {
        style: {
            error:   'red white-text',
            success: 'green white-text',
            info:    'blue white-text'
        }
    },

    /**
     * Всплывающее уведомление.
     * Для удобства, функция вынесена в отдельную.
     *
     * @param {string} text
     * @param {string} style
     * @param {int} delay
     */
    info: function (text, style = 'info', delay = 4000) {
        if (appFunc.empty(style)) {
            style = 'info';
        }

        if (delay < 1000 || delay === undefined) {
            delay = 4000;
        }

        Materialize.toast(text, delay, appFunc.toast.style[style]);
    },

    /**
     * Определение "пустой" переменной.
     *
     * @param data
     * @returns {boolean}
     */
    empty: function (data) {
        return data === '' || data === undefined;
    },

    /**
     * Вывод уведомлений в консоль.
     *
     * @param data
     * @param type
     * @returns {boolean}
     */
    console: function (data, type) {
        if (appFunc.debug !== true) {
            return false;
        }

        switch (type) {
            case 'info':
                console.info(data);
                break;

            case 'warning':
                console.warn(data);
                break;

            case 'error':
                console.error(data);
                break;

            default:
                console.log(data);
        }
    },

    build_query(obj) {
        if (typeof obj !== 'array' && typeof obj !== 'object') {
            appFunc.info('Unknown data', 'error');
            return '';
        }

        var query = [];

        for (var key in obj) {
            query.push(key + '=' + obj[key]);
        }

        return query.join('&');
    },

    /**
     * Конвертирование объектов в массив.
     *
     * @param obj
     * @returns {*}
     */
    toArray(obj){
        if (typeof obj === 'object') {
            obj = $.map(obj, function (value, index) {
                    return [value];
                }
            );
        }

        return obj;
    },

    /**
     * Переводим секунды в человеко-понятное время.
     *
     * @param duration
     * @returns {string}
     */
    timeToHumans(duration){
        var date = new Date(1970, 1, 1, 0, 0, duration);
        var hours = date.getHours();
        var minutes = date.getMinutes();
        var seconds = date.getSeconds();
        var exp = [];

        exp = appFunc.pushDateArray(exp, hours);
        exp = appFunc.pushDateArray(exp, minutes, true);
        exp = appFunc.pushDateArray(exp, seconds, true);

        return exp.join(':');
    },
    /**
     * Если число однозначное - добавляем ведущий ноль.
     *
     * @param num
     * @returns {*}
     */
    numAddZero(num){
        if (num < 10) {
            return '0' + num;
        }

        return num;
    },
    /**
     * Если число больше нуля - добавляем его в массив.
     * Либо если передан параметр принудительного добавления.
     *
     * @param arr
     * @param num
     * @param zero
     * @returns {*}
     */
    pushDateArray(arr, num = 0, zero = false){
        if (num > 0 || zero === true) {
            num = appFunc.numAddZero(num);
            arr.push(num);
        }

        return arr;
    }
};