<template>
    <div v-cloak class="vh">
        <header-component ref="header"></header-component>

        <main>
            <header>
                <top-menu ref="topMenu"></top-menu>
            </header>

            <loader-screen ref="loaderScreen"></loader-screen>

            <router-view></router-view>
        </main>

        <footer-component></footer-component>
    </div>
</template>
<script>
    import HeaderComponent from './header.vue'
    import FooterComponent from './footer.vue'
    import LoaderScreen from '../functions/loader-screen.vue'
    import TopMenu from '../functions/top-menu.vue'
    export default{
        data(){
            return {
                user: {
                    info: {}
                }
            }
        },
        props:      {
            auth:             String,
            default:          false,
            errorDescription: ''
        },
        computed:   {
            normalizedAuth() {
                return this.auth.length > 0 || this.auth !== 0;
            }
        },
        components: {
            HeaderComponent,
            FooterComponent,
            LoaderScreen,
            TopMenu
        },
        beforeMount()
        {
            this.checkAuth();
            this.getUserInfo();
        },
        mounted()
        {
            appFunc.console('Component Main ready.');
        },
        methods:    {
            checkErrors(){
                if (this.errorDescription.length > 0 && this.$route.name !== 'error') {
                    router.push({
                        name: 'error'
                    });
                }
            },
            /**
             * Проверка авторизации с необходимой переадресацией.
             */
            checkAuth(){
                /**
                 * Так как проверка метода будет повторять места проверки аутентификации,
                 * будет логичнее объявить метод внутри ее проверки)
                 */
                this.checkErrors();

                /**
                 * А теперь аутентификация))
                 */
                var withoutAuth = [
                    'auth',
                    'feedback',
                    'index'
                ];
                var isRouteAuth = withoutAuth.indexOf(this.$route.name) !== -1;

                if (this.auth === '0' && !isRouteAuth) {
                    router.push({
                        name: 'auth'
                    });
                }

                if (this.auth === '1' && isRouteAuth && this.$route.name !== 'index') {
                    window.location.href = '/';
                }
            },
            /**
             * Отображение лоадера.
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
                this.$http.get('current.user.info').then(
                        (response) => {
                            if (response.data.error == undefined) {
                                this.user.info = response.data.response;
                            }
                        },
                        (response) => {
                            appFunc.console(response.statusText);
                        });
            },
            /**
             * Аналог хелпера для получения переводенных значений переменных.
             *
             * @param param
             * @param values
             * @returns {*}
             */
            trans(param, values = []){
                var text = window.Laravel[param];

                if (text === undefined) {
                    return param;
                }

                if (values.length == 0) {
                    return text;
                }

                values.forEach(function (key, value) {
                    text = text.replace(':' + key, value);
                });

                return text;
            }
        }
    }
</script>
