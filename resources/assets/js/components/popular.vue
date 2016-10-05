<template>
    <div v-cloak>
        <div class="container">
            <h3>
                {{ title }}
            </h3>

            <div class="row">
                <vue-audio ref="audio"></vue-audio>
            </div>
        </div>
    </div>
</template>
<script>
    import VueAudio from './functions/audio.vue'
    export default{
        data(){
            return {
                title: 'Popular'
            }
        },
        components: {
            VueAudio
        },
        mounted(){
            this.$parent.checkAuth();
            this.$parent.hideLoader();
            this.$refs.audio.load('audio.popular');
            appFunc.console('Component Popular ready.');
        }
    }
</script>
