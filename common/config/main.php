<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'name' => 'VIETTALK PROMOTION',
    'timeZone' => 'Asia/Ho_Chi_Minh',
    'modules' => [

    ],
    'aliases' => [
        '@cp' => '@cp',
        '@file_export' => 'uploads/file_export',
        '@cat_image' => 'uploads/cat_images',
        '@content_images' => 'uploads/content_images',
    ],
    'components' => [

        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],

        /*'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'enablePrettyUrl' => true,
//            'suffix' => '.html',
        ],
        */
        'i18n' => [
            'translations' => [

                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                    //'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'app' => 'app.php',
                        'topic' => 'topic.php'

                    ],
                ],
                'user' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                    //'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'user' => 'user.php'

                    ],
                ],
                'topic' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                    //'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'topic' => 'topic.php'

                    ],
                ],
                'grade' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                    //'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'grade' => 'grade.php'

                    ],
                ],
                'campaign' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                    //'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'grade' => 'campaign.php'

                    ],
                ],
                'quiz' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                    //'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'grade' => 'quiz.php'

                    ],
                ],
                'question' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                    //'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'grade' => 'question.php'

                    ],
                ],
                'article' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                    //'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'grade' => 'article.php'

                    ],
                ],
                'article_cat' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                    //'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'grade' => 'article_cat.php'

                    ],
                ],
            ],
        ],

        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host'=> "smtp.vivas.vn",
                'username' => 'support@tvod.vn',
                'password' => 'vivas@123', // new generated API key by mandrill
                'port'=> 587,
                'encryption' => 'tls',
            ],
        ],

    ],
    'language' => 'vi_VN',
];
