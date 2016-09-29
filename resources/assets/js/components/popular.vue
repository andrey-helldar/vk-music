<template>
    <div class="container">
        <div class="row">
            <div class="col s12 m12">
                <h3>
                    {{ title }}
                </h3>
            </div>
        </div>
    </div>
</template>
<script>
    export default{
        data(){
            return {
                title: 'Popular'
            }
        },
        ready(){
            this.$parent.checkAuth();
            this.$parent.hideLoader();
            appFunc.console('Component Popular ready.');
        }
    }
</script>
