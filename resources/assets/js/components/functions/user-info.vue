<template>
    <h1 class="right-align">
        <img class="circle" v-bind:src="photo" v-if="photo">
    </h1>
</template>
<script>
    export default{
        data(){
            return {
                photo: ''
            }
        },
        mounted(){
            appFunc.console('Component User Info ready.');

            this.getUserInfo();
        },
        methods: {
            /**
             * Получение информации о пользователе.
             */
            getUserInfo(){
                var parent = this;

                var intervalPhoto = setInterval(()=> {
                    var photo = parent.$root.$refs.app.user.info.photo;

                    if (photo !== undefined) {
                        parent.photo = photo;

                        if (photo.length) {
                            clearInterval(intervalPhoto);
                        }
                    }
                }, 500, parent);
            }
        }
    }
</script>
