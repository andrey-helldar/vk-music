<template>
    <div class="container">
        <h3>
            Errors!
        </h3>

        <ul class="collection red-text text-darken-2">
            <li class="collection-item">{{ errorDescription }}</li>
        </ul>

        <div class="row center-align">
            <button class="btn waves-effect waves-light" @click="redirectToAuth">
                <i class="material-icons left">undo</i>
                Return to auth page
            </button>
        </div>
    </div>
</template>
<script>
    export default{
        data(){
            return {
                errorDescription: ''
            }
        },
        beforeMount(){
            this.checkAuth();
            this.checkVkErrors();
        },
        mounted(){
            appFunc.console('Component Error ready.');
            this.$parent.hideLoader();
        },
        methods: {
            /**
             * Проверяем авторизацию.
             * Страница доступна только при отсутствии авторизации.
             */
            checkAuth(){
                if (this.$parent.errorDescription.length > 0) {
                    return;
                }

                if (isAuth === '1') {
                    router.push({
                        name: 'index'
                    });
                }
            },
            /**
             * Проверка наличия переданной ошибки.
             * Если ошибка не передана - производим редирект на главную страницу.
             */
            checkVkErrors() {
                this.errorDescription = this.$parent.errorDescription;

                if (this.$parent.isError === false || this.errorDescription.length === 0) {
                    window.location.href = '/';
                    return;
                }
            },
            /**
             * Редирект на страницу авторизации.
             */
            redirectToAuth(){
                window.location.href = '/auth';
            }
        }
    }
</script>
