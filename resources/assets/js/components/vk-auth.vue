<template>
    <div class="center-align">
        <h3>Need authorization in VK</h3>

        <button class="btn btn-large btn-primary waves-effect waves-light" @click="vkAuth">
            <i class="material-icons left">account_circle</i>
            Auth VK
        </button>
    </div>
</template>
<script>
    export default{
        data(){
            return {
                vk: {
                    client_id:     0,
                    redirect_uri:  '/',
                    display:       'page',
                    scope:         'offline,audio',
                    response_type: 'code',
                    v:             5.53
                }
            }
        },
        ready() {
            this.getVkParams();
            appFunc.console('Component VK Auth ready.');
        },
        watch:   {
            'vk.client_id': {
                handler: function (newValue, oldValue) {
                    this.$parent.hideLoader();
                },
                deep:    true
            }
        },
        methods: {
            getVkParams(){
                this.$http.get('/api/vk.params')
                        .then(function (response) {
                                    this.vk = response.data.response;
                                }, function (response) {
                                    this.vk.client_id = 0;
                                    appFunc.info('Error loading parameters. Please, reload this page.', 'error');
                                }
                        );
            },
            vkAuth(){
                var query = appFunc.build_query(this.vk);

                window.location.href = 'https://oauth.vk.com/authorize?' + query;
            }
        }
    }
</script>
