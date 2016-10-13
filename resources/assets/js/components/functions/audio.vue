<template>
    <div v-cloak>
        <div class="input-field">
            <input id="search" type="search" required v-model="filterKey"/>
            <label for="search"><i class="material-icons">filter_list</i></label>
            <i class="material-icons">close</i>
        </div>

        <div class="row">
            <div class="col s6 m4 l3" v-for="(item, index) in filteredItems">
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
                                <i class="material-icons waves-effect waves-light valign" @click="audioPlayOrPause(item, index)">play_arrow</i>
                            </li>
                            <li class="audio-duration">
                                <span v-if="audio.index !== index">{{ timeToHumans(item.duration) }}</span>
                                <span v-if="audio.index === index">{{ timeToHumans(audio.currentTime) }}</span>
                            </li>
                            <li class="audio-download valign-wrapper">
                                <i class="material-icons waves-effect-waves-light valign tooltipped" @click="addMyAudio(item)" v-bind:data-tooltip="locale.addMyAudios">add</i>
                                <i class="material-icons waves-effect waves-light valign tooltipped" @click="download(item)" v-bind:data-tooltip="locale.downloadThisTrack">file_download</i>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>

            <div class="col s12 m8 offset-m2 center-align" v-if="!items.length">
                <div class="card-panel blue lighten-1">
                    <div class="white-text">
                        <h6>
                            {{ locale.noAudios }}
                        </h6>
                    </div>
                </div>
            </div>

            <div class="col s12 m12 center-align" v-if="items.length < vk.count_all">
                <button class="btn-flat waves-effect waves-blue tooltipped more-audio" data-position="top" v-bind:data-tooltip="locale.giveMore" @click="moreAudio">
                    <i class="material-icons">more_horiz</i>
                </button>
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        data(){
            return {
                items:             [],
                genres:            {},
                vk:                {
                    offset:    0,
                    count_all: 0
                },
                loading:           {
                    wait:       false,
                    position:   '',
                    showLoader: true
                },
                audio:             {
                    player:            false,
                    index:             -1,
                    currentTime:       0,
                    maxSecondsPlaying: 60,
                    duration:          0,
                    title:             '',
                    class:             'audio-playing',
                    buttons:           {
                        play:  'play_arrow',
                        pause: 'stop'
                    }
                },
                locale:            {
                    noAudios:          'No audios',
                    addMyAudios:       'Add to my audios',
                    downloadThisTrack: 'Download this track',
                    reloadingPage:     '<br>Reloading this page...',
                    check:             'Check...',
                    pleaseWait:        'Please, wait...',
                    sendingRequest:    'Sending request...'
                },
                url:               'audio.user',
                default_url:       'audio.user',
                filterKey:         '',
                searchPlaceholder: 'Filter',
                statusCode:        200
            }
        },
        mounted() {
            appFunc.console('Component Audio ready.');

            this.getGenres();
            this.locale();
        },
        watch:    {
            'loading': 'checkDataLoading'
        },
        computed: {
            filteredItems(){
                var filterKey = this.filterKey.toLowerCase();

                return this.items.filter((item)=> {
                    if (filterKey.length == 0) {
                        return true;
                    }

                    return item.artist.toLowerCase().indexOf(filterKey) > -1 || item.title.toLowerCase().indexOf(filterKey) > -1;
                });
            }
        },
        beforeDestroy(){
            this.audioPause();
        },
        methods:  {
            locale(){
                this.locale.noAudios = this.$root.$refs.app.trans('interface.statuses.no_audio');
                this.locale.addMyAudios = this.$root.$refs.app.trans('interface.buttons.add_my_audios');
                this.locale.downloadThisTrack = this.$root.$refs.app.trans('interface.buttons.download_this_track');
                this.locale.giveMore = this.$root.$refs.app.trans('interface.buttons.give_more');
                this.locale.reloadingPage = this.$root.$refs.app.trans('interface.actions.reloading_page');
                this.locale.check = this.$root.$refs.app.trans('interface.statuses.check');
                this.locale.pleaseWait = this.$root.$refs.app.trans('interface.statuses.please_wait');
                this.locale.sendingRequest = this.$root.$refs.app.trans('interface.statuses.sending_request');
            },
            hideLoader(){
                this.$root.$refs.app.hideLoader();
            },
            /**
             * Проверка статуса изменения отображения окна.
             */
            checkDataLoading(newValue, oldValue){
                if (this.loading.showLoader === true) {
                    this.$root.$refs.app.showLoader('Please, wait...', newValue.position);
                }
            },
            checkUrl(){
                if (!this.url.length || this.url === undefined) {
                    this.url = this.default_url;
                }
            },
            /**
             * Передача параметра на загрузку аудио.
             * Необходимо при работе с некоторыми страницами.
             *
             * @param url
             * @param title
             * @param owner_id
             * @param owner_type
             * @param postData
             */
            load(url = '', owner_id = 0, owner_type = 'default', postData = {}){
                this.url = url;
                this.vk.count_all = 0;
                this.vk.offset = 0;
                this.vk.owner_id = owner_id;
                this.vk.owner_type = owner_type;
                this.items = [];

                this.getAudio(true, postData);
            },
            /**
             * Загрузка списка аудио.
             *
             * @param {bool}    enableLoader    Включить ли отображение лоадер скрина.
             * @param {Object}  postData        Передача дополнительных данных в запрос.
             */
            getAudio(enableLoader = false, postData = {}){
                this.loading.showLoader = enableLoader;

                this.checkUrl();
                this.setStatus('send');

                this.$http.post(this.url, Object.assign({
                    offset:     this.vk.offset,
                    owner_type: this.vk.owner_type,
                    owner_id:   this.vk.owner_id
                }, postData))
                        .then(
                                (response)=> {
                                    appFunc.info(response.data.response.resolve, 'success');
                                    this.loading.wait = true;
                                    this.loading.position = response.data.response.description;
                                    this.checkTimer();

                                    appFunc.console(response.status);
                                },
                                (response)=> {
                                    this.loading.wait = false;

                                    switch (response.data.error_code) {
                                        case 20:
                                            appFunc.info(response.data.error, 'info');
                                            this.loading.wait = true;
                                            this.checkTimer();
                                            break;

                                        default:
                                            this.setStatus('hide');
                                            appFunc.info(response.data.error, 'error');
                                    }
                                }
                        );
            },
            /**
             * Проверка выполненных запросов и вывод записей на экран.
             */
            getAudioLoaded(){
                this.checkUrl();
                this.setStatus('check');

                this.$http.get(this.url)
                        .then(
                                (response) => {
                                    this.loading.wait = false;
                                    this.vk.offset += response.data.response.count_query;
                                    this.vk.count_all = response.data.response.count_all;
                                    this.items = this.items.concat(response.data.response.items);

                                    this.initTooltip();
                                    this.hideLoader();
                                    appFunc.info(response.data.response.resolve, 'success');
                                },
                                (response)=> {
                                    switch (response.status) {

                                        case 502:
                                            appFunc.info(response.statusText + this.locale.reloadingPage, 'error');
                                            location.reload();
                                            break;

                                        case 500:
                                            appFunc.info(response.statusText, 'error');
                                            break;

                                        case 406:
                                            this.loading.position = response.data.error.description;
                                            break;

                                        case 403:
                                            this.loading.wait = false;
                                            this.vk.offset = 0;
                                            this.vk.count_all = [];
                                            this.items = [];

                                            this.initTooltip();
                                            this.hideLoader();
                                            appFunc.info(response.data.error.resolve, 'error');
                                            return;

                                        case 401:
                                            appFunc.console(response.statusText, 'info');
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
                                            appFunc.console(response.status + ' ' + response.statusText, 'error');
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
                this.$http.get('audio.genres')
                        .then(
                                (response) => {
                                    this.genres = response.data.response.genres;
                                },
                                ()=> {
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
                var checkAudio = setInterval(() => {
                            if (parent.loading.wait === false) {
                                clearInterval(checkAudio);
                            } else {
                                parent.getAudioLoaded();
                            }
                        },
                        3000, parent);
            },
            /**
             * Изменение визуального статуса выполнения.
             * @param {string} status
             */
            setStatus(status){
                var position = this.loading.position;

                var notify = (parent, text, description, style, showModal = true) => {
                    if (parent.loading.showLoader === true) {
                        parent.$root.$refs.app.showLoader(text, description, style);
                    } else {
                        appFunc.info(text, 'info', 1000);
                    }
                };

                switch (status) {
                    case 'check':
                        notify(this, this.locale.check, position, 'check');
                        break;

                    case 'wait':
                        notify(this, this.locale.pleaseWait, position, 'wait', false);
                        break;

                    case 'send':
                        notify(this, this.locale.sendingRequest, '', 'info', false);
                        break;

                    default:
                        this.hideLoader();
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

                appFunc.info('Playing: ' + this.audio.title);

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
            audioPause(){
                if (this.audio.player !== false) {
                    appFunc.info('Stopped: ' + this.audio.title);

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
            onTimeUpdateListener(player){
                var currentTime = parseInt(player.currentTime);

                this.audio.currentTime = currentTime;
                this.backgroundColor();

                if (currentTime > this.audio.maxSecondsPlaying) {
                    this.audioPause();
                }
            },
            /**
             * Транслируем глобальную функцию преобразования времени.
             *
             * @param time
             * @returns {*|string}
             */
            timeToHumans(time){
                return appFunc.timeToHumans(time);
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
            },
            /**
             * Копирует аудиозапись на страницу пользователя или группы.
             *
             * @param item
             */
            addMyAudio(item){
                this.$http.post('audio.add', {
                    audio_id: item.id,
                    owner_id: item.owner_id
                }).then(
                        (response) => {
                            appFunc.info(response.data.response.resolve, 'success');
                        }
                );
            },
            /**
             * Передача запроса на скачивание файла.
             *
             * @param item
             */
            download(item){
                this.$root.$refs.app.$refs.header.$refs.download.getDownload(item);
            },
            /**
             * Инициализируем тултипы.
             */
            initTooltip()
            {
                $(document).ready(function () {
                    $('.tooltipped').tooltip('remove');
                    $('.tooltipped').tooltip({
                        delay:    50,
                        position: 'top'
                    });
                });
            }
        }
    }
</script>
