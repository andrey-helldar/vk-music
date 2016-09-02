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
                items  : [],
                vk     : {
                    offset: 0
                },
                loading: {
                    wait : false,
                    count: 0
                }
            }
        },
        ready() {
            app.console('Component Audios ready.');
        },
        asyncData(){
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

                              if (response.data.error_code === 20) {
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
                this.$http.get('/api/audios.user')
                    .then(function (response)
                          {
                              app.info(response.data.response.resolve, 'success');
                              this.loading.wait = false;
                              this.vk.items     = response.data.response.items;
                          }, function (response)
                          {
                              this.loading.count++;
                          }
                    );
            },
            /**
             * Таймер проверки ответов.
             */
            checkTimer(){
                this.loading.count = 0;
                var parent         = this;
                var checkAudio     = setInterval(
                        function ()
                        {
                            if (parent.loading.wait === false) {
                                clearInterval(checkAudio);
                            }

                            // app.console('getAudioLoaded: ' + parent.loading.count);
                            parent.getAudioLoaded();
                        }, 1000, parent
                );
            },
        }
    }
</script>
