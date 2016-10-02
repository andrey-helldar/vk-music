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
import VueRouter from "vue-router";
Vue.use(VueRouter);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the body of the page. From here, you may begin adding components to
 * the application, or feel free to tweak this setup for your needs.
 */

// Компиляция ресурсов в компоненты.
//setComponent('app-component', 'layouts/app', false);
setComponent('vk-verify');

/**
 * Router redirect.
 */
//router.redirect({
//    '*': '/'
//});

/**
 * Router map.
 */
const routes = [
    {
        name:      'index',
        path:      '/',
        //component: setComponent('index')
        component: {
            template: '<h1>TEST</h1>'
        }
    },
    {
        name:      'foo',
        path:      '/foo',
        component: {
            template: '<h1>FOO</h1>'
        }
    },
    {
        name:      'bar',
        path:      '/bar',
        component: {
            template: '<h1>BAR</h1>'
        }
    },
    //{
    //    name:      'my',
    //    path:      '/my',
    //    component: setComponent('my')
    //},
    //{
    //    name:      'search',
    //    path:      '/search',
    //    component: setComponent('search')
    //},
    //{
    //    name:      'friends',
    //    path:      '/friends',
    //    component: setComponent('friends')
    //},
    //{
    //    name:      'groups',
    //    path:      '/groups',
    //    component: setComponent('groups')
    //},
    //{
    //    name:      'recommendations',
    //    path:      '/recommendations',
    //    component: setComponent('recommendations')
    //},
    //{
    //    name:      'popular',
    //    path:      '/popular',
    //    component: setComponent('popular')
    //},
    ///**
    // * Authenticate
    // */
    //{
    //    name:      'auth',
    //    path:      '/auth',
    //    component: setComponent('vk-auth')
    //},
    //{
    //    name:      'verify',
    //    path:      '/verify',
    //    component: setComponent('vk-verify')
    //}
];

/**
 * Инициализация роутера.
 *
 * @type {Router}
 */
const router = new VueRouter({
    routes // short for routes: routes
});

/**
 * Application.
 */
const app = new Vue({
    router
}).$mount('#app');