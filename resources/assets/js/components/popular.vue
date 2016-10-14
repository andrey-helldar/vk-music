<template>
    <div class="container">
        <h3>
            {{ trans.title }}
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
                trans: {
                    title: 'Popular'
                },
                url:   'audio.popular'
            }
        },
        components: {
            VueAudio
        },
        beforeMount(){
            this.$parent.checkAuth();
        },
        mounted(){
            appFunc.console('Component Popular ready.');

            this.locale();
            this.$refs.audio.load(this.url);
        },
        methods:    {
            locale(){
                this.trans.title = this.$root.$refs.app.trans('interface.title.popular');
            }
        }
    }
</script>
