<template>
    <div v-cloak>
        <div class="container">
            <div class="center-align">
                <h3>Need access verification</h3>

                <button class="btn btn-large btn-primary waves-effect waves-light" disabled>
                    <i class="material-icons left">check</i>
                    {{ buttonText }}
                </button>
            </div>
        </div>
    </div>
</template>
<script>
    export default{
        data(){
            return {
                buttonText: 'Checking access token'
            }
        },
        beforeMount(){
            this.$parent.checkAuth();
        },
        mounted() {
            this.dotButton();
            this.checkGetParams();
            appFunc.console('Component VK Verify ready.');
        },
        methods: {
            /**
             * Основная функция верификации.
             */
            vkVerify(){
                appFunc.info('Verifying access token...');

                var
                        uri    = window.location.href,
                        params = this.parseUri(uri);

                this.$http.post('vk.verify', params)
                        .then((response) => {
                                    appFunc.info(response.data.response, 'success');
                                    window.location.href = '/';
                                }, (response) => {
                                    appFunc.info(response.data.error, 'error');
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

                if (url[1].indexOf('&') !== -1) {
                    params = url[1].split('&');

                    params.forEach((item) => {
                                var param = item.split('=');
                                obj[param[0]] = param[1];
                            }
                    );
                } else {
                    params = url[1].split('=');
                    obj[params[0]] = params[1];
                }

                return obj;
            },

            /**
             * Символ-разделитель.
             *
             * @param uri
             * @returns {*}
             */
            uriSymbol(uri){
                if (uri.indexOf('#') !== -1) {
                    return '#';
                }

                return '?';
            },

            /**
             * Проверка параметров запроса.
             */
            checkGetParams(){
                var
                        uri     = window.location.href,
                        success = uri.indexOf('#') > -1 || uri.indexOf('?') > -1;

                if (success === true) {
                    this.vkVerify();
                } else {
                    window.location.href = '/';
                }
            },

            /**
             * Вывод "бегающих" точек на кнопке статуса.
             */
            dotButton(){
                var parent = this;
                var text = this.buttonText;
                var dotted = '.';

                setInterval(() => {
                    if (dotted.length > 3) {
                        dotted = '.';
                    }

                    parent.buttonText = text + dotted;
                    dotted += '.';
                }, 1000, text, dotted, parent);
            }
        }
    }
</script>
