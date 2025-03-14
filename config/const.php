<?php

return [
    'user_type' => [
        'buyer' => 1,
        'supplier' => 2,
        'system_admin' => 3,
        //'normal_admin' => 4,
    ],
    'user_type_code' => [
        1 => '一般（バイヤー）',
        2 => 'サプライヤー',
        3 => 'システム管理者',
    ],

    'accept_status' => [
        'default' => 0,
        'accepted' => 1,
        'rejected' => 2,
        'returned' => 3,
    ],

    'request_status' => [
        'waiting' => 1,
        'has_quote' => 2,
        'selected' => 3,
    ],
    'request_status_code' => [
        1 => '回答待ち',
        2 => '回答あり',
        3 => '選定済',
    ],

    'quote_status' => [
        'limited' => 1,
        'waiting' => 2,
        'quoted' => 3,
        'accepted' => 4,
        'rejected' => 5,
        'returned' => 6,
    ],

    'quote_status_code' => [
        1 => '回答期限切れ',
        2 => '回答待ち',
        3 => '回答済み',
        4 => '採用',
        5 => '非採用',
        6 => '差し戻し済み',
    ],

    'quote_sent' => 1,

    'admin_quote_status' => [
        'waiting' => 2,
        'quoted' => 3,
        'rejected' => 4,
        'accepted' => 5,
        'returned' => 6,
    ],

    'admin_quote_status_code' => [
        2 => '回答待ち',
        3 => '選定待ち',
        4 => '非採用',
        5 => '選定済',
        6 => '差し戻し済み',
    ],


];
