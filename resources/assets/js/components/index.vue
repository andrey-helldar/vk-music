<template>
    <div v-cloak>
        <div class="container">
            <div class="row">

                <div v-for="item in items" v-cloak>
                    <div class="col s6" v-if="item.is_active">
                        <div class="panel hoverable">
                            <div class="panel-image">
                                <i class="material-icons">{{ item.icon }}</i>
                            </div>
                            <div class="panel-content">
                                <h2>{{ item.title }}</h2>
                                <p>{{ item.description }}</p>

                                <div class="panel-action">
                                    <router-link :to="{path: item.url}">
                                        Go to page
                                    </router-link>
                                </div>
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
                items: []
            }
        },
        mounted(){
            this.$parent.checkAuth();
            this.getItems();
            appFunc.console('Component Index ready.');
        },
        methods: {
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
            }
        }
    }
</script>
