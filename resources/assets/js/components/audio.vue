<template>
    <h3>
        <i class="material-icons">audiotrack</i>
        {{ activePage.title }}
    </h3>

    <div class="input-field">
        <input id="search" type="search" required v-model="filterKey">
        <label for="search"><i class="material-icons">search</i></label>
        <i class="material-icons">close</i>
    </div>

    <div class="row">
        <div class="col s6 m4 l3" v-for="item in items | filterBy filterKey">
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
                            <span v-if="audio.index !== $index">{{ timeToHumans(item.duration) }}</span>
                            <span v-if="audio.index === $index">{{ timeToHumans(audio.currentTime) }}</span>
                        </li>
                        <li class="audio-download valign-wrapper">
                            <i class="material-icons waves-effect waves-light valign" @click="download(item)">file_download</i>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>

        <div class="col s12 m8 offset-m2 center-align" v-if="!items.length">
            <div class="card-panel materialize-red">
                <div class="white-text">
                    <h6>No audios</h6>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col s12 m12 center-align" v-if="vk.offset < vk.count_all">
            <a href="#!" class="btn-flat waves-effect waves-blue tooltipped more-audio" data-position="top" data-tooltip="Give more audio"
               @click="moreAudio">
                <i class="material-icons">more_horiz</i>
            </a>
        </div>
    </div>
</template>

<script>
    export default {
        data(){
            return {
                searchQuery: '',
                items:       [],
                genres:      {},
                vk:          {
                    offset:    0,
                    count_all: 0
                },
                loading:     {
                    wait:       false,
                    position:   '',
                    showLoader: true
                },
                audio:       {
                    player:      false,
                    index:       -1,
                    currentTime: 0,
                    duration:    0,
                    title:       '',
                    class:       'audio-playing',
                    buttons:     {
                        play:  'play_arrow',
                        pause: 'stop'
                    }
                },
                activePage:  {
                    title: 'Your audio',
                    url:   '/api/audio.user'
                }
            }
        },
        ready() {
            app.console('Component Audio ready.');
        },
        asyncData(){
            this.getGenres();
            this.getAudio(true);
        },
        watch:   {
            'items':   {
                handler: function (newValue, oldValue) {
                    this.$root.hideLoader();
                }
            },
            'loading': {
                handler: function (newValue, oldValue) {
                    if (this.loading.showLoader === true) {
                        this.$root.showLoader('Please, wait...', newValue.position);
                    }
                },
                deep:    true
            }
        },
        methods: {
            /**
             * Загрузка списка аудио.
             *
             * @param {bool}    enableLoader    Включить ли отображение лоадер скрина.
             * @param {Object}  postData        Передача дополнительных данных в запрос.
             */
            getAudio(enableLoader = false, postData = {}){
                this.loading.showLoader = enableLoader;

                this.setStatus('send');

                this.$http.post(this.activePage.url, Object.assign({
                            offset:     this.vk.offset,
                            owner_type: this.vk.owner_type,
                            owner_id:   this.vk.owner_id
                        }, postData)
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
                                            this.setStatus('hide');
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

                this.$http.get(this.activePage.url)
                        .then(function (response) {
                                    app.info(response.data.response.resolve, 'success');
                                    this.loading.wait = false;
                                    this.vk.offset += response.data.response.count_query;
                                    this.vk.count_all = response.data.response.count_all;
                                    this.items = this.items.concat(response.data.response.items);
                                }, function (response) {
                                    switch (response.status) {

                                        case 502:
                                            app.info(response.statusText + '<br>Reloading this page...', 'error');
                                            location.reload();
                                            break;

                                        case 500:
                                            app.console(response.statusText, 'warning');
                                            break;

                                        case 406:
                                            this.loading.position = response.data.error.description;
                                            break;

                                        case 403:
                                            app.info(response.data.error.resolve, 'error');
                                            this.loading.wait = false;
                                            this.vk.offset = 0;
                                            this.vk.count_all = [];
                                            this.items = [];
                                            return;

                                        case 401:
                                            app.console(response.statusText, 'info');
                                            location.href = '/';
                                            break;

                                            // 304 Not Modified
                                        case 304:
                                            this.loading.position = response.data.error.description;
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
             * Загрузка следующих аудиозаписей.
             */
            moreAudio(){
                this.loading.showLoader = false;
                this.getAudio();
            },
            /**
             * Получение списка жанров.
             */
            getGenres(){
                this.$http.get('/api/audio.genres')
                        .then(function (response) {
                                    this.genres = response.data.response.genres;
                                }, function (response) {
                                    this.genres = {};
                                }
                        );
            },
            /**
             * Определение жанра для конкретного трека.
             */
            getGenre(genre_id){
                if (this.genres[genre_id] !== undefined) {
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
                var position = this.loading.position;

                var notify = function (parent, text, description, style, showModal = true) {
                    if (parent.loading.showLoader === true) {
//                        if (showModal === true) {
                        parent.$root.showLoader(text, description, style);
//                        }
                    } else {
                        app.info(text, 'info', 1000);
                    }
                };

                switch (status) {
                    case 'check':
                        notify(this, 'Check...', position, 'check');
                        break;

                    case 'wait':
                        notify(this, 'Please, wait...', position, 'wait', false);
                        break;

                    case 'send':
                        notify(this, 'Sending request...', '', 'info', false);
                        break;

                    default:
                        this.$root.hideLoader();
                }
            },
            /**
             * Старт/стоп воспроизведения звука.
             */
            audioPlayOrPause(item, index){
                if (this.audio.index === -1) {
                    this.audioPlay(item, index);
                } else {
                    if (this.audio.index === index) {
                        this.audioPause();
                    } else {
                        this.audioPause();
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
                this.audio.player.volume = 1.0;
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
            audioPause(){
                if (this.audio.player !== false) {
                    app.info('Stopped: ' + this.audio.title);
                    this.backgroundColor(false);

                    this.audio.player.pause();

//                    if (this.audioVolume() === true) {
                    this.audio.player = false;
                    this.audio.index = -1;
                    this.audio.currentTime = 0;
                    this.audio.duration = 0;
                    this.audio.title = '';
//                    }
                }

                $('.audio').removeClass(this.audio.class);
                $('.audio .audio-actions .audio-play i').text(this.audio.buttons.play);
            },
            /**
             * Останавливаем воспроизведение аудио плавно.
             */
            audioVolume(){
                var parent = this;
                var audioSetVolume = setInterval(function () {
//                    app.console('Volume ' + parent.audio.player.volume);
                    var volume = parent.audio.player.volume;

                    if (volume <= 0 || volume === undefined) {
                        parent.audio.player.pause();
                        clearInterval(audioSetVolume);
                    } else {
                        parent.audio.player.volume -= 0.1;
                    }
                }, 200, parent);

                return true;
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
                            url:      item.url,
                            artist:   item.artist.trim(),
                            title:    item.title.trim(),
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
