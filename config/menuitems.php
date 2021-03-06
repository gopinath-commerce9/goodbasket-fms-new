<?php

return [

    'items' => [
        [
            'name' => 'Users',
            'path' => '/userauth/users',
            'icon' => 'fas fa-user-friends',
            'customIcon' => false,
            'toolTip' => 'Users',
            'permission_type' => 'permission',
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
            'permission_type' => 'permission',
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
            'permission_type' => 'permission',
            'permission' => 'user-role-permissions.view',
            'active' => true,
            'children' => null,
        ],
        [
            'name' => 'Pickers',
            'path' => '/userrole/pickers',
            'icon' => 'fas fa-cart-arrow-down',
            'customIcon' => false,
            'toolTip' => 'Pickers',
            'permission_type' => 'role',
            'permission' => ['admin', 'supervisor'],
            'active' => true,
            'children' => null,
        ],
        [
            'name' => 'Drivers',
            'path' => '/userrole/drivers',
            'icon' => 'fas fa-shuttle-van',
            'customIcon' => false,
            'toolTip' => 'Drivers',
            'permission_type' => 'role',
            'permission' => ['admin', 'supervisor'],
            'active' => true,
            'children' => null,
        ],
        [
            'name' => 'Orders',
            'path' => '/sales/orders',
            'icon' => 'fas fa-shopping-cart',
            'customIcon' => false,
            'toolTip' => 'Orders',
            'permission_type' => 'role',
            'permission' => ['admin'],
            'active' => true,
            'children' => null,
        ],
        [
            'name' => 'Update Stock',
            'path' => '/sales/stock-update',
            'icon' => 'ktmt/media/update-stock.png',
            'customIcon' => true,
            'toolTip' => 'Update Stock',
            'permission_type' => 'role',
            'permission' => ['admin', 'supervisor'],
            'active' => true,
            'children' => null,
        ],
        [
            'name' => 'Out of Stock Report',
            'path' => '/sales/oos-report',
            'icon' => 'ktmt/media/out-of-stock-report.png',
            'customIcon' => true,
            'toolTip' => 'Out of Stock Report',
            'permission_type' => 'role',
            'permission' => ['admin', 'supervisor'],
            'active' => true,
            'children' => null,
        ],
        [
            'name' => 'Order Items Sales Report',
            'path' => '/sales/order-items-report',
            'icon' => 'ktmt/media/order-items-sales-report.png',
            'customIcon' => true,
            'toolTip' => 'Order Items Sales Report',
            'permission_type' => 'role',
            'permission' => ['admin', 'supervisor'],
            'active' => true,
            'children' => null,
        ],
        [
            'name' => 'POS System',
            'path' => '/sales/pos',
            'icon' => 'ktmt/media/pos_icon.png',
            'customIcon' => true,
            'toolTip' => 'POS System',
            'permission_type' => 'role',
            'permission' => ['admin', 'cashier'],
            'active' => true,
            'children' => null,
        ]
    ],

];
