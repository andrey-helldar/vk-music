<template>
    <div class="container">
        <div class="row">

            <div class="col s6" v-for="item in items" v-if="item.panel.is_show === true">
                <div class="panel hoverable">
                    <div class="panel-image">
                        <i class="material-icons">{{ item.panel.icon }}</i>
                    </div>
                    <div class="panel-content">
                        <h2>{{ item.title }}</h2>
                        <p>{{ item.panel.description }}</p>

                        <div class="panel-action">
                            <a href="{{ item.url }}">
                                Go to page
                            </a>
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
        ready(){
            app.console('Component Index ready.');
        },
        asyncData(){
            this.getItems();
        },
        methods: {
            getItems(){
                this.$http.get('/api/topmenu').then(
                        function (response) {
                            if (response.data.error == undefined) {
                                this.items = response.data.response;
                            } else {
                                app.info(response.data.error, 'error');
                            }

                            this.$root.hideLoader();
                        }, function (response) {
                            app.info(response.data.error, 'error');
                            this.$root.hideLoader();
                        }
                );
            }
        }
    }
</script>
