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
            {{ msg.text }}
        </div>
    </div>
</template>

<script>
    export default {
        data(){
            return {
                items  : [],
                genres : [],
                vk     : {
                    offset: 0
                },
                loading: {
                    wait: false
                },
                msg    : {
                    text : 'Check...',
                    style: 'yellow darken-2',
                    show : true
                }
            }
        },
        ready() {
            app.console('Component Audios ready.');
        },
        asyncData(){
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
                    .then(function (response)
                          {
                              app.info(response.data.response, 'success');
                              this.loading.wait = true;
                              this.checkTimer();
                          }, function (response)
                          {
                              this.loading.wait = false;
                              app.info(response.data.error, 'error');

                              if (response.data.error_code == 20) {
                                  this.loading.wait = true;
                                  this.checkTimer();
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
                    .then(function (response)
                          {
                              app.info(response.data.response.resolve, 'success');
                              app.console(response.data, 'info');
                              this.loading.wait = false;
                              this.items        = response.data.response.items;
                              this.setStatus('hide');
                          }, function (response)
                          {
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
                    .then(function (response)
                          {
                              this.genres = app.toArray(response.data.response.genres);
                          }, function (response)
                          {
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
                var parent     = this;
                var checkAudio = setInterval(
                        function ()
                        {
                            if (parent.loading.wait === false) {
                                app.console('loading.white == false', 'info')
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
                        this.msg.show  = true;
                        this.msg.text  = 'Check...';
                        this.msg.style = 'yellow darken-2';
                        break;

                    case 'wait':
                        this.msg.show  = true;
                        this.msg.text  = 'Waiting...';
                        this.msg.style = 'blue';
                        break;

                    default:
                        this.msg.show = false;
                }
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
                var exp     = duration / 60;
                var minutes = ~~exp;
                var seconds = duration - minutes * 60;

                return minutes + ':' + seconds;
            }
        }
    }
</script>
