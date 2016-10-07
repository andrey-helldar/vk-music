<template>
    <button class="badge new blue white-text waves-effect waves-light" @click="downloadModalOpen">
        {{ items.length }}
    </button>

    <!--start: Modal-->
    <div class="modal bottom-sheet" id="downloadModal">
        <div class="modal-content" v-if="items.length">
            <h5>
                In queue:
            </h5>

            <ul class="collection">
                <li class="collection-item avatar" v-for="item in items">
                    <i class="material-icons circle green">play_arrow</i>
                    <span class="title">
                        {{ item.artist }} - {{ item.title }}
                    </span>

                    <span class="badge" data-badge-caption="in queue position">
                        1
                    </span>

                    <span class="badge">
                        <i class="material-icons">send</i>
                    </span>
                </li>
            </ul>
        </div>

        <div class="modal-content valign-wrapper center-align" v-else>
            <h5 class="valign">
                No items
            </h5>
        </div>

        <div class="modal-footer">
            <button class="btn-flat modal-action modal-close waves-effect waves-green">
                Close
            </button>
        </div>
    </div>
    <!--end: Modal-->
</template>
<script>
    export default{
        data(){
            return {
                items: [],
                url:   'download'
            }
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
//                            this.checkTimer();
                        },
                        (response)=> {
                            switch (response.data.error_code) {
                                case 20:
                                    appFunc.info(response.data.error, 'info');
//                                    this.checkTimer();
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
                                    response.data.response.items.forEach((item)=> {
                                        this.downloadFile(item.url, item.title);
                                        this.deleteItemFromQueue(item.audios);
                                        appFunc.info(item.title, 'success');
                                    });
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
                var element = document.createElement('a');

                element.setAttribute('href', url);

                appFunc.console(element.getAttribute('href'));

                element.style.display = 'none';
                document.body.appendChild(element);

                element.click();

                document.body.removeChild(element);
            },
            /**
             * Таймер проверки ответов.
             * Пока присутствуют файлы в очереди - перебираем цикл.
             */
            checkTimer(){
                var parent = this;
                var checkFile = setInterval(() => {
                            if (parent.items.length > 0) {
                                parent.getDownloadLoaded();
                            } else {
                                clearInterval(checkFile);
                            }
                        },
                        3000, parent);
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
