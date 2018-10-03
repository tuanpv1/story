<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = Yii::t('app','Đổi mật khẩu');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Trang chủ'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <h1 class="text-center"><?= $this->title ?></h1>

    <?= $this->render('_form_password', [
        'model' => $model,
    ]) ?>

</div>
