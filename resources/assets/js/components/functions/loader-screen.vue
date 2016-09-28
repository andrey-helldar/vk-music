<template>
    <div class="loader-screen valign-wrapper" v-if="show" :transition="transitionName">
        <div class="row valign center-align loader-content">
            <h2 class="loader-text" :class="[style.selected]">{{ text }}</h2>

            <h5 class="loader-description" v-if="description.length">{{ description }}</h5>

            <h6 class="loader-duration">{{ timeToHumans(time) }}</h6>
        </div>
    </div>
</template>
<script>
    export default{
        data(){
            return {
                show:           true,
                transitionName: 'top',
                text:           'Loading...',
                description:    '',
                time:           0,
                style_type:     'wait',
                style:          {
                    selected: 'blue-text text-darken-2',
                    wait:     'blue-text text-darken-2',
                    check:    'amber-text text-darken-2'
                },
                timerInterval:  undefined
            }
        },
        ready() {
            // Запускаем отображение при инициализации компонента.
            this.showLoader();

            // Если на странице найден блок с определенным классом - вырубаем лоадер.
            this.hideOnClass('loader-screen-hide');

            appFunc.console('Component Loader Screen ready.');
        },
        watch:   {
            /**
             * Изменяем стиль элементов.
             */
            style_type: {
                handler: (newValue, oldValue) => {
                    var style = this.style[newValue];

                    if (style === undefined) {
                        style = this.style.wait;
                    }

                    this.style.selected = style;
                }
            },
            /**
             * Отслеживаем слишком долгие запросы.
             */
            time:       {
                handler: (newValue, oldValue) => {
                    if (newValue > 300) {
                        this.text = 'Whoops...';
                        this.description = 'Something went wrong. We reload the page ...';
                        location.reload();
                    }
                }
            }
        },
        methods: {
            /**
             * Запускаем лоадер.
             */
            showLoader(text = 'Loading...', description = '', style_type = 'wait'){
                this.style_type = style_type;
                this.text = this.checkTextLength(text, 'Loading...');
                this.description = this.checkTextLength(description);
                this.show = true;
                this.timer();
            },
            /**
             * Прячем лоадер.
             */
            hideLoader(){
                this.style_type = 'wait';
                this.show = false;
                this.text = 'Loading...';
                this.time = 0;
            },
            /**
             * Проверяем длину текста.
             */
            checkTextLength(text, defaultText = ''){
                if (text.length === 0 || text === undefined) {
                    if (defaultText.length !== 0) {
                        text = defaultText;
                    } else {
                        text = '';
                    }
                }

                return text;
            },
            /**
             * Таймер отсчета времени ожидания.
             */
            timer(){
                if (this.timerInterval !== undefined && this.show !== false) {
                    return;
                }

                var parent = this;
                this.timerInterval = setInterval(
                        () => {
                            if (parent.show === false) {
                                clearInterval(parent.timerInterval);
                                parent.timerInterval = undefined;
                            } else {
                                parent.time++;
                            }
                        }, 1000, parent
                );
            },
            /**
             * Переводим время в человеко-понятный формат.
             *
             * @param time
             * @returns {*|string}
             */
            timeToHumans(time){
                return appFunc.timeToHumans(time);
            },
            /**
             * Если на странице найден элемент с тегом "loader-screen-hide" - скрываем лоадер.
             */
            hideOnClass(codeClass){
                if ($('*').is('.' + codeClass)) {
                    // Создадим секундную видимость лоадера))
                    var parent = this;

                    setTimeout(() => {
                        parent.hideLoader();
                    }, 1000);
                }
            }
        }
    }
</script>
