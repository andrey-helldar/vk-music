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
        'index',
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

window.appVue = new Vue({
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
            },
            /**
             * Передача параметра на загрузку аудио.
             * Необходимо при работе с некоторыми страницами.
             *
             * @param url
             * @param title
             * @param owner_id
             * @param owner_type
             * @param postData
             */
            loadAudios(url = '/api/audio.user', title = 'Your audio', owner_id = 0, owner_type = 'default', postData = {}){
                this.showLoader();
                this.getCurrentPage();

                var audio = this.$refs.audio;
                audio.activePage.title = title;
                audio.activePage.url = url;
                audio.vk.count_all = 0;
                audio.vk.offset = 0;
                audio.vk.owner_id = owner_id;
                audio.vk.owner_type = owner_type;
                audio.items = [];

                audio.getAudio(true, postData);
            },
            getCurrentPage(){
                var sharp = window.location.href.indexOf('#');

                if (sharp !== false) {
                    var page = window.location.href.substr(sharp + 1);
                    var url = '';

                    switch (page) {
                        default:
                            url:'/api/audio.user'
                    }
                }
            }
        }
    }
);

/**
 * Routes.
 */
//require('./routes');