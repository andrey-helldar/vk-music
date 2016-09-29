<template>
    <nav>
        <div class="container">
            <div class="nav-wrapper">
                <a href="#" data-activates="mobile-demo" class="button-collapse"><i class="material-icons">menu</i></a>

                <!--start: Пункт с именем юзера-->
                <ul class="left hide-on-med-and-down">
                    <li>
                        <a v-link="{path: '/'}">
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

                <ul class="right hide-on-med-and-down selection">
                    <li v-for="item in items" v-show="item.is_active">
                        <a v-link="{path: item.url}">
                            <i class="material-icons" v-if="item.icon">{{ item.icon }}</i>
                            {{ item.title }}
                        </a>
                    </li>
                </ul>

                <ul class="side-nav selection" id="mobile-demo">
                    <li v-for="item in items" v-show="item.is_active">
                        <a v-link="{path: item.url}">
                            <i class="material-icons" v-if="item.icon">{{ item.icon }}</i>
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
            this.getTopMenu();
            appFunc.console('Component Top Menu ready.');
        },
        methods: {
            /**
             * Получение списка меню.
             */
            getTopMenu(){
                this.$http.get('top.menu').then(
                        function (response) {
                            if (response.data.error == undefined) {
                                this.items = response.data.response;
                            } else {
                                appFunc.info(response.data.error, 'error');
                            }
                        }, function (response) {
                            appFunc.info(response.data.error, 'error');
                        }
                );
            },
            /**
             * Получение параметра пользователя из родительского элемента.
             *
             * @param param
             * @param defaultText
             * @returns {*}
             */
            user(param, defaultText = undefined){
                var data = this.$root.$refs.app.$data.user.info[param];

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
