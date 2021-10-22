<?php

return [

    'api' => [

        'env' => 'staging',

        'development' => [
            'url' => 'https://admuat.goodbasket.com/',
            'version' => 1,
            'role' => 'admin',
            'admin' => [
                'username' => 'ajaygb',
                'password' => 'jayaraj321$A',
            ],
            'timeoutSeconds' => 600,
            'retryLoop' => 5,
            'retryLoopInterval' => 60,
        ],
        'testing' => [
            'url' => 'https://admuat.goodbasket.com/',
            'version' => 1,
            'role' => 'admin',
            'admin' => [
                'username' => 'ajaygb',
                'password' => 'jayaraj321$A',
            ],
            'timeoutSeconds' => 600,
            'retryLoop' => 5,
            'retryLoopInterval' => 60,
        ],
        'staging' => [
            'url' => 'https://admuat.goodbasket.com/',
            'version' => 1,
            'role' => 'admin',
            'admin' => [
                'username' => 'nived',
                'password' => 'Commerce@9',
            ],
            'timeoutSeconds' => 600,
            'retryLoop' => 5,
            'retryLoopInterval' => 60,
        ],
        'live' => [
            'url' => 'https://api.goodbasket.com/',
            'version' => 1,
            'role' => 'admin',
            'admin' => [
                'username' => 'nived',
                'password' => 'Commerce@9',
            ],
            'timeoutSeconds' => 600,
            'retryLoop' => 5,
            'retryLoopInterval' => 60,
        ],
    ],

    'order_statuses' => [
        'being_prepared' => 'Being Prepared',
        'canceled' => 'Canceled',
        'closed' => 'Closed',
        'complete' => 'Complete',
        'fraud' => 'Suspected Fraud',
        'holded' => 'On Hold',
        'out_for_delivery' => 'Out For Delivery',
        'ready_to_dispatch' => 'Ready To Dispatch',
        'payment_review'  => 'Payment Review',
        'pending'  => 'Processing',
        'pending_payment' => 'Pending Payment',
        'processing' => 'Processing',
        'returned' => 'Returned',
    ],

    'emirates' => [
        'DXB' =>'Dubai',
        'SHJ' =>'Sharjah',
        'AUH'=>'Abu Dhabhi',
        'AJM' => 'Ajman'
    ],

];
