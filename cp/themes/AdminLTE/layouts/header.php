<?php
use yii\helpers\Html;
use common\widgets\NavBar;
use common\widgets\Nav;
use \common\widgets\NotificationDropdown;
use \common\widgets\UserDropdown;

/* @var $this \yii\web\View */
/* @var $content string */
/* @var $directoryAsset string */
/* @var $baseAsset string */
$username = '';
if(!Yii::$app->user->isGuest){
    $username = Yii::$app->user->identity->username;
}
$url = \yii\helpers\Url::to(['site/logout']);
$js = <<<JS
    $('#change-text-cp').click(function(){
        $('#myModalLogoutCp').modal('show');
        return false;         
    });

    $('#proccessLogoutCp').click(function() {
        $.ajax({
            url : '$url',
            type :'POST'
        });
    });
JS;
$this->registerJs($js, \yii\web\View::POS_READY);
?>

<header class="main-header">
    <?php

    NavBar::begin([
        'brandLabel' => Yii::t('app','Trang chủ'),
        'brandLabelMini' => Yii::t('app','Home'),
        'innerContainerOptions' => ['class' => 'navbar-custom-menu' ]
    ]);

    $items = [
        [
            'encode' => false,
            'label' => Html::img("$baseAsset/img/avatar.jpeg", ['class' => 'user-image']) . Html::tag('span', $username, ['class' => 'hidden-xs']),
            'options' => ['class' => 'dropdown user user-menu'],
            'linkOptions' => [
//                'data-toggle' => "dropdown",
                'class' => 'dropdown-toggle'
            ],
            'url' => '#',
            'dropdownClass' => UserDropdown::className()
        ],
        [
            'encode' => false,
//            'label' =>Html::tag('li', Html::a(Yii::t('app','Đăng xuất'), ['/site/logout'], ['data-method' => 'post','data-confirm' => Yii::t('app',"Bạn có chắc chắn muốn đăng xuất?")])),
            'label' => Html::tag('li', Html::a(Yii::t('app', 'Đăng xuất'), ['#'], ['id' => 'change-text-cp'])),
            'url'=>false,
        ],
    ];
    echo Nav::widget([
        'items' => $items,
        'activateParents' => true,
        'options' => [
            'class' => 'nav navbar-nav'
        ]
    ]);

    NavBar::end();
    ?>

</header>
<!-- Modal -->
<div class="modal fade" id="myModalLogoutCp" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel"><?= Yii::t('app', 'Đăng xuất') ?></h4>
            </div>
            <div class="modal-body">
                <?= Yii::t('app', "Bạn có chắc chắn muốn đăng xuất?") ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= Yii::t('app', 'Huỷ') ?></button>
                <button type="button" class="btn btn-primary" id="proccessLogoutCp">ok</button>
            </div>
        </div>
    </div>
</div>