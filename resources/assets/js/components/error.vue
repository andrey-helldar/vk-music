<template>
    <div v-cloak>
        <div>
            <h3>Errors!</h3>

            <ul class="collection red-text text-darken-2">
                <li>{{ errorDescription }}</li>
            </ul>
        </div>
    </div>
</template>
<script>
    export default{
        data(){
            return {
                isError:          false,
                errorDescription: ''
            }
        },
        beforeMount(){
            this.checkVkErrors();
        },
        methods: {
            checkVkErrors() {
                if (this.$parent.isError === false) {
                    window.location.href = '/';
                    return;
                }

                this.isError = this.$parent.isError;
                this.errorDescription = this.$parent.errorDescription;
            }
        }
    }
</script>
