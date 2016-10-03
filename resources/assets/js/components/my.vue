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
                title: 'My audio'
            }
        },
        components: {
            VueAudio
        },
        mounted(){
            this.$parent.checkAuth();
            this.$refs.audio.load();
//            this.$parent.hideLoader();
            appFunc.console('Component My ready.');
        }
    }
</script>
