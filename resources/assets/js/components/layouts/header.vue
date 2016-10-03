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
    </div>
</template>
<script>
    export default{
        data(){
            return {}
        }
    }
</script>
