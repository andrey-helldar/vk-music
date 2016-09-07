<template>
    <div class="loader-screen valign-wrapper" v-if="show" :transition="fade" :transition-mode="out-in">
        <div class="row valign center-align loader-content" :class="[style.selected]">
            <h2>{{ text }}</h2>

            <h4 v-if="description.length">{{ description }}</h4>

            <h6>{{ timeToHumans(time) }}</h6>
        </div>
    </div>
</template>
<script>
    export default{
        data(){
            return {
                show:        false,
                text:        'Loading...',
                description: 'test',
                time:        0,
                style:       {
                    selected: 'blue-text text-darken-1',
                    wait:     'blue-text text-darken-1',
                    check:    'yellow-text text-darken-2'
                }
            }
        },
        ready() {
            app.console('Component Loader Screen ready.');
        },
        asyncData(){
//            this.showLoader();
        },
        methods: {
            /**
             * Запускаем лоадер.
             */
            showLoader(){
                this.show = true;
                this.timer();
            },
            /**
             * Прячем лоадер.
             */
            hideLoader(){
                this.show = false;
                this.text = 'Loading...';
                this.time = 0;
            },
            /**
             * Таймер отсчета времени ожидания.
             */
            timer(){
                var parent = this;

                var timerInterval = setInterval(
                        function () {
                            if (parent.show === false) {
                                clearInterval(timerInterval);
                            } else {
                                parent.time++;
                            }
                        }, 1000, parent
                );
            },
            timeToHumans(time){
                return app.timeToHumans(time);
            }
        }
    }
</script>
