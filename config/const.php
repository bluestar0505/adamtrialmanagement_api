<?php

return [
    'user_type' => [
        'buyer' => 1,
        'supplier' => 2,
        'system_admin' => 3,
        'normal_admin' => 4,
    ],

    'accept_status' => [
        'default' => 0,
        'accepted' => 1,
    ],

    'quote_status' => [
        'waiting' => 1,
        'has_quote' => 2,
        'selected' => 3,
    ],
    'quote_status_code' => [
        1 => '回答待ち',
        2 => '回答あり',
        3 => '選定済',
    ],


];
