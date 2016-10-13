<template>
    <div class="container">
        <div class="row">

            <div v-for="item in items" v-cloak>
                <div class="col s6" v-if="item.is_active">
                    <div class="panel hoverable">
                        <div class="panel-image">
                            <i class="material-icons">{{ item.icon }}</i>
                        </div>
                        <div class="panel-content">
                            <h2>{{ trans(item.key + '.title') }}</h2>
                            <p>{{ trans(item.key + '.description') }}</p>

                            <div class="panel-action">
                                <router-link :to="{path: item.url}">
                                    {{ locale.goToPage }}
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>
<script>
    export default{
        data(){
            return {
                items:  [],
                locale: {
                    goToPage: 'Go to page'
                }
            }
        },
        beforeMount(){
            this.$parent.checkAuth();
            this.locale();
        },
        mounted(){
            this.getItems();
            appFunc.console('Component Index ready.');
        },
        methods: {
            locale(){
                this.locale.goToPage = this.$root.$refs.app.trans('interface.buttons.go_to_page');
            },
            getItems(){
                this.$http.get('main.blocks').then(
                        function (response) {
                            if (response.data.error == undefined) {
                                this.items = response.data.response;
                            } else {
                                appFunc.info(response.data.error, 'error');
                            }

                            this.$parent.hideLoader();
                        }, function (response) {
                            appFunc.info(response.data.error, 'error');
                            this.$parent.hideLoader()
                        }
                );
            },
            /**
             * Получение значения переведенного параметра интерфейса.
             *
             * @param param
             * @returns {*}
             */
            trans(key, param = []){
                if (key.length == 0) {
                    return '';
                }

                return this.$parent.trans('interface.pages.' + key, param);
            }
        }
    }
</script>
