<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'],
                ],

            ],
        ],
    ],
//    'modules' => [
//        'user' => [
//            'class' => 'dektrium\user\Module',
//            'as frontend' => 'dektrium\user\filters\FrontendFilter',
//            'controllerMap' => [
//                'registration' => 'frontend\controllers\user\RegistrationController',
//                'security' => 'frontend\controllers\user\SecurityController'
//            ],
//            'modelMap' => [
//                'User' => 'common\models\User',
//                'Profile' => 'common\models\Profile',
//            ],
//        ],
//
//    ],
    'params' => $params,
];
