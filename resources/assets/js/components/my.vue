<template>
    <div class="container">
        <h3>
            {{ locale.title }}
        </h3>

        <div class="row">
            <vue-audio ref="audio"></vue-audio>
        </div>
    </div>
</template>
<script>
    import VueAudio from './functions/audio.vue'
    export default{
        data(){
            return {
                locale: {
                    title: 'My audio'
                }
            }
        },
        components: {
            VueAudio
        },
        beforeMount(){
            this.$parent.checkAuth();
            this.locale();
        },
        mounted(){
            appFunc.console('Component My ready.');

            this.$refs.audio.load();
        },
        methods:    {
            locale(){
                this.locale.title = this.$root.$refs.app.trans('interface.title.my');
            }
        }
    }
</script>
