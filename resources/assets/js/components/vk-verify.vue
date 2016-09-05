<template>
    <div class="center-align">
        <h3>Need access verification</h3>

        <button class="btn btn-large btn-primary waves-effect waves-light" disabled>
            <i class="material-icons left">check</i>
            Checking access token...
        </button>
    </div>
</template>
<script>
    export default{
        data(){
            return {}
        },
        ready() {
            app.console('Component VK Verify ready.');
            this.vkVerify();
        },
        methods: {
            vkVerify(){
                app.info('Verifying access token...', 'info');

                var
                        uri    = window.location.href,
                        params = this.parseUri(uri);

                this.$http.post('/api/vk.verify', params)
                    .then(function (response)
                          {
                              app.info('Access token verified succesfully!', 'success');
                              window.location.href = '/';
                          }, function (response)
                          {
                              app.info('Error loading parameters. Please, reload this page.', 'error');
                          }
                    );
            },

            /**
             * Разбираем строку, извлекая параметры в объект.
             *
             * @param uri
             * @returns {{}}
             */
            parseUri(uri){
                var
                        symbol = this.uriSymbol(uri),
                        url    = uri.split(symbol),
                        params = [],
                        obj    = {};

                if (url[1].indexOf('&') !== false) {
                    params = url[1].split('&');
                } else {
                    params = url[1];
                }

                params.forEach(function (item)
                               {
                                   var param     = item.split('=');
                                   obj[param[0]] = param[1];
                               }
                );

                return obj;
            },

            /**
             * Символ-разделитель.
             *
             * @param uri
             * @returns {*}
             */
            uriSymbol(uri){
                if (uri.indexOf('#') !== false) {
                    return '#';
                }

                return '?';
            }
        }
    }
</script>
