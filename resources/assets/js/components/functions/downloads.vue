<template>
    <div v-cloak>
        <div class="card-panel" v-if="items.length">
            <h5>
                {{ locale.title }}
            </h5>

            <div class="row center-align">
                <button class="btn waves-effect waves-light hoverable" @click="downloadModalOpen">
                    {{ items.length }}
                    {{ locale.inQueue }}
                </button>
            </div>
        </div>


        <!--start: Modal-->
        <div class="modal bottom-sheet black-text" id="downloadModal">
            <div class="modal-content" v-if="items.length">
                <h5>
                    {{ locale.inQueue.toUppercase() }}
                </h5>

                <ul class="collection">
                    <li class="collection-item avatar" v-for="(item, index) in items">
                        <i class="material-icons circle green">play_arrow</i>
                        <span class="title">
                        {{ item.artist }} - {{ item.title }}
                    </span>

                        <span class="badge" v-bind:data-badge-caption="locale.inQueuePosition">
                            {{ index + 1 }}
                        </span>
                    </li>
                </ul>
            </div>

            <div class="modal-content valign-wrapper center-align" v-if="!items.length">
                <h5 class="valign">
                    {{ locale.noItems }}
                </h5>
            </div>

            <div class="modal-footer">
                <button class="btn red lighten-2 modal-action modal-close waves-effect waves-light">
                    {{ locale.close }}
                </button>
            </div>
        </div>
        <!--end: Modal-->
    </div>
</template>
<script>
    export default{
        data(){
            return {
                items:  [],
                url:    'download',
                locale: {
                    title:           'Downloads',
                    inQueue:         'in queue',
                    inQueuePosition: 'in queue position',
                    noItems:         'No items',
                    close:           'Close'
                }
            }
        },
        beforeMount(){
            this.checkDownloadedFiles();
            this.locale();
        },
        mounted(){
            appFunc.console('Component Downloads ready.');
        },
        methods: {
            locale(){
                this.locale.title = this.$root.$refs.app.trans('interface.title.downloads');
                this.locale.inQueue = this.$root.$refs.app.trans('interface.statuses.in_queue');
                this.locale.inQueuePosition = this.$root.$refs.app.trans('interface.statuses.in_queue_position');
                this.locale.noItems = this.$root.$refs.app.trans('interface.statuses.no_items');
                this.locale.close = this.$root.$refs.app.trans('interface.buttons.close');
            },
            /**
             * Создание запроса на скачивание файла.
             */
            getDownload(item) {
                var title = item.artist.trim() + ' - ' + item.title.trim();
                appFunc.info('Preparing to download:<br>' + title, 'info');

                var item = {
                    id:       item.id,
                    artist:   item.artist.trim(),
                    title:    item.title.trim(),
                    duration: item.duration,
                    owner_id: item.owner_id.toString(),
                    audios:   item.owner_id.toString() + '_' + item.id
                };

                this.$http.post('download', item).then(
                        (response)=> {
                            this.items.push(item);
                            appFunc.info(response.data.response.resolve, 'success');
                        },
                        (response)=> {
                            switch (response.data.error_code) {
                                case 20:
                                    appFunc.info(response.data.error, 'info');
                                    break;

                                default:
                                    appFunc.info(response.data.error, 'error');
                            }
                        }
                );
            },
            /**
             * Проверка выполненных запросов и вывод записей на экран.
             */
            getDownloadLoaded(){
                this.$http.get(this.url)
                        .then(
                                (response) => {
                                    /**
                                     * Если файлы присутствуют в базе - проверяем их.
                                     */
                                    if (response.data.response.items.length) {
                                        response.data.response.items.forEach((item)=> {
                                            appFunc.info(item.title, 'success');
                                            this.deleteItemFromQueue(item.audios);
                                            this.downloadFile(item.url, item.title);
                                        });
                                    }
                                },
                                (response)=> {
                                    switch (response.status) {

                                        case 502:
                                            appFunc.info(response.statusText + '<br>Reloading this page...', 'error');
                                            location.reload();
                                            break;

                                        case 500:
                                            appFunc.info(response.statusText, 'error');
                                            break;

                                        case 406:
                                            this.loading.position = response.data.error.description;
                                            break;

                                        case 403:
                                            appFunc.info(response.data.error.resolve, 'error');
                                            return;

                                        case 401:
                                            appFunc.console(response.statusText, 'info');
                                            location.href = '/';
                                            break;

                                            // 304 Not Modified
                                        case 304:
                                            break;

                                            // 204 No Content
                                        case 204:
                                            break;

                                        default:
                                            appFunc.console(response.status + ' ' + response.statusText, 'error');
                                    }
                                }
                        );
            },
            /**
             * Непосредственно загрузка файла.
             */
            downloadFile(url, title){
                var id = url.replace(/:|\/|\./gi, '-');
                $('body').append('<iframe style="display: none;" src="' + url + '" id="' + id + '"></iframe>');

                setTimeout(()=> {
                    $('#' + id).remove();
                }, 10000, id);
            },
            /**
             * Таймер проверки скачанных файлов.
             * Так как пользователь может перезагрузить браузер, а записи в базе отображаться - выводим их.
             */
            checkDownloadedFiles(){
                var parent = this;
                setInterval(() => {
                            parent.getDownloadLoaded();
                        },
                        5000, parent);
            },
            /**
             * Удаляем файл из очереди скачивания.
             */
            deleteItemFromQueue(key){
                var parent = this;

                this.items.forEach((item)=> {
                    var el = item.audios.indexOf(key);

                    if (el != -1) {
                        parent.items.splice(el, 1);
                    }
                });
            },
            /**
             * Открываем список скачиваемых треков.
             */
            downloadModalOpen(){
                $('#downloadModal').openModal();
            }
        }
    }
</script>
