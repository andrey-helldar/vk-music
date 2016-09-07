<?php

return [
    'api_version'      => 5.53,

    /**
     * Идентификатор Вашего приложения.
     */
    'client_id'        => 5614504,

    /**
     * Секретный ключ приложения.
     */
    'client_secret'    => 'FncWs3rbPCMmdTox9sg0',

    /**
     * Битовая маска настроек доступа приложения, которые необходимо проверить при авторизации пользователя и запросить отсутствующие.
     *
     * @see https://vk.com/dev/permissions
     */
    'scopes'           => 'audio,groups,+256',

    /**
     * Адрес, на который будет переадресован пользователь после прохождения авторизации.
     *
     * @see https://vk.com/dev/implicit_flow_user?f=redirect_uri
     */
    'redirect_uri'     => 'http://vk-music.dev/verify',

    /**
     * Указывает тип отображения страницы авторизации. Поддерживаются следующие варианты:
     *      page — форма авторизации в отдельном окне;
     *      popup — всплывающее окно;
     *      mobile — авторизация для мобильных устройств (без использования Javascript).
     *
     * Если пользователь авторизуется с мобильного устройства, будет использован тип mobile.
     */
    'display'          => 'page',

    /**
     * Тип ответа, который необходимо получить. Укажите token.
     */
    'response_type'    => 'code',

    /**
     * Параметр, указывающий, что необходимо не пропускать этап подтверждения прав, даже если пользователь уже авторизован.
     */
    'revoke'           => 0,

    /**
     * Максимально разрешенное количество запросов в секунду.
     */
    'rps'              => 3,

    /**
     * Срок жизни файла в минутах.
     */
    'files_expired_in' => 10,

    /**
     * Количество загружаемых записей.
     */
    'count_records'    => 50,
];