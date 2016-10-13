<template>
    <nav>
        <div class="container">
            <div class="nav-wrapper">
                <a href="#" data-activates="mobile-demo" class="button-collapse"><i class="material-icons">menu</i></a>

                <!--start: Пункт с именем юзера-->
                <ul class="left hide-on-med-and-down">
                    <li>
                        <router-link :to="{name: 'index'}">
                            {{ hello }}!
                        </router-link>
                    </li>
                </ul>

                <ul class="right hide-on-med-and-up">
                    <li>
                        <router-link :to="{name: 'index'}">
                            {{ hello }}!
                        </router-link>
                    </li>
                </ul>
                <!--end: Пункт с именем юзера-->

                <ul class="right hide-on-med-and-down selection">
                    <li v-for="item in items" v-show="item.is_active">
                        <router-link :to="{path: item.url}">
                            <i class="material-icons" v-if="item.icon">{{ item.icon }}</i>
                            {{ trans(item.title) }}
                        </router-link>
                    </li>
                </ul>

                <ul class="side-nav selection" id="mobile-demo">
                    <li v-for="item in items" v-show="item.is_active">
                        <router-link :to="{path: item.url}">
                            <i class="material-icons" v-if="item.icon">{{ item.icon }}</i>
                            {{ trans(item.title) }}
                        </router-link>
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
                items:      [],
                hello:      'Hello!',
                first_name: 'Guest'
            }
        },
        mounted() {
            appFunc.console('Component Top Menu ready.');

            this.getTopMenu();
            this.user();
            this.locale();
        },
        methods: {
            locale(){
                this.hello = this.$root.$refs.app.trans('interface.context.hello');
                this.hello += this.first_name;
            },
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
             * @returns {*}
             */
            user(){
                this.first_name = window.Laravel.trans['user.first_name'];
            },
            /**
             * Получение значения переведенного параметра интерфейса.
             *
             * @param param
             * @returns {*}
             */
            trans(param){
                if (param.length == 0) {
                    return '';
                }

                return this.$parent.trans('interface.pages.' + param + '.title');
            }
        }
    }
</script>
