<template>
    <div v-cloak>
        <div class="card-panel" v-if="items.length">
            <h5>Downloads</h5>

            <div class="row center-align">
                <button class="btn waves-effect waves-light hoverable" @click="downloadModalOpen">
                    {{ items.length }} in queue
                </button>
            </div>
        </div>


        <!--start: Modal-->
        <div class="modal bottom-sheet black-text" id="downloadModal">
            <div class="modal-content" v-if="items.length">
                <h5>
                    In queue:
                </h5>

                <ul class="collection">
                    <li class="collection-item avatar" v-for="(item, index) in items">
                        <i class="material-icons circle green">play_arrow</i>
                        <span class="title">
                        {{ item.artist }} - {{ item.title }}
                    </span>

                        <span class="badge" data-badge-caption="in queue position">
                            {{ index + 1 }}
                        </span>
                    </li>
                </ul>
            </div>

            <div class="modal-content valign-wrapper center-align" v-if="!items.length">
                <h5 class="valign">
                    No items
                </h5>
            </div>

            <div class="modal-footer">
                <button class="btn red lighten-2 modal-action modal-close waves-effect waves-light">
                    Close
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
                items: [],
                url:   'download'
            }
        },
        beforeMount(){
            this.checkDownloadedFiles();
        },
        mounted(){
            appFunc.console('Component Downloads ready.');
        },
        methods: {
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
                                    // TODO: Если в базе есть записи, а пользователь перезапустил браузер - заполнить список файлов на скачивание
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
                                            appFunc.console(response.statusText, 'warning');
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
