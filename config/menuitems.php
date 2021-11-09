<?php

return [

    'items' => [
        [
            'name' => 'Users',
            'path' => '/userauth/users',
            'icon' => 'fas fa-user-friends',
            'customIcon' => false,
            'toolTip' => 'Users',
            'permission' => 'users.view',
            'active' => true,
            'children' => null,
        ],
        [
            'name' => 'User Roles',
            'path' => '/userrole/roles',
            'icon' => 'fas fa-user-tie',
            'customIcon' => false,
            'toolTip' => 'User Roles',
            'permission' => 'user-roles.view',
            'active' => true,
            'children' => null,
        ],
        [
            'name' => 'User Role Permissions',
            'path' => '/userrole/permissions',
            'icon' => 'fas fa-tasks',
            'customIcon' => false,
            'toolTip' => 'User Role Permissions',
            'permission' => 'user-role-permissions.view',
            'active' => true,
            'children' => null,
        ],
        [
            'name' => 'Update Stock',
            'path' => '/stock/update',
            'icon' => 'ktmt/media/update-stock.png',
            'customIcon' => true,
            'toolTip' => 'Update Stock',
            'permission' => null,
            'active' => false,
            'children' => null,
        ],
        [
            'name' => 'Out of Stock Report',
            'path' => '/stock/oos-report',
            'icon' => 'ktmt/media/out-of-stock-report.png',
            'customIcon' => true,
            'toolTip' => 'Out of Stock Report',
            'permission' => null,
            'active' => false,
            'children' => null,
        ],
        [
            'name' => 'Order Items Sales Report',
            'path' => '/sales/order-items-report',
            'icon' => 'ktmt/media/order-items-sales-report.png',
            'customIcon' => true,
            'toolTip' => 'Order Items Sales Report',
            'permission' => null,
            'active' => false,
            'children' => null,
        ],
        [
            'name' => 'POS System',
            'path' => '/sales/pos',
            'icon' => 'ktmt/media/pos_icon.png',
            'customIcon' => true,
            'toolTip' => 'POS System',
            'permission' => null,
            'active' => false,
            'children' => null,
        ],
        [
            'name' => 'InStores Sales Report',
            'path' => '/sales/instore-report',
            'icon' => 'ktmt/media/order-items-sales-report.png',
            'customIcon' => true,
            'toolTip' => 'InStores Sales Report',
            'permission' => null,
            'active' => false,
            'children' => null,
        ],
    ],

];
