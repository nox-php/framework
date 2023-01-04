<?php

return [
    'not_found' => 'Module cannot be found',

    'boot_failed' => 'Module failed to boot',

    'publish' => [
        'success' => [
            'title' => ':name',
            'body' => 'Successfully published module'
        ],

        'failed' => [
            'title' => ':name',
            'body' => 'Failed to publish module'
        ]
    ],

    'enabled' => [
        'success' => [
            'title' => ':name',
            'body' => 'Successfully enabled module'
        ],

        'failed' => [
            'title' => ':name'
        ]
    ],

    'disabled' => [
        'success' => [
            'title' => ':name',
            'body' => 'Successfully disabled module'
        ],

        'failed' => [
            'title' => ':name'
        ]
    ],

    'install' => [
        'success' => [
            'title' => ':name',
            'body' => 'Successfully installed module'
        ],

        'failed' => [
            'title' => 'Failed to install module'
        ]
    ],

    'delete' => [
        'success' => [
            'title' => ':name',
            'body' => 'Successfully deleted module'
        ],

        'failed' => [
            'title' => 'Failed to delete module'
        ]
    ]
];
