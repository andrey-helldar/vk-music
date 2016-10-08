<?php

return [
    'api_version'           => 5.57,

    /**
     * Идентификатор Вашего приложения.
     */
    'client_id'             => 5614504,

    /**
     * Секретный ключ приложения.
     */
    'client_secret'         => 'FncWs3rbPCMmdTox9sg0',

    /**
     * Битовая маска настроек доступа приложения, которые необходимо проверить при авторизации пользователя и запросить отсутствующие.
     *
     * @see https://vk.com/dev/permissions
     */
    'scopes'                => 'offline,audio,groups,friends,email,+256',

    /**
     * Адрес, на который будет переадресован пользователь после прохождения авторизации.
     *
     * @see https://vk.com/dev/implicit_flow_user?f=redirect_uri
     */
    'redirect_uri'          => 'http://vk-music.dev/verify',

    /**
     * VK API.
     */
    'request_url'           => 'https://api.vk.com/method/',

    /**
     * Указывает тип отображения страницы авторизации. Поддерживаются следующие варианты:
     *      page — форма авторизации в отдельном окне;
     *      popup — всплывающее окно;
     *      mobile — авторизация для мобильных устройств (без использования Javascript).
     *
     * Если пользователь авторизуется с мобильного устройства, будет использован тип mobile.
     */
    'display'               => 'page',

    /**
     * Тип ответа, который необходимо получить. Укажите token.
     */
    'response_type'         => 'code',

    /**
     * Параметр, указывающий, что необходимо не пропускать этап подтверждения прав, даже если пользователь уже авторизован.
     */
    'revoke'                => 1,

    /**
     * Максимально разрешенное количество запросов в секунду.
     */
    'rps'                   => 3,

    /**
     * Коэффициент времени для расчета количества запросов в секунду.
     */
    'response_time_factor'  => 1.05,

    /**
     * Коэффициент для расчета допустимого количества запросов в минуту.
     * По-умолчанию, 0.9 - сокращение количества запросов на 10% от нормы.
     */
    'records_factor'        => 0.9,

    /**
     * Время жизни кэша с метками количества запросов в секунду и временем задержки.
     * По-умолчанию, 30 минут.
     */
    'cache_delay'           => 30,

    /**
     * Среднее время ответа платформы ВК.
     * Нужно для расчета допустимого количаства запросов в минуту.
     * Данное значение используется системой в самом начале, а в ходе работы
     * "учится" находить более оптимальное значение.
     */
    'average_response_time' => 50,

    /**
     * Срок жизни в минутах.
     * Применяется к файлам, записям БД и прочему.
     */
    'expired_in'            => 60,

    /**
     * Количество загружаемых записей.
     *
     * ВНИМАНИЕ!
     * При формировании запроса записей следует учитывать количество запрашиваемых данных.
     * Для симметричного отображения на экране пользователя, рекомендуется указывать количество
     * кратное четырем.
     */
    'count_records'         => 20,

    /**
     * В случае рекомендуемых и популярных аудиозаписей более мелкие параметры недостаточны.
     */
    'count_records_force'   => 100,
];