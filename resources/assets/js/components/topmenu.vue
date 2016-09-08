<template>
    <nav>
        <div class="container">
            <div class="nav-wrapper">
                <a href="#" data-activates="mobile-demo" class="button-collapse"><i class="material-icons">menu</i></a>

                <ul class="right hide-on-med-and-down">
                    <li v-for="item in items" :class="{active: item.is_active}">
                        <a href="{{ item.url }}" @click="setPage($index)">
                            {{ item.title }}
                        </a>
                    </li>
                </ul>

                <ul class="side-nav" id="mobile-demo">
                    <li v-for="item in items" :class="{active: item.is_active}">
                        <a href="{{ item.url }}" @click="setPage($index)">
                            {{ item.title }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</template>

<script>
    export default {
        data(){
            return {
                items: []
            }
        },
        ready() {
            app.console('Component Top Menu ready.');
        },
        asyncData(){
            this.getTopmenu();
        },
        methods: {
            /**
             * Получение списка меню.
             */
            getTopmenu(){
                this.$http.get('/api/topmenu').then(
                        function (response) {
                            if (response.data.error == undefined) {
                                this.items = response.data.response;
                                this.setTopMenuActiveDefault();
                            } else {
                                app.info(response.data.error, 'error');
                            }
                        }, function (response) {
                            app.info(response.data.error, 'error');
                        }
                );
            },
            /**
             * Проверка активности элементов.
             */
            checkActivity(){
                var keyActive = -1;

                this.items.forEach(
                        function (item, key) {
                            if (item.is_active == true) {
                                keyActive = key;
                            }
                        }
                );

                return keyActive;
            },
            /**
             * Установка дефолтного значения активности элемента меню.
             */
            setTopMenuActiveDefault(){
                var keyActive = this.checkActivity();

                if (keyActive < 0 && this.items.length) {
                    this.setSelectItem(0);
                }
            },
            /**
             * Меняем активный элемент меню.
             */
            setPage(index){
                var item = this.items[index];

                if (item.api === undefined) {
                    this.$parent.showLoader('Please, wait...', 'Redirecting to ' + item.title);
                    location.href = item.url;
                    return false;
                }

                $('#search').val('');
                this.setSelectItem(index);
                this.setAudioData(this.items[index]);

                return false;
            },
            /**
             * Устанавливаем выделение в меню.
             *
             * @param index
             */
            setSelectItem(index){
                this.items.forEach(function (item, key) {
                    item.is_active = (key == index);
                });
            },
            /**
             * Загружаем аудио.
             *
             * @param item
             */
            setAudioData(item){
                this.$root.$refs.loaderScreen.showLoader();
                var audio = this.$root.$refs.audio;

                audio.activePage.title = item.title;
                audio.activePage.url = item.api;

                audio.items = [];
                audio.getAudio(true);
            }
        }
    }
</script>
