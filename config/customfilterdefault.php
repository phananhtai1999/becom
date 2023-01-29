<?php

return [
    'email' => [
        'label' => 'email',
        'operate' => [
            '=',
            '!=',
            'like',
        ],
        'type' => 'string',
        'value' => null
    ],
    'first_name' => [
        'label' => 'first_name',
        'operate' => [
            '=',
            '!=',
            'like',
        ],
        'type' => 'string',
        'value' => null
    ],
    'last_name' => [
        'label' => 'last_name',
        'operate' => [
            '=',
            '!=',
            'like',
        ],
        'type' => 'string',
        'value' => null
    ],
    'middle_name' => [
        'label' => 'middle_name',
        'operate' => [
            '=',
            '!=',
            'like',
            'empty',
            'not empty'
        ],
        'type' => 'string',
        'value' => null
    ],
    'points' => [
        'label' => 'points',
        'operate' => [
            '=',
            '!=',
            'like',
            '>',
            '<',
            '>=',
            '<='
        ],
        'type' => 'numeric',
        'value' => null
    ],
    'phone' => [
        'label' => 'phone',
        'operate' => [
            '=',
            '!=',
            'like',
            'empty',
            'not empty'
        ],
        'type' => 'string',
        'value' => null
    ],
    'sex' => [
        'label' => 'sex',
        'operate' => [
            '=',
            '!=',
            'like',
            'empty',
            'not empty'
        ],
        'type' => 'string',
        'value' => null
    ],
    'dob' => [
        'label' => 'dob',
        'operate' => [
            '=',
            '!=',
            'like',
            '>',
            '<',
            '>=',
            '<=',
            'empty',
            'not empty'
        ],
        'type' => 'date',
        'value' => null
    ],
    'city' => [
        'label' => 'city',
        'operate' => [
            '=',
            '!=',
            'like',
            'empty',
            'not empty'
        ],
        'type' => 'string',
        'value' => null
    ],
    'country' => [
        'label' => 'country',
        'operate' => [
            '=',
            '!=',
            'like',
            'empty',
            'not empty'
        ],
        'type' => 'string',
        'value' => null
    ],
    'user_uuid' => [
        'label' => 'user_uuid',
        'operate' => [
            '=',
            '!=',
            'like',
            '>',
            '<',
            '>=',
            '<='
        ],
        'type' => 'numeric',
        'value' => null
    ],
    'user.username' => [
        'label' => 'user.username',
        'operate' => [
            '=',
            '!=',
            'like',
            '>',
            '<',
            '>=',
            '<='
        ],
        'type' => 'string',
        'value' => null
    ],
];
