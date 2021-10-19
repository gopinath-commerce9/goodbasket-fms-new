<?php

return [

    'items' => [
        [
            'name' => 'Dashboard',
            'path' => '/dashboard',
            'icon' => 'flaticon2-protection',
            'customIcon' => false,
            'toolTip' => 'Dashboard',
            'roles' => ['admin'],
            'active' => true,
            'children' => null,
        ],
        [
            'name' => 'Update Stock',
            'path' => '/stock/update',
            'icon' => 'ktmt/media/update-stock.png',
            'customIcon' => true,
            'toolTip' => 'Update Stock',
            'roles' => ['admin'],
            'active' => true,
            'children' => null,
        ],
        [
            'name' => 'Out of Stock Report',
            'path' => '/stock/oos-report',
            'icon' => 'ktmt/media/out-of-stock-report.png',
            'customIcon' => true,
            'toolTip' => 'Out of Stock Report',
            'roles' => ['admin'],
            'active' => true,
            'children' => null,
        ],
        [
            'name' => 'Order Items Sales Report',
            'path' => '/sales/order-items-report',
            'icon' => 'ktmt/media/order-items-sales-report.png',
            'customIcon' => true,
            'toolTip' => 'Order Items Sales Report',
            'roles' => ['admin'],
            'active' => true,
            'children' => null,
        ],
        [
            'name' => 'POS System',
            'path' => '/sales/pos',
            'icon' => 'ktmt/media/pos_icon.png',
            'customIcon' => true,
            'toolTip' => 'POS System',
            'roles' => ['admin'],
            'active' => true,
            'children' => null,
        ],
        [
            'name' => 'InStores Sales Report',
            'path' => '/sales/instore-report',
            'icon' => 'ktmt/media/order-items-sales-report.png',
            'customIcon' => true,
            'toolTip' => 'InStores Sales Report',
            'roles' => ['admin'],
            'active' => true,
            'children' => null,
        ],
    ],

];
