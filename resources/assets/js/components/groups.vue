<template>
    <h3>
        Friends
        <sup class="grey-text text-lighten-2" v-if="items.length">
            {{ items.length }} / {{ vk.count_all }}
        </sup>
    </h3>

    <div class="input-field">
        <input id="search" type="search" required v-model="filterKey">
        <label for="search"><i class="material-icons">search</i></label>
        <i class="material-icons">close</i>
    </div>

    <div class="row">
        <div class="col s12 m12">
            <ul class="collection">

                <li class="collection-item avatar" v-if="items.length" v-for="item in items | filterBy filterKey">
                    <img class="circle" alt="Avatar" v-bind:src="item.photo_50">
                    <span class="title">
                        {{ item.first_name }}
                        {{ item.last_name }}
                    </span>

                    <p>
                        <span class="green-text" v-if="item.online">Online</span>
                        <span class="grey-text" v-else>Offline</span>
                    </p>

                    <a class="secondary-content" href="#!" @click="getFriendAudios(item)">
                        <i class="material-icons">send</i>
                    </a>
                </li>

                <li class="collection-item avatar" v-if="!items.length">
                    <i class="material-icons circle">account_circle</i>
                    <span class="title">No friends</span>
                    <p>
                        ...no audios...<br>
                        ...no actions...
                    </p>

                    <a class="secondary-content disabled">
                        <i class="material-icons grey-text">clear</i>
                    </a>
                </li>

            </ul>
        </div>

        <div class="row">
            <div class="col s12 m12 center-align" v-if="vk.offset < vk.count_all">
                <a href="#!" class="btn-flat waves-effect waves-blue tooltipped more-audio" data-position="top" data-tooltip="Give more friends"
                   @click="moreFriends">
                    <i class="material-icons">more_horiz</i>
                </a>
            </div>
        </div>
    </div>
</template>
<script>
    export default{
        data(){
            return {
                items:          [],
                loading:        {
                    wait:       false,
                    position:   '',
                    showLoader: true
                },
                vk:             {
                    offset:    0,
                    count_all: 0
                },
                url:            '/api/friends.get',
                selectedUserId: 0
            }
        },
        ready(){
            app.console('Component Friends ready.');
        },
        asyncData(){
            this.getFriends();
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
             * Получение списка контактов.
             */
            getFriends(){
                this.$http.post(this.url, {
                            count:  100,
                            offset: this.vk.offset,
                            fields: 'photo_50'
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
                                            this.setStatus('hide');
                                            app.info(response.data.error, 'error');
                                    }
                                }
                        );
            },
            /**
             * Проверка выполненных запросов и вывод записей на экран.
             */
            getFriendsLoaded(){
                this.setStatus('check');

                this.$http.get(this.url)
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
             * Таймер проверки ответов.
             */
            checkTimer(){
                var parent = this;
                var checkAudio = setInterval(
                        function () {
                            if (parent.loading.wait === false) {
                                clearInterval(checkAudio);
                            } else {
                                parent.getFriendsLoaded();
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
             * Загрузка следующих друзей.
             */
            moreFriends(){
                this.loading.showLoader = false;
                this.getFriends();
            },
            /**
             * Загружаем аудио.
             *
             * @param item
             */
            getFriendAudios(item){
                this.$root.$refs.loaderScreen.showLoader();
                var audio = this.$root.$refs.audio;

                audio.activePage.title = 'Audios: ' + item.first_name + ' ' + item.last_name;
                audio.activePage.url = '/api/audio.user';
                audio.vk.count_all = 0;
                audio.vk.offset = 0;
                audio.vk.owner_id = item.id;
                audio.vk.owner_type = 'user';
                audio.items = [];

                audio.getAudio(true);
            },
        }
    }
</script>
