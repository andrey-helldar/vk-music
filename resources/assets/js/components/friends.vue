<template>
    <div class="container">
        <div class="row">
            <div class="col s12 m4">
                <h3>
                    {{ trans.title }}

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

                    <div class="col s2 margin-bottom-10" v-for="item in filteredItems">
                        <img class="circle responsive-img z-depth-1 waves-effect waves-light tooltipped" v-bind:src="item.photo_50" v-bind:alt="getUserName(item)"
                             @click="getFriendAudios(item)" v-bind:data-tooltip="getUserName(item)">
                    </div>

                    <div class="col s12 center-align" v-if="!items.length">
                        <div class="card-panel blue lighten-1">
                            <div class="white-text">
                                <h6>
                                    {{ trans.noFriends }}
                                </h6>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col s12 m12 center-align" v-if="items.length < vk.count_all">
                    <button class="btn-flat waves-effect waves-blue tooltipped more-audio" data-position="top" v-bind:data-tooltip="trans.giveMore" @click="moreFriends">
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
                trans:            {
                    title:          'Friends',
                    noFriends:      'Friends not found',
                    giveMore:       'Give more',
                    pleaseWait:     'Please, wait...',
                    reloadingPage:  '<br>Reloading this page...',
                    check:          'Check...',
                    sendingRequest: 'Sending request...'
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
            appFunc.console('Component Friends ready.');

            this.locale();
            this.getFriends();
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
            locale(){
                this.trans.title = this.$root.$refs.app.trans('interface.title.friends');
                this.trans.noFriends = this.$root.$refs.app.trans('interface.statuses.no_friends');
                this.trans.giveMore = this.$root.$refs.app.trans('interface.buttons.give_more');
                this.trans.reloadingPage = this.$root.$refs.app.trans('interface.statuses.reloading_page');
                this.trans.check = this.$root.$refs.app.trans('interface.statuses.check');
                this.trans.pleaseWait = this.$root.$refs.app.trans('interface.statuses.please_wait');
                this.trans.sendingRequest = this.$root.$refs.app.trans('interface.statuses.sending_request');
            },
            hideLoader(){
                this.$root.$refs.app.hideLoader();
            },
            /**
             * Проверка статуса изменения отображения окна.
             */
            checkDataLoading(newValue, oldValue){
                if (this.loading.showLoader === true) {
                    this.$root.$refs.app.showLoader(this.trans.pleaseWait, newValue.position);
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
                        (response) => {
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
            getFriendsLoaded() {
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
                                (response) => {
                                    switch (response.status) {

                                        case 502:
                                            appFunc.info(response.statusText + this.trans.reloadingPage, 'error');
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
            checkTimer()            {
                var parent = this;
                var checkFriends = setInterval(() => {
                            if (parent.loading.wait === false) {
                                clearInterval(checkFriends);
                            }
                            else {
                                parent.getFriendsLoaded();
                            }
                        },
                        3000, parent
                );
            },
            /**
             * Изменение визуального статуса выполнения.
             * @param {string} status
             */
            setStatus(status)            {
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
                        notify(this, this.trans.check, position, 'check');
                        break;

                    case 'wait':
                        notify(this, this.trans.pleaseWait, position, 'wait', false);
                        break;

                    case 'send':
                        notify(this, this.trans.sendingRequest, '', 'info', false);
                        break;

                    default:
                        this.hideLoader();
                }
            },
            /**
             * Загрузка следующих друзей.
             */
            moreFriends()
            {
                this.loading.showLoader = false;
                this.getFriends();
            },
            /**
             * Загружаем аудио.
             *
             * @param item
             */
            getFriendAudios(item) {
                this.selectedUserName = item.first_name + ' ' + item.last_name;
                this.$refs.audio.load(this.url_user, item.id, 'user');

                return false;
            },
            /**
             * Инициализируем тултипы.
             */
            initTooltip()
            {
                // TODO Исправить отображение имени контакта.
                $(document).ready(function () {
                    $('.tooltipped').tooltip('remove');
                    $('.tooltipped').tooltip({delay: 50});
                });
            },
            /**
             * Получение имени пользователя.
             *
             * @param item
             * @returns {string}
             */
            getUserName(item){
                return item.first_name + ' ' + item.last_name;
            }
        }
    }
</script>
