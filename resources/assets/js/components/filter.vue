<template>
    <form>
        <div class="input-field">
            <input id="search" type="search" required v-model="searchQuery">
            <label for="search"><i class="material-icons">search</i></label>
            <i class="material-icons">close</i>
        </div>
    </form>
</template>
<script>
    export default{
        data(){
            return {
                searchQuery: ''
            }
        },
    }
</script>
