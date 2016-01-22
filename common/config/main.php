<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
    ],
    'modules' => [
        'user' => [
            'class' => 'dektrium\user\Module',
            'controllerMap' => [
                'admin' => [
                    'class' => 'dektrium\user\controllers\AdminController',
                    'as access' => [
                        'class' => 'yii\filters\AccessControl',
                        'rules' => [
                            [
                                'allow' => true,
                                'roles' => ['administrateUser'],
                            ],
                        ],
                    ],
                ],
                'profile' => [
                    'class' => 'dektrium\user\controllers\ProfileController',
                    'as access' => [
                        'class' => 'yii\filters\AccessControl',
                        'rules' => [
                            [
                                ['allow' => true, 'actions' => ['index'], 'roles' => ['@']], //redirect на show. id в данном действии нет
                                ['allow' => true, 'actions' => ['show'], 'roles' => ['showUserProfile']], // просмотр других запрещён
                            ],
                        ],
                    ],
                ],
                'settings' => [
                    'class' => 'dektrium\user\controllers\SettingsController',
                    'as access' => [
                        'class' => 'yii\filters\AccessControl',
                        'rules' => [
                            [
                                ['allow' => true, 'actions' => ['confirm'], 'roles' => ['@']], //подтвердить почту по ссылке можно только залогинившись
                                ['allow' => true, 'actions' => ['account'], 'roles' => ['updateSelfAccount']],
                                ['allow' => true, 'actions' => ['profile'], 'roles' => ['updateSelfProfile']],
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'admin' => [
            'class' => 'mdm\admin\Module',
            'as access' => [
                'class' => 'yii\filters\AccessControl',
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['administrateRbac'],
                    ],
                ],
            ],
        ],
    ],
];
