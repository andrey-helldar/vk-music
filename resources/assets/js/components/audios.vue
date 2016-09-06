<template>
    <div class="row">
        <div class="col s12 m3" v-for="item in items">
            <ul class="audio">
                <li class="audio-title">
                    <ul>
                        <li>{{ item.artist.trim() }}</li>
                        <li>{{ item.title.trim() }}</li>
                        <li>{{ getGenre(item.genre_id) }}</li>
                    </ul>
                </li>

                <li class="audio-actions">
                    <ul>
                        <li class="audio-play">
                            <i class="material-icons waves-effect waves-light" @click="play(item)">play_arrow</i>
                        </li>
                        <li class="audio-duration">
                            {{ durationToHumans(item.duration) }}
                        </li>
                        <li class="audio-download">
                            <i class="material-icons" @click="download(item)">file_download</i>
                        </li>
                    </ul>
                </li>

            </ul>
        </div>
    </div>

    <div class="row" v-if="msg.show">
        <div class="white-text col s8 offset-s2 m4 offset-m4 center-align transition" :class="[msg.style]">
            <h5>{{ msg.text }}</h5>
            <h6>{{ durationToHumans(msg.time) }}</h6>
        </div>
    </div>
</template>

<script>
    export default {
        data(){
            return {
                items:   [],
                genres:  [],
                vk:      {
                    offset: 0
                },
                loading: {
                    wait: false
                },
                msg:     {
                    text:  'Check...',
                    style: 'yellow darken-2',
                    show:  true,
                    time:  0
                }
            }
        },
        ready() {
            app.console('Component Audios ready.');
        },
        asyncData(){
            this.setStatusTime();
            this.getGenres();
            this.getAudio();
        },
        methods: {
            /**
             * Загрузка списка аудио.
             */
            getAudio(){
                this.$http.post('/api/audios.user', {
                            offset: this.offset
                        }
                )
                        .then(function (response) {
                                    app.info(response.data.response, 'success');
                                    this.loading.wait = true;
                                    this.checkTimer();
                                }, function (response) {
                                    this.loading.wait = false;

                                    switch (response.data.error_code) {
                                        case 20:
                                            app.info(response.data.error, 'info');
                                            this.loading.wait = true;
                                            this.checkTimer();
                                            break;

                                        default:
                                            app.info(response.data.error, 'error');
                                    }
                                }
                        );
            },
            /**
             * Проверка выполненных запросов и вывод записей на экран.
             */
            getAudioLoaded(){
                this.setStatus('check');
                this.$http.get('/api/audios.user')
                        .then(function (response) {
                                    app.info(response.data.response.resolve, 'success');
                                    app.console(response.data, 'info');
                                    this.loading.wait = false;
                                    this.items = response.data.response.items;
                                    this.setStatus('hide');
                                }, function (response) {
                                    switch (response.status) {
                                        case 500:
                                            app.console(response.statusText, 'warning');
                                            break;

                                            // 304 Not Modified
                                        case 304:
                                            break;

                                            // 204 No Content
                                        case 204:
                                            break;

                                        default:
                                            app.console(response.status + ' ' + response.statusText, 'error');
                                    }
                                    this.setStatus('wait');
                                }
                        );
            },
            /**
             * Получение списка жанров.
             */
            getGenres(){
                this.$http.get('/api/audios.genres')
                        .then(function (response) {
                                    this.genres = app.toArray(response.data.response.genres);
                                }, function (response) {
                                    this.genres = [];
                                }
                        );
            },
            /**
             * Определение жанра для конкретного трека.
             */
            getGenre(genre_id){
                if (this.genres.length) {
                    return this.genres[genre_id];
                }

                return 'Unknown';
            },
            /**
             * Таймер проверки ответов.
             */
            checkTimer(){
                var parent = this;
                var checkAudio = setInterval(
                        function () {
                            if (parent.loading.wait === false) {
                                clearInterval(checkAudio);
                            }

                            parent.getAudioLoaded();
                        }, 3000, parent
                );
            },
            /**
             * Изменение визуального статуса выполнения.
             * @param {string} status
             */
            setStatus(status){
                switch (status) {
                    case 'check':
                        this.msg.show = true;
                        this.msg.text = 'Check...';
                        this.msg.style = 'yellow darken-2';
                        break;

                    case 'wait':
                        this.msg.show = true;
                        this.msg.text = 'Waiting...';
                        this.msg.style = 'blue';
                        break;

                    default:
                        clearInterval(this.msg.timer);
                        this.msg.show = false;
                        this.msg.time = 0;
                }
            },
            /**
             * Отображение секунд ожидания.
             */
            setStatusTime(){
                var parent = this;

                setInterval(function (parent) {
                    parent.msg.time++;
                }, 1000, parent);
            },
            play(item){
                app.console('Playing ' + item.title);
            },
            download(item){
                window.location.href = item.url;
            },
            /**
             * Обрабатываем секунды в человеко-понятный формат времени.
             *
             * @param duration
             * @returns {string}
             */
            durationToHumans(duration){
                var date = new Date(1970, 1, 1, 0, 0, duration);
                var hours = date.getHours();
                var minutes = date.getMinutes();
                var seconds = date.getSeconds();
                var exp = [];

                exp = this.pushDateArray(exp, hours);
                exp = this.pushDateArray(exp, minutes);
                exp = this.pushDateArray(exp, seconds);

                return exp.join(':');
            },
            /**
             * Добавляем ведущий ноль к числу.
             *
             * @param num
             * @returns {*}
             */
            numAddZero(num){
                if (num < 10) {
                    return '0' + num;
                }
                return num;
            },
            /**
             * Если число больше нуля - добавляем его к массиву.
             *
             * @param arr
             * @param num
             * @returns {*}
             */
            pushDateArray(arr, num){
                if (num > 0) {
                    num = this.numAddZero(num);
                    arr.push(num);
                }
                return arr;
            }
        }
    }
</script>
