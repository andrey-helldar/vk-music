<template>
    <nav>
        <div class="container">
            <div class="nav-wrapper">
                <a href="#" data-activates="mobile-demo" class="button-collapse"><i class="material-icons">menu</i></a>

                <!--start: Пункт с именем юзера-->
                <ul class="left hide-on-med-and-down">
                    <li>
                        <a href="/">
                            Hello, {{ user('first_name', 'Guest') }}!
                        </a>
                    </li>
                </ul>

                <ul class="right hide-on-med-and-up">
                    <li>
                        <a href="/">
                            Hello, {{ user('first_name', 'Guest') }}!
                        </a>
                    </li>
                </ul>
                <!--end: Пункт с именем юзера-->

                <ul class="right hide-on-med-and-down">
                    <div v-for="item in items" v-cloak>
                        <li v-if="item.is_active">
                            <a to="/" @click="setPage($index)">
                                <i class="material-icons" v-if="item.icon">{{ item.icon }}</i>
                                <span v-if="item.show_title">
                                {{ item.title }}
                            </span>
                            </a>
                        </li>
                    </div>
                </ul>

                <ul class="side-nav" id="mobile-demo">
                    <div v-for="item in items" v-cloak>
                        <li v-if="item.is_active">
                            <a to="/" @click="setPage($index)">
                                <i class="material-icons" v-if="item.icon">{{ item.icon }}</i>
                                <span v-if="item.show_title">
                                {{ item.title }}
                            </span>
                            </a>
                        </li>
                    </div>
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
            this.getTopmenu();
            appFunc.console('Component Top Menu ready.');
        },
        methods: {
            /**
             * Получение списка меню.
             */
            getTopmenu(){
                this.$http.get('topmenu').then(
                        function (response) {
                            if (response.data.error == undefined) {
                                this.items = response.data.response;
                                this.setTopMenuActiveDefault();
                            } else {
                                appFunc.info(response.data.error, 'error');
                            }
                        }, function (response) {
                            appFunc.info(response.data.error, 'error');
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

                $(".button-collapse").sideNav('hide');

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
                this.$root.loadAudios(item.api, item.title);
            },
            /**
             * Получение параметра пользователя из родительского элемента.
             *
             * @param param
             * @param defaultText
             * @returns {*}
             */
            user(param, defaultText = undefined){
                var data = this.$root.$data.user.info[param];

                if (data === undefined) {
                    if (defaultText !== undefined) {
                        return defaultText;
                    }

                    return '';
                }

                return data;
            }
        }
    }
</script>
