<template>
    <div v-cloak>
        <div class="container">
            <div class="row">
                <div class="col s12 m4">
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

                    <ul class="collection">
                        <li class="collection-item avatar" v-if="items.length" v-for="item in filteredItems">
                            <img class="circle" alt="Avatar" v-bind:src="item.photo_50">
                            <span class="title">
                                    {{ item.first_name }}
                                    {{ item.last_name }}
                                </span>

                            <p>
                                <span class="green-text" v-if="item.online">Online</span>
                                <span class="grey-text" v-else>Offline</span>
                            </p>

                            <button class="btn-flat waves-effect waves-blue secondary-content" @click="getFriendAudios(item)">
                                <i class="material-icons">send</i>
                            </button>
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

                    <div class="col s12 m12 center-align" v-if="vk.offset < vk.count_all">
                        <button class="btn-flat waves-effect waves-blue tooltipped more-audio" data-position="top" data-tooltip="Give more friends"
                                @click="moreFriends">
                            <i class="material-icons">more_horiz</i>
                        </button>
                    </div>
                </div>

                <div class="col s12 m8">
                    <h3>
                        {{ selectedUserName }}
                    </h3>

                    <vue-audio ref="audio"></vue-audio>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import VueAudio from './functions/audio.vue'
    export default{
        data(){
            return {
                items:            [],
                vk:               {
                    offset:    0,
                    count_all: 0
                },
                loading:          {
                    wait:       false,
                    position:   '',
                    showLoader: true
                },
                url:              'friends.user',
                url_user:         'audio.user',
                selectedUserName: '...',
                selectedUserId:   0,
                filterKey:        ''
            }
        },
        components: {
            VueAudio
        },
        beforeMount(){
            this.$parent.checkAuth();
        },
        mounted(){
            this.getFriends();
            appFunc.console('Component Friends ready.');
        },
        watch:      {
            'loading': 'checkDataLoading'
        },
        computed:   {
            filteredItems(){
                var filterKey = this.filterKey.toLowerCase();

                return this.items.filter((item)=> {
                    if (filterKey.length == 0) {
                        return true;
                    }

                    return item.first_name.toLowerCase().indexOf(filterKey) > -1 || item.last_name.toLowerCase().indexOf(filterKey) > -1;
                });
            }
        },
        methods:    {
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
            /**
             * Получение списка контактов.
             */
            getFriends(){
                this.loading.showLoader = true;
                this.setStatus('send');

                this.$http.post(this.url, {
                    count:  100,
                    offset: this.vk.offset,
                    fields: 'photo_50'
                }).then(
                        (response)=> {
                            appFunc.info(response.data.response.resolve, 'success');
                            this.loading.wait = true;
                            this.loading.position = response.data.response.description;
                            this.checkTimer();
                        },
                        (response) => {
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
            getFriendsLoaded(){
                this.setStatus('check');

                this.$http.get(this.url)
                        .then(
                                (response) => {
                                    this.loading.wait = false;
                                    this.vk.offset += response.data.response.count_query;
                                    this.vk.count_all = response.data.response.count_all;
                                    this.items = this.items.concat(response.data.response.items);

                                    this.hideLoader();
                                    appFunc.info(response.data.response.resolve, 'success');
                                },
                                (response) => {
                                    switch (response.status) {

                                        case 502:
                                            appFunc.info(response.statusText + '<br>Reloading this page...', 'error');
                                            location.reload();
                                            break;

                                        case 500:
                                            appFunc.console(response.statusText, 'warning');
                                            break;

                                        case 406:
                                            this.loading.position = response.data.error.description;
                                            break;

                                        case 403:
                                            this.loading.wait = false;
                                            this.vk.offset = 0;
                                            this.vk.count_all = [];
                                            this.items = [];

                                            appFunc.info(response.data.error.resolve, 'error');
                                            this.hideLoader();
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
             * Таймер проверки ответов.
             */
            checkTimer(){
                var parent = this;
                var checkFriends = setInterval(() => {
                            if (parent.loading.wait === false) {
                                clearInterval(checkFriends);
                            } else {
                                parent.getFriendsLoaded();
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
                        notify(this, 'Check...', position, 'check');
                        break;

                    case 'wait':
                        notify(this, 'Please, wait...', position, 'wait', false);
                        break;

                    case 'send':
                        notify(this, 'Sending request...', '', 'info', false);
                        break;

                    default:
                        this.hideLoader();
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
                this.selectedUserName = item.first_name + ' ' + item.last_name;
                this.$refs.audio.load(this.url_user, item.id, 'user');

                return false;
            }
        }
    }
</script>
