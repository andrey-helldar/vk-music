<template>
    <div v-cloak class="vh">
        <header-component ref="header"></header-component>

        <main>
            <header>
                <top-menu ref="topMenu"></top-menu>
            </header>

            <loader-screen ref="loaderScreen"></loader-screen>

            <transition name="fade">
                <router-view></router-view>
            </transition>
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
            auth:    String,
            default: false
        },
        computed:   {
            normalizedAuth(){
                return this.auth.length > 0 || this.auth !== 0;
            }
        },
        components: {
            HeaderComponent,
            FooterComponent,
            LoaderScreen,
            TopMenu
        },
        beforeMount(){
            this.checkAuth();
            this.getUserInfo();
        },
        mounted(){
            appFunc.console('Component Main ready.');
        },
        methods:    {
            /**
             * Проверка авторизации с необходимой переадресацией.
             */
            checkAuth(){
                var withoutAuth = [
                    'auth',
                    'feedback',
                    'index'
                ];
                var isAuth = withoutAuth.indexOf(this.$route.name) !== -1;

                if (this.auth === '0' && !isAuth) {
                    router.push({
                        name: 'auth'
                    });
                }

                if (this.auth === '1' && isAuth) {
                    router.push({
                        name: 'index'
                    });
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
                        }, (response) => {
                            //appFunc.info(response.data.error, 'error');
                            appFunc.console(response.statusText);
                        }
                );
            }
        }
    }
</script>
