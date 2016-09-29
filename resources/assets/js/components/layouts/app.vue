<template>
    <header-component></header-component>

    <main>
        <header>
            <top-menu v-ref:top-menu></top-menu>
        </header>

        <loader-screen v-ref:loader-screen></loader-screen>

        <router-view></router-view>
    </main>

    <footer-component></footer-component>
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
            auth: {
                default: false,
                coerce:  (value)=> {
                    return value.length > 0;
                }
            }
        },
        components: {
            HeaderComponent,
            FooterComponent,
            LoaderScreen,
            TopMenu
        },
        ready(){
            this.checkAuth();
            this.getUserInfo();

            appFunc.console('Component Main ready.');
        },
        methods:    {
            /**
             * Проверка авторизации с необходимой переадресацией.
             */
            checkAuth(is_auth_page = false){
                if (!this.auth && !is_auth_page) {
                    router.go({
                        name: 'auth'
                    });
                }

                if (this.auth && is_auth_page) {
                    router.go({
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
