/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the body of the page. From here, you may begin adding components to
 * the application, or feel free to tweak this setup for your needs.
 */

    // Здесь будем перечислять все загружаемые компоненты
var components = [
        'loader-screen',
        'topmenu',
        'audios',
        'vk-auth',
        'vk-verify'
    ];

// Компиляция ресурсов в компоненты.
if (components.length) {
    components.forEach(
        function (item)
        {
            Vue.component(item, require('./components/' + item + '.vue'));
        }
    );
}

new Vue({
        el: 'main'
    }
);
