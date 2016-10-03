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
                title: 'Recommendations'
            }
        },
        components: {
            VueAudio
        },
        mounted(){
            this.$parent.checkAuth();
            this.$parent.hideLoader();
            appFunc.console('Component Recommendations ready.');
        }
    }
</script>
