<template>
    <div class="container">
        <h3>
            {{ locale.title }}
        </h3>

        <div class="row center-align">
            <button class="btn btn-large btn-primary waves-effect waves-light" @click="vkAuth">
                <i class="material-icons left">account_circle</i>
                {{ locale.authVk }}
            </button>
        </div>
    </div>
</template>
<script>
    export default{
        data(){
            return {
                api_uri: 'https://oauth.vk.com/authorize?',
                vk:      {
                    client_id:     0,
                    redirect_uri:  '/',
                    display:       'page',
                    scope:         'offline,audio',
                    response_type: 'code',
                    v:             5.53
                },
                locale:  {
                    title:              'Need authorization in VK',
                    authVk:             'Auth VK',
                    errorLoadingParams: 'Error loading parameters. Please, reload this page.'
                }
            }
        },
        beforeMount(){
            this.$parent.checkAuth();
            this.locale();
        },
        mounted() {
            appFunc.console('Component VK Auth ready.');

            this.getVkParams();
        },
        methods: {
            locale(){
                this.locale.title = this.$root.$refs.app.trans('interface.title.need_auth_vk');
                this.locale.authVk = this.$root.$refs.app.trans('interface.buttons.auth_vk');
                this.locale.errorLoadingParams = this.$root.$refs.app.trans('interface.statuses.error_loading_params');
            },
            getVkParams(){
                this.$http.get('vk.params')
                        .then(function (response) {
                                    this.vk = response.data.response;
                                    this.$parent.hideLoader();
                                }, function (response) {
                                    this.vk.client_id = 0;
                                    this.$parent.hideLoader();
                                    appFunc.info(this.locale.errorLoadingParams, 'error');
                                }
                        );
            },
            vkAuth(){
                var query = appFunc.build_query(this.vk);

                window.location.href = this.api_uri + query;
            }
        }
    }
</script>
