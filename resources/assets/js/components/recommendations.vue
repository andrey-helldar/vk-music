<template>
    <div v-cloak>
        <div class="container">
            <h3>
                {{ title }}
            </h3>

            <div class="row">
                <vue-audio ref:audio></vue-audio>
            </div>
        </div>
    </div>
</template>
<script>
    export default{
        data(){
            return {
                title: 'Recommendations'
            }
        },
        mounted(){
            this.$parent.checkAuth();
            this.$parent.hideLoader();
            appFunc.console('Component Recommendations ready.');
        }
    }
</script>
