<template>
    <h3>
        Search
    </h3>

    <form class="row" name="search" v-on:submit.prevent="searching">
        <div class="input-field col s12">
            <input id="search" type="search" name="q" length="255" class="character-counter" required>
            <label for="search"><i class="material-icons">search</i></label>
            <i class="material-icons">close</i>
        </div>
    </form>
</template>
<script>
    export default{
        data(){
            return {
                url:   '/api/audio.search',
                title: 'Audios'
            }
        },
        ready(){
            app.console('Component Search ready.');
        },
        methods: {
            searching(){
                var form = $('form[name=search]');
                var query = form.find('input[name=q]').val();
                var parent = this;

                app.info('Searching:<br>"' + query + '"...');

                this.$root.loadAudios(parent.url, parent.title, 0, 'default', {
                    q: query
                });

                return false;
            }
        }
    }
</script>
