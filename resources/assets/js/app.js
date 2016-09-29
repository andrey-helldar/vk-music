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
import VueRouter from 'vue-router';
Vue.use(VueRouter);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the body of the page. From here, you may begin adding components to
 * the application, or feel free to tweak this setup for your needs.
 */

// Компиляция ресурсов в компоненты.
setComponent('app-component', 'layouts/app', false);
setComponent('vk-verify');

/**
 * Инициализация роутера.
 *
 * @type {Router}
 */
window.router = new VueRouter();

/**
 * Router redirect.
 */
//router.redirect({
//    '*': '/'
//});

/**
 * Router map.
 */
router.map({
    '/':                {
        name:      'index',
        component: setComponent('index')
    },
    '/my':              {
        name:      'my',
        component: setComponent('my')
    },
    '/search':          {
        name:      'search',
        component: setComponent('search')
    },
    '/friends':         {
        name:      'friends',
        component: setComponent('friends')
    },
    '/groups':          {
        name:      'groups',
        component: setComponent('groups')
    },
    '/recommendations': {
        name:      'recommendations',
        component: setComponent('recommendations')
    },
    '/popular':         {
        name:      'popular',
        component: setComponent('popular')
    },
    /**
     * Authenticate
     */
    '/auth':            {
        name:      'auth',
        component: setComponent('vk-auth')
    }
});

/**
 * Application.
 */
var App = Vue.extend({});

router.start(App, '#app');