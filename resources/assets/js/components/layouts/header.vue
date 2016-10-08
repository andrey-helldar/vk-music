<template>
    <div v-cloak>
        <header>
            <div class="container">
                <div class="row">
                    <div class="col s12 m9">
                        <h1>
                            <router-link :to="{name: 'index'}">
                                <img src="/images/logotype.svg" alt="" class="logotype">
                            </router-link>
                        </h1>
                    </div>

                    <div class="col s10 offset-s1 m3">
                        <download-component ref="download"></download-component>
                    </div>
                </div>
            </div>
        </header>
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
