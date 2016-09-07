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
                    <div class="progress">
                        <div class="determinate"></div>
                    </div>

                    <ul>
                        <li class="audio-play valign-wrapper">
                            <i class="material-icons waves-effect waves-light valign" @click="audioPlayOrPause(item, $index)">play_arrow</i>
                        </li>
                        <li class="audio-duration">
                            <span>{{ timeToHumans(item.duration) }}</span>
                            <span v-if="audio.index === $index">/ {{ timeToHumans(audio.currentTime) }}</span>
                        </li>
                        <li class="audio-download valign-wrapper">
                            <i class="material-icons waves-effect waves-light valign" @click="download(item)">file_download</i>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>

    <div class="row" v-if="msg.show">
        <div class="white-text col s8 offset-s2 m4 offset-m4 center-align transition" :class="[msg.style]">
            <h2>{{ msg.text }}</h2>
            <h5 v-if="msg.description.length">{{ msg.description }}</h5>
            <h6>{{ timeToHumans(msg.time) }}</h6>
        </div>
    </div>
</template>

<script>
    export default {
        data(){
            return {
                items: [],
                genres: [],
                vk: {
                    offset: 0
                },
                loading: {
                    wait: false,
                    position: ''
                },
                msg: {
                    text: 'Check...',
                    description: '',
                    style: 'yellow darken-2',
                    show: true,
                    time: 0
                },
                audio: {
                    player: false,
                    index: -1,
                    currentTime: 0,
                    duration: 0,
                    title: '',
                    class: 'audio-playing',
                    buttons: {
                        play: 'play_arrow',
                        pause: 'stop'
                    }
                },
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
                            offset: this.vk.offset
                        }
                )
                        .then(function (response) {
                                    app.info(response.data.response.resolve, 'success');
                                    this.loading.wait = true;
                                    this.loading.position = response.data.response.description;
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
                                    this.loading.wait = false;
                                    this.items = response.data.response.items;
                                    this.setStatus('hide');
                                }, function (response) {
                                    switch (response.status) {

                                        case 502:
                                            app.info(response.statusText + '<br>Reloading this page...', 'error');
                                            window.location.reload();
                                            break;

                                        case 500:
                                            app.console(response.statusText, 'warning');
                                            break;

                                        case 406:
                                            this.msg.description = response.data.error.description;
                                            break;

                                        case 401:
                                            app.console(response.statusText, 'info');
                                            window.location.href = '/';
                                            break;

                                            // 304 Not Modified
                                        case 304:
                                            this.loading.position = response.data.response.description;
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
                            } else {
                                parent.getAudioLoaded();
                            }
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
                        }, 1000, parent
                );
            },
            /**
             * Старт/стоп воспроизведения звука.
             */
            audioPlayOrPause(item, index){
                if (this.audio.index === -1) {
                    this.audioPlay(item, index);
                } else {
                    if (this.audio.index === index) {
                        this.audioPause(index);
                    } else {
                        this.audioPause(index);
                        this.audioPlay(item, index);
                    }
                }
            },
            /**
             * Начало воспроизведения звука.
             */
            audioPlay(item, index){
                this.audio.index = index;
                this.audio.currentTime = 0;
                this.audio.duration = item.duration;
                this.audio.title = item.artist.trim() + ' - ' + item.title.trim();

                app.info('Playing: ' + this.audio.title);

                this.audio.player = new Audio(item.url);
                this.audio.player.play();

                var parent = this;

                this.audio.player.ontimeupdate = function () {
                    parent.onTimeUpdateListener(this);
                };

                this.audio.player.onpause = function () {
                    if (parent.audio.player.ended === true) {
                        parent.audioPause();
                    }
                };

                var elemAudio = $('.audio:eq(' + index + ')');
                var elemAudioIcon = elemAudio.find('.audio-actions .audio-play i');

                elemAudio.addClass(this.audio.class);
                elemAudioIcon.text(this.audio.buttons.pause);
            },
            /**
             * Остановка воспроизведения.
             */
            audioPause(index){
                if (this.audio.player !== false) {
                    app.info('Stopped: ' + this.audio.title);
                    this.backgroundColor(false);

                    this.audio.player.pause();

                    this.audio.player = false;
                    this.audio.index = -1;
                    this.audio.currentTime = 0;
                    this.audio.duration = 0;
                    this.audio.title = '';
                }

                $('.audio').removeClass(this.audio.class);
                $('.audio .audio-actions .audio-play i').text(this.audio.buttons.play);
            },
            /**
             * Обновление времени проигрываемого трека.
             */
            onTimeUpdateListener: function (player) {
                var currentTime = parseInt(player.currentTime);

                this.audio.currentTime = currentTime;
                this.backgroundColor();

                if (currentTime > 30) {
                    this.audioPause();
                }
            },
            /**
             * Основная форма загрузки файла с системой кэширования.
             */
            download(item){
                var title = item.artist.trim() + ' - ' + item.title.trim();
                app.info('Preparing to download:<br>' + title, 'info');

                this.$http.post('/api/download', {
                            url: item.url,
                            artist: item.artist.trim(),
                            title: item.title.trim(),
                            duration: item.duration,
                            owner_id: item.owner_id
                        }
                )
                        .then(function (response) {
                                    app.info(response.data.response.resolve, 'success');
                                    this.downloadFile(response.data.response.url, response.data.response.title);
                                }, function (response) {
                                    app.console(response.data);
                                }
                        );
            },
            /**
             * Непосредственно загрузка файла.
             */
            downloadFile(url, title){
                var element = document.createElement('a');

                element.setAttribute('href', url);

                app.console(element.getAttribute('href'));

                element.style.display = 'none';
                document.body.appendChild(element);

                element.click();

                document.body.removeChild(element);
            },
            /**
             * Транслируем глобальную функцию преобразования времени.
             *
             * @param time
             * @returns {*|string}
             */
            timeToHumans(time){
                return app.timeToHumans(time);
            },
            /**
             * Изменение размера бэкграунда.
             *
             * @param colorize
             */
            backgroundColor(colorize = true){
                var progressBar = $('.audio-playing .progress .determinate');

                if (colorize === false) {
                    progressBar.css({
                        width: '0%'
                    });
                    return;
                }

                var duration = this.audio.duration;
                var currentTime = this.audio.currentTime;
                var width = (currentTime / duration) * 100;

                if (width > 100) {
                    width = 100;
                }

                progressBar.css({
                    width: width + '%'
                });
            }
        }
    }
</script>
