<?php

return [

    'defaults' => [
        'apiEnv' => env('DEFAULT_API_ENV', 'production'),
    ],

    'api' => [

        'development' => [

            'defaults' => [
                'channel' => 'gb-1',
                'country_code' => 'AE'
            ],

            'channels' => [
                'gb-1' => [
                    'id' => 'gb-1',
                    'name' => 'Good Basket Magento UAT',
                    'url' => 'https://admuat.goodbasket.com/',
                    'version' => 1,
                    'apiUri' => 'rest/V1/',
                    'authUri' => 'integration/admin/token',
                    'authRole' => 'admin',
                    'authKey' => 'ajaygb',
                    'authSecret' => 'jayaraj321$A',
                    'timezone' => 'Asia/Dubai',
                    'timeoutSeconds' => 600,
                    'retryLoop' => 5,
                    'retryLoopInterval' => 60,
                ],
            ],

        ],

        'testing' => [

            'defaults' => [
                'channel' => 'gb-1',
                'country_code' => 'AE'
            ],

            'channels' => [
                'gb-1' => [
                    'id' => 'gb-1',
                    'name' => 'Good Basket Magento UAT',
                    'url' => 'https://admuat.goodbasket.com/',
                    'version' => 1,
                    'apiUri' => 'rest/V1/',
                    'authUri' => 'integration/admin/token',
                    'authRole' => 'admin',
                    'authKey' => 'ajaygb',
                    'authSecret' => 'jayaraj321$A',
                    'timezone' => 'Asia/Dubai',
                    'timeoutSeconds' => 600,
                    'retryLoop' => 5,
                    'retryLoopInterval' => 60,
                ],
            ],

        ],

        'staging' => [

            'defaults' => [
                'channel' => 'gb-1',
                'country_code' => 'AE'
            ],

            'channels' => [
                'gb-1' => [
                    'id' => 'gb-1',
                    'name' => 'Good Basket Magento UAT',
                    'url' => 'https://admuat.goodbasket.com/',
                    'version' => 1,
                    'apiUri' => 'rest/V1/',
                    'authUri' => 'integration/admin/token',
                    'authRole' => 'admin',
                    'authKey' => 'nived',
                    'authSecret' => 'Commerce@9',
                    'timezone' => 'Asia/Dubai',
                    'timeoutSeconds' => 600,
                    'retryLoop' => 5,
                    'retryLoopInterval' => 60,
                ],
            ],

        ],

        'production' => [

            'defaults' => [
                'channel' => 'gb-1',
                'country_code' => 'AE'
            ],

            'channels' => [
                'gb-1' => [
                    'id' => 'gb-1',
                    'name' => 'Good Basket Magento',
                    'url' => 'https://api.goodbasket.com/',
                    'version' => 1,
                    'apiUri' => 'rest/V1/',
                    'authUri' => 'integration/admin/token',
                    'authRole' => 'admin',
                    'authKey' => 'nived',
                    'authSecret' => 'Commerce@9',
                    'timezone' => 'Asia/Dubai',
                    'timeoutSeconds' => 600,
                    'retryLoop' => 5,
                    'retryLoopInterval' => 60,
                ],
            ],

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
