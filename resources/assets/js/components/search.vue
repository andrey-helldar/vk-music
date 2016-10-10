<template>
    <div>
        <div class="container">
            <h3>
                {{ title }}
            </h3>
        </div>

        <nav>
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
                url:   'audio.search',
                title: 'Search'
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

                this.$refs.audio.load(parent.url, 0, 'default', {
                    q: query
                });

                return false;
            }
        }
    }
</script>
