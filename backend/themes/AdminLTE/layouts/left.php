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

        <!-- Sidebar user panel -->
<!--        <div class="user-panel">-->
<!--            <div class="pull-left image">-->
<!--                <img src="--><?//= $baseAsset ?><!--/img/avatar.jpeg" class="img-circle" alt="User Image"/>-->
<!--            </div>-->
<!--            <div class="pull-left info">-->
<!--                <p>--><?//= $username ?><!--</p>-->
<!---->
<!--                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>-->
<!--            </div>-->
<!--        </div>-->
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
                                'label' => Yii::t('app', 'Quản lý đối tác sử dụng mã'),
                                'icon' => 'asterisk',
                                'url' => ['partner/index'],
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
                        'label' => Yii::t('app', 'Quản lý đại lý'),
                        'icon' => 'asterisk',
                        'url' => ['cp-users/index'],
                    ],
                    [
                        'label' => Yii::t('app', 'Quản lý thuộc tính khuyến mại'),
                        'icon' => 'gift',
                        'url' => ['attribute/index'],
                    ],
                    [
                        'label' => Yii::t('app', 'QL template đơn hàng'),
                        'icon' => 'file',
                        'url' => ['cp-order/index'],
                    ],
                    [
                        'label' => Yii::t('app', 'Quản lý đơn hàng'),
                        'icon' => 'shopping-cart',
                        'url' => ['cp-order-asm/index'],
                    ],
                    [
                        'label' => Yii::t('app', 'Quản lý chương trình ưu đãi'),
                        'icon' => 'th-large',
                        'url' => ['promotion/index'],
                    ],
                ],
            ]
        ) ?>
    </section>
</aside>
