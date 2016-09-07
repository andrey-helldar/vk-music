<template>
    <div class="container">
        <form>
            <div class="input-field">
                <input id="search" type="search" placeholder="{{ placeholder }}" required>
                <label for="search"><i class="material-icons">search</i></label>
                <i class="material-icons">close</i>
            </div>
        </form>
    </div>
</template>
<script>
    export default{
        data(){
            return {
                placeholder: 'SEARCH'
            }
        }
    }
</script>
