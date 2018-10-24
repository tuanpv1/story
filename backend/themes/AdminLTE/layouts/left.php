<?php
/* @var $directoryAsset string */

use common\models\User;

/* @var $baseAsset string */
$username = 'Unknown';
$user = null;
if (!Yii::$app->user->isGuest) {
    /** @var \common\models\User $user */
    $user = Yii::$app->user->identity;
    $username = $user->username;
}
?>
<aside class="main-sidebar">

    <section class="sidebar">
        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu'],
                'items' => [
                    [
                        'label' => Yii::t('app', 'Quản lý tài khoản'),
                        'icon' => 'users',
                        'url' => '#',
                        'items' => [
                            [
                                'label' => Yii::t('app', 'QL tài khoản Admin'),
                                'icon' => 'lock',
                                'url' => ['users/index'],
                            ],
                            [
                                'label' => Yii::t('app', 'QL quyền'),
                                'icon' => 'key',
                                'url' => ['rbac-backend/permission'],
                            ],
                            [
                                'label' => Yii::t('app', 'QL nhóm quyền'),
                                'icon' => 'key',
                                'url' => ['rbac-backend/role']
                            ],
                        ]
                    ],
                    [
                        'label' => Yii::t('app', 'Quản lý danh mục'),
                        'icon' => 'book',
                        'url' => ['category/index'],
                    ],
                    [
                        'label' => Yii::t('app', 'Quản lý content'),
                        'icon' => 'eye',
                        'url' => ['content/index'],
                    ],
                    [
                        'label' => Yii::t('app', 'Quản lý thuê bao'),
                        'icon' => 'user',
                        'url' => ['subscriber/index'],
                    ],
                ],
            ]
        ) ?>
    </section>
</aside>
