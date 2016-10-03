<template>
    <div v-cloak>
        <div class="container">
            <div class="row">
                <div class="col s12 m4">

                    <h3>
                        Groups
                        <sup class="grey-text text-lighten-2" v-if="items.length">
                            {{ items.length }} / {{ vk.count_all }}
                        </sup>
                    </h3>

                    <div class="input-field">
                        <input id="search" type="search" required v-model="filterKey">
                        <label for="search"><i class="material-icons">filter_list</i></label>
                        <i class="material-icons">close</i>
                    </div>

                    <div class="row">
                        <div class="col s12 m12">
                            <ul class="collection">

                                <li class="collection-item avatar" v-if="items.length" v-for="item in filteredItems">
                                    <img class="circle" alt="Avatar" v-bind:src="item.photo_50">
                                    <span class="title">
                        {{ item.name }}
                    </span>

                                    <a class="secondary-content" href="#!" @click="getGroupAudios(item)">
                                        <i class="material-icons">send</i>
                                    </a>
                                </li>

                                <li class="collection-item avatar" v-if="!items.length">
                                    <i class="material-icons circle">account_circle</i>
                                    <span class="title">No groups</span>
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

                        <div class="col s12 m12 center-align" v-if="vk.offset < vk.count_all">
                            <a href="#!" class="btn-flat waves-effect waves-blue tooltipped more-audio" data-position="top"
                               data-tooltip="Give more groups"
                               @click="moreGroups">
                                <i class="material-icons">more_horiz</i>
                            </a>
                        </div>
                    </div>

                </div>

                <div class="col s12 m8">
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
                url:            'groups.user',
                selectedUserId: 0,
                filterKey:      ''
            }
        },
        components: {
            VueAudio
        },
        mounted(){
            this.$parent.checkAuth();
            this.getGroups();
            appFunc.console('Component Groups ready.');
        },
        watch:      {
            'items':   {
                handler: (newValue, oldValue) => {
                    this.$parent.hideLoader();
                }
            },
            'loading': {
                handler: (newValue, oldValue) => {
                    if (this.loading.showLoader === true) {
                        this.$parent.showLoader('Please, wait...', newValue.position);
                    }
                },
                deep:    true
            }
        },
        computed:   {
            filteredItems: function () {
                return this.items.filter(function (item) {
                    return item.name.indexOf(this.filterKey)
                })
            }
        },
        methods:    {
            /**
             * Получение списка контактов.
             */
            getGroups(){
                this.$http.post(this.url, {
                            count:  100,
                            offset: this.vk.offset,
                            fields: 'photo_50'
                        }
                )
                        .then(
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
            getGroupsLoaded(){
                this.setStatus('check');

                this.$http.get(this.url)
                        .then(
                                (response) => {
                                    appFunc.info(response.data.response.resolve, 'success');
                                    this.loading.wait = false;
                                    this.vk.offset += response.data.response.count_query;
                                    this.vk.count_all = response.data.response.count_all;
                                    this.items = this.items.concat(response.data.response.items);
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
                var checkAudio = setInterval(
                        () => {
                            if (parent.loading.wait === false) {
                                clearInterval(checkAudio);
                            } else {
                                parent.getGroupsLoaded();
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

                var notify = (parent, text, description, style, showModal = true) => {
                    if (parent.loading.showLoader === true) {
//                        if (showModal === true) {
                        parent.$parent.showLoader(text, description, style);
//                        }
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
                        this.$parent.hideLoader();
                }
            },
            /**
             * Загрузка следующих друзей.
             */
            moreGroups(){
                this.loading.showLoader = false;
                this.getGroups();
            },
            /**
             * Загружаем аудио.
             *
             * @param item
             */
            getGroupAudios(item){
                this.$parent.loadAudios('audio.user', item.name, item.id, 'group');
            }
        }
    }
</script>
