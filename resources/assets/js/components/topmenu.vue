<template>
    <nav>
        <div class="container">
            <div class="nav-wrapper">
                <a href="#" data-activates="mobile-demo" class="button-collapse"><i class="material-icons">menu</i></a>

                <ul class="right hide-on-med-and-down">
                    <li v-for="item in items" :class="{active: item.is_active}">
                        <a href="{{ item.url }}">
                            {{ item.title }}
                        </a>
                    </li>
                </ul>

                <ul class="side-nav" id="mobile-demo">
                    <li v-for="item in items" :class="{active: item.is_active}">
                        <a href="{{ item.url }}">
                            {{ item.title }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</template>

<script>
    export default {
        data(){
            return {
                items: []
            }
        },
        ready() {
            app.console('Component Topmenu ready.');
        },
        asyncData(){
            this.getTopmenu();
        },
        methods: {
            /**
             * Получение списка меню.
             */
            getTopmenu(){
                this.$http.get('/api/topmenu').then(
                        function (response) {
                            if (response.data.error == undefined) {
                                this.items = response.data.response;
                                this.setTopMenuActiveDefault();
                            } else {
                                app.info(response.data.error, 'error');
                            }
                        }, function (response) {
                            app.info(response.data.error, 'error');
                        }
                );
            },
            /**
             * Проверка активности элементов.
             *
             * @returns {boolean}
             */
            checkActivity(){
                var isActive = false;

                this.items.forEach(
                        function (item) {
                            if (item.is_active == true) {
                                isActive = true;
                            }
                        }
                );

                return isActive;
            },
            /**
             * Установка дефолтного значения активности элемента меню.
             */
            setTopMenuActiveDefault(){
                var isActive = this.checkActivity();

                if (isActive === false && this.items.length) {
                    this.items[0].is_active = true;
                }
            }
        }
    }
</script>
