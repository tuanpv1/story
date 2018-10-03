<?php
/* @var $directoryAsset string */


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
                        'label' => Yii::t('app', 'Quản lý đơn hàng'),
                        'icon' => 'file',
                        'url' => ['cp-order-asm/index'],
                    ],
                    [
                        'label' => Yii::t('app', 'Quản lý chương trình ưu đãi'),
                        'icon' => 'gift',
                        'url' => ['promotion/index'],
                    ],
                    [
                        'label' => Yii::t('app', 'Quản lý mã ưu đãi'),
                        'icon' => 'tasks',
                        'url' => ['#'],
                        'items'=>[
                            [
                                'label' => Yii::t('app', 'Gen mã ưu đãi'),
                                'icon' => 'download',
                                'url' => ['promotion-code/view'],
                            ],
                            [
                                'label' => Yii::t('app', 'Danh sách mã ưu đãi'),
                                'icon' => 'signal',
                                'url' => ['promotion-code/index'],
                            ],
                        ]
                    ],
                ],
            ]
        ) ?>
    </section>
</aside>
