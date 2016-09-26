<?php

return [
    [
        'is_active'  => true,
        'route'      => 'my',
        'api'        => '/api/audio.user',
        'title'      => 'My Audio',
        'show_title' => true,
        'panel'      => [
            'is_show'     => true,
            'icon'        => 'audiotrack',
            'description' => 'It is a new text',
        ],
    ],
    [
        'is_active'  => true,
        'route'      => 'recommendations',
        'api'        => '/api/audio.recommendations',
        'title'      => 'Recommendations',
        'show_title' => true,
        'panel'      => [
            'is_show'     => true,
            'icon'        => 'thumb_up',
            'description' => 'It is a new text',
        ],
    ],
    [
        'is_active'  => true,
        'route'      => 'popular',
        'api'        => '/api/audio.popular',
        'title'      => 'Popular',
        'show_title' => true,
        'panel'      => [
            'is_show'     => true,
            'icon'        => 'trending_up',
            'description' => 'It is a new text',
        ],
    ],
    [
        'is_active'  => true,
        'route'      => 'friends',
        'title'      => 'Friends',
        'show_title' => true,
        'panel'      => [
            'is_show'     => true,
            'icon'        => 'sentiment_satisfied',
            'description' => 'It is a new text',
        ],
    ],
    [
        'is_active'  => true,
        'route'      => 'groups',
        'title'      => 'Groups',
        'show_title' => true,
        'panel'      => [
            'is_show'     => true,
            'icon'        => 'group',
            'description' => 'It is a new text',
        ],
    ],
    [
        'is_active'  => true,
        'route'      => 'search',
        'icon'       => 'search',
        'title'      => 'Search',
        'show_title' => false,
        'panel'      => [
            'is_show'     => true,
            'icon'        => 'search',
            'description' => 'It is a new text',
        ],
    ],
];