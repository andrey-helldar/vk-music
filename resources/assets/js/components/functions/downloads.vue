<template>
    <div class="container">
        Items: <strong>{{ items.length }}</strong>
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

                this.$http.post('download', {
                            id:       item.id,
                            artist:   item.artist.trim(),
                            title:    item.title.trim(),
                            duration: item.duration,
                            owner_id: item.owner_id.toString(),
                            audios:   item.owner_id.toString() + '_' + item.id
                        }
                ).then(
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
            }
        }
    }
</script>
