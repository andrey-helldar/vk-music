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
        components: {
            HeaderComponent,
            FooterComponent,
            LoaderScreen,
            TopMenu
        },
        ready(){
            this.getUserInfo();
            appFunc.console('Component Main ready.');
        },
        methods:    {
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
                this.$http.get('current.user.info').then(
                        (response) => {
                            if (response.data.error == undefined) {
                                this.user.info = response.data.response;
                            }
                        }, (response)=> {
                            //appFunc.info(response.data.error, 'error');
                            appFunc.console(response.statusText);
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
            loadAudios(url = '', title = 'My audios', owner_id = 0, owner_type = 'default', postData = {}){
                this.showLoader();

                if (url.length == 0) {
                    return;
                }

                var audio = this.$refs.audio;
                audio.activePage.title = title;
                audio.activePage.url = url;
                audio.vk.count_all = 0;
                audio.vk.offset = 0;
                audio.vk.owner_id = owner_id;
                audio.vk.owner_type = owner_type;
                audio.items = [];

                audio.getAudio(true, postData);
            }
        }
    }
</script>
