<template>
    <div class="container">
        <h3>
            {{ title }}
        </h3>

        <div class="row">
            <vue-audio v-ref:audio></vue-audio>
        </div>
    </div>
</template>
<script>
    import VueAudio from './functions/audio.vue'
    export default{
        data(){
            return {
                title: 'My audio'
            }
        },
        components: {
            VueAudio
        },
        ready(){
            this.$parent.checkAuth();
            this.$refs.audio.load();
//            this.$parent.hideLoader();
            appFunc.console('Component My ready.');
        }
    }
</script>
