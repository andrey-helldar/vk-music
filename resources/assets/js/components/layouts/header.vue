<template>
    <div v-cloak>
        <header>
            <div class="container">
                <h1>
                    <router-link :to="{name: 'index'}">
                        <img src="/images/logotype.svg" alt="" class="logotype">
                    </router-link>
                </h1>
            </div>
        </header>

        <div class="row blue lighten-2 white-text">
            <download-component ref="download"></download-component>
        </div>
    </div>
</template>
<script>
    import DownloadComponent from '../functions/downloads.vue'
    export default{
        data(){
            return {}
        },
        components: {
            DownloadComponent
        },
        mounted(){
            appFunc.console('Component Header ready.');
        }
    }
</script>
