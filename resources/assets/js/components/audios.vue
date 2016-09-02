<template>
    <div class="audio">
        <div class="audio-action">
            <i class="material-icons">close</i>
        </div>
        <div class="audio-title">
            Author - Track 1
        </div>
        <ul class="audio-actions">
            <li>
                <i class="material-icons">close</i>
            </li>
            <li>3:57</li>
        </ul>
    </div>
</template>

<script>
    export default {
        data(){
            return {
                tracks: []
            }
        },
        ready() {
            app.console('Component Audios ready.');
        },
        asyncData(){
            //
        },
        methods: {}
    }
</script>
