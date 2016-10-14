<template>
    <div class="container">
        <h3>
            {{ trans.title }}
        </h3>

        <div class="row center-align">
            <button class="btn btn-large red waves-effect waves-light" @click="actionLogout">
                {{ trans.acceptLogout }}
            </button>

            <button class="btn-flat btn-large waves-effect waves-green" @click="actionCancel">
                {{ trans.cancel }}
            </button>
        </div>
    </div>
</template>
<script>
    export default{
        data(){
            return {
                trans: {
                    title:        'Logout',
                    acceptLogout: 'Accept logout',
                    cancel:       'Cancel'
                },
                url:   'logout'
            }
        },
        beforeMount(){
            this.checkAuth();
        },
        mounted(){
            appFunc.console('Component Logout ready.');

            this.locale();
            this.$parent.hideLoader();
        },
        methods: {
            locale(){
                this.trans.title = this.$root.$refs.app.trans('interface.title.logout');
                this.trans.acceptLogout = this.$root.$refs.app.trans('interface.buttons.accept_logout');
                this.trans.cancel = this.$root.$refs.app.trans('interface.buttons.cancel');
            },
            /**
             * Редирект на главную страницу.
             */
            actionCancel(){
                router.push({
                    name: 'index'
                });
            },
            actionLogout(){
                this.$http.post(this.url).then(
                        function (response) {
                            appFunc.info(response.data.response, 'success');
                            window.location.href = '/';
                        }, function (response) {
                            appFunc.info(response.data.error, 'error');
                        }
                );
            },
            checkAuth(){
                var without = [
                    'logout'
                ];
                var isRouteAuth = without.indexOf(this.$route.name) !== -1;

                if (this.auth === '1' && !isRouteAuth) {
                    router.push({
                        name: 'index'
                    });
                }
            }
        }
    }
</script>
