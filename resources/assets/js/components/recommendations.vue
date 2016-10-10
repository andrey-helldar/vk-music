<template>
    <div class="container">
        <h3>
            {{ title }}
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
                title: 'Recommendations'
            }
        },
        components: {
            VueAudio
        },
        beforeMount(){
            this.$parent.checkAuth();
        },
        mounted(){
            appFunc.console('Component Recommendations ready.');
            this.$refs.audio.load('audio.recommendations');
        }
    }
</script>
