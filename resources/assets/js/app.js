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
        'search',
        'filter',
        'topmenu',
        'audio',
        'friends',
        'groups',
        'vk-auth',
        'vk-verify'
    ];

// Компиляция ресурсов в компоненты.
if (components.length) {
    components.forEach(
        function (item) {
            Vue.component(item, require('./components/' + item + '.vue'));
        }
    );
}

new Vue({
        el:      'main',
        data:    {
            user: {
                info: {}
            }
        },
        ready(){
            app.console('Component Main ready.');

            this.getUserInfo();
        },
        methods: {
            /**
             * Обображение лоадера.
             *
             * @param text
             * @param description
             * @param style_type
             */
            showLoader(text = 'Loading...', description = '', style_type = 'wait'){
                var loaderElement = this.$refs.loaderScreen;
                loaderElement.showLoader(text, description, style_type);
            },
            /**
             * Скрытие лоадера.
             */
            hideLoader(){
                var loaderElement = this.$refs.loaderScreen;
                loaderElement.hideLoader();
            },
            /**
             * Получение информации о текущем пользователе.
             */
            getUserInfo(){
                this.$http.get('/api/current.user.info').then(
                    function (response) {
                        if (response.data.error == undefined) {
                            this.user.info = response.data.response;
                        }
                    }, function (response) {
                        //app.info(response.data.error, 'error');
                    }
                );
            }
        }
    }
);
