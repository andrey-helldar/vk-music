<template>
    <footer class="page-footer">
        <div class="container">
            <div class="row">
                <div class="col l6 s12">
                    <h5 class="white-text">{{ trans('title') }}</h5>
                    <p class="grey-text text-lighten-4">{{ trans('description') }}</p>
                </div>
                <div class="col l4 offset-l2 s12">
                    <h5 class="white-text">{{ trans('links') }}</h5>
                    <ul>
                        <li v-for="link in links">
                            <a class="grey-text text-lighten-3" v-bind:link="link.url">
                                {{ link.title }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="footer-copyright">
            <div class="container">
                © {{ year }} {{ trans('title') }}

                <a class="grey-text text-lighten-4 right" href="#">
                    {{ trans('to_top') }}
                </a>
            </div>
        </div>
    </footer>
</template>
<script>
    export default{
        data(){
            return {
                start_year: 2016,
                year:       2016,
                links:      []
            }
        },
        beforeMount(){
            this.year();
            this.footerLinks();
        },
        mounted(){
            appFunc.console('Component Footer ready.');
        },
        methods: {
            footerLinks(){
                this.$http.get('footer.links').then(
                        function (response) {
                            if (response.data.error == undefined) {
                                this.links = response.data.response;
                            } else {
                                appFunc.info(response.data.error, 'error');
                            }
                        }, function (response) {
                            appFunc.info(response.data.error, 'error');
                        }
                );
            },
            /**
             * Вывод года
             */
            year(){
                var now = new Date();

                if (now.getFullYear() > this.start_year) {
                    this.year = this.start_year + '-' + now.getFullYear();
                } else {
                    this.year = this.start_year;
                }
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

                return this.$parent.trans('interface.site.' + param);
            }
        }
    }
</script>
