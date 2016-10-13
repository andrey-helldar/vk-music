<template>
    <div>
        <div class="container">
            <h3>
                {{ title }}
            </h3>
        </div>

        <nav :class="statusColors.selected">
            <div class="nav-wrapper">
                <div class="container">
                    <form name="search" v-on:submit.prevent="searching">
                        <div class="input-field">
                            <input id="search" type="search" name="q" length="255" class="character-counter" required v-bind:placeholder="title.toUpperCase()">
                            <label for="search"><i class="material-icons">search</i></label>
                            <i class="material-icons">close</i>
                        </div>
                    </form>
                </div>
            </div>
        </nav>

        <div class="container">
            <vue-audio ref="audio"></vue-audio>
        </div>
    </div>
</template>
<script>
    import VueAudio from './functions/audio.vue'
    export default{
        data(){
            return {
                url:          'audio.search',
                title:        'Search',
                statusColors: {
                    selected: '',
                    default:  '',
                    success:  '',
                    error:    'red darken-1'
                }
            }
        },
        components: {
            VueAudio
        },
        beforeMount(){
            this.$parent.checkAuth();
        },
        mounted(){
            appFunc.console('Component Search ready.');
            this.$parent.hideLoader();
        },
        methods:    {
            searching(){
                var form = $('form[name=search]');
                var query = form.find('input[name=q]').val();
                var parent = this;

                appFunc.info('Searching:<br>"' + query + '"...');

                // TODO Визуально вывести статус подключения к VK API в виде изменения цвета фона строки поиска.
                this.$refs.audio.load(parent.url, 0, 'default', {
                    q: query
                });

                return false;
            },
            /**
             * Установка фонового цвета строки поиска в зависимости от полученного результата.
             *
             * @param status
             */
            setNavStatusColor(status = 'default'){
                var selected = '';

                switch (status) {
                    case 'error':
                        selected = this.statusColors.error;
                        break;

                    case 'success':
                        selected = this.statusColors.default;
                        break;

                    default:
                        selected = this.statusColors.default;
                }

                this.statusColors.selected = selected;
            }
        }
    }
</script>
