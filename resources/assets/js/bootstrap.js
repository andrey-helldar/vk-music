window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

window.$ = window.jQuery = require('jquery');
require('../vendor/materialize-css/js/bin/materialize.min');

/**
 * Vue is a modern JavaScript library for building interactive web interfaces
 * using reactive data binding and reusable components. Vue's API is clean
 * and simple, leaving you to focus on building your next great project.
 */

window.Vue = require('vue');
var VueResource = require('vue-resource');
var VueAsyncData = require('vue-async-data');

Vue.use(VueResource);
Vue.use(VueAsyncData);

/**
 * We'll register a HTTP interceptor to attach the "CSRF" header to each of
 * the outgoing requests issued by this application. The CSRF middleware
 * included with Laravel will automatically verify the header's value.
 */

Vue.http.interceptors.push(
    (request, next) => {
        request.headers['X-CSRF-TOKEN'] = Laravel.csrfToken;

        next();
    }
);

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from "laravel-echo"

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: 'your-pusher-key'
// });

/**
 * Other
 */
window.app = {
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
     */
    info: function (text, style = 'info') {
        if (app.empty(style)) {
            style = 'info';
        }

        Materialize.toast(text, 4000, app.toast.style[style]);
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
        if (app.debug !== true) {
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
            app.info('Unknown data', 'error');
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
            obj = $.map(obj, function (el) {
                    return el;
                }
            );
        }

        return obj;
    }
};

/**
 * Other parameters of Vue.js.
 */
Vue.config.async = true;
Vue.config.devtools = app.debug; // DevTools mode is only available in development build. In production set FALSE !
Vue.config.debug = app.debug; // Debug mode is only available in development build. In production set FALSE !
Vue.config.silent = !app.debug; //Suppress all Vue.js logs and warnings.
Vue.config.unsafeDelimiters = ['{!!', '!!}']; // Change the raw HTML interpolation delimiters.


$(document).ready(
    function () {
        $('.modal-trigger').leanModal();
        $('select').material_select();
        $(".button-collapse").sideNav();

        Materialize.updateTextFields();
    }
);
