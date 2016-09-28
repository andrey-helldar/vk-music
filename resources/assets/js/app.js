/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * Подгрузка и возврат компонента.
 *
 * @param name
 * @param path
 * @param result
 * @returns {*}
 */
function setComponent(name, path = '', result = true) {
    if (path.length == 0 || path === undefined) {
        path = name;
    }

    Vue.component(name, require('./components/' + path + '.vue'));

    if (result === true) {
        return Vue.component(name);
    }
}

/**
 * Routes.
 */
var VueRouter = require('vue-router');
Vue.use(VueRouter);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the body of the page. From here, you may begin adding components to
 * the application, or feel free to tweak this setup for your needs.
 */

// Компиляция ресурсов в компоненты.
setComponent('app-component', 'layouts/app', false);
setComponent('vk-auth');
setComponent('vk-verify');

/**
 * Инициализация роутера.
 *
 * @type {Router}
 */
var router = new VueRouter();

/**
 * Router redirect.
 */
router.redirect({
    '*': '/'
});

/**
 * Router map.
 */
router.map({
    '/':                {
        component: setComponent('index')
    },
    '/my':              {
        component: setComponent('my')
    },
    '/search':          {
        component: setComponent('search')
    },
    '/friends':         {
        component: setComponent('friends')
    },
    '/groups':          {
        component: setComponent('groups')
    },
    '/recommendations': {
        component: setComponent('recommendations')
    },
    '/popular':         {
        component: setComponent('popular')
    }
});

/**
 * Application.
 */
var App = Vue.extend({});

router.start(App, '#app');