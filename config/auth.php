<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a perfect start for most applications.
    |
    */

    'defaults' => [
        'guard' => 'sys',
        'passwords' => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | here which uses session storage and the Eloquent user provider.
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | Supported: "session", "token"
    |
    */

    'guards' => [
        'sys' => [
            'driver' => 'session',
            'provider' => 'admin',
        ],

        'api' => [
            'driver' => 'jwt',
            'provider' => 'member',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | If you have multiple user tables or models you may configure multiple
    | sources which represent each model / table. These sources may then
    | be assigned to any extra authentication guards you have defined.
    |
    | Supported: "database", "eloquent"
    |
    */

    'providers' => [
        'admin' => [
            'driver' => 'eloquent',
            'model' => App\Models\Admin::class,
        ],

        'member' => [
            'driver' => 'eloquent',
            'model' => App\Models\Member::class,
        ],

        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | You may specify multiple password reset configurations if you have more
    | than one user table or model in the application and you want to have
    | separate password reset settings based on the specific user types.
    |
    | The expire time is the number of minutes that the reset token should be
    | considered valid. This security feature keeps tokens short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_resets',
            'expire' => 60,
        ],
    ],

    'permissions' => [
        'member' => [
            'title' => '会员管理',
            'items' => [
                //'list' => '普通会员管理',
                'proxy' => '代理会员管理',
                'group' => '代理级别管理',
            ]
        ],
        'meeting' => [
            'title' => '会议管理',
            'items' => [
                'category' => '会议类型管理',
                'list' => '会议管理',
            ],
        ],
        'course' => [
            'title' => '课程管理',
            'items' => [
                //'category' => '课程类型管理',
                'list' => '课程管理',
            ],
        ],
        'order' => [
            'title' => '订单管理',
            'items' => [
                'member' => '代理注册订单',
                'normal' => '会议/课程报名订单',
            ],
        ],
        'company' => [
            'title' => '公司财务管理',
            'items' => [
                'finance' => '财务日志',
                'wallet' => '提现申请',
            ],
        ],
        /*'article' => [
            'title' => '文章管理',
            'items' => [
                'category' => '文章类型管理',
                'list' => '文章列表管理'
            ]
        ],
        'product' => [
            'title' => '产品管理',
            'items' => [
                'list' => '产品列表管理',
            ],
        ],
        'company' => [
            'title' => '企业展示管理',
            'items' => [
                'list' => '企业展示列表管理',
            ],
        ],*/
        'sys' => [
            'title' => '后台管理',
            'items' => [
                'admins' => '用户管理',
                'params' => '参数设置',
                'industry' => '行业管理'
            ]
        ],
    ],
];
