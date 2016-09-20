<template>
    <div class="row">

        <div class="col s6">
            <div class="panel hoverable">
                <div class="panel-image">
                    <i class="material-icons">audiotrack</i>
                </div>
                <div class="panel-content">
                    <h2>My audios</h2>
                    <p>This is a description</p>

                    <div class="panel-action">
                        <a href="#">
                            Go to page
                        </a>
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
            app.console('Component Main ready.');
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
                        }, function (response) {
                            app.info(response.data.error, 'error');
                        }
                );
            }
        }
    }
</script>
