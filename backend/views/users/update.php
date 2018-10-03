<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = Yii::t('app','Cập nhật thông tin tài khoản: ') . $model->username;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Quản lý tài khoản'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Thông tin tài khoản'), 'url' => ['info', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-update">
    <div class="panel panel-success">
        <div class="panel-heading text-center"><h3 class="panel-title"><?= Yii::t('app','Cập nhật thông tin chi tiết tài khoản') ?></h3></div>
        <div class="box ">
            <div class="box-body">
                <?= $this->render('_form', [
                    'model' => $model,
                ]) ?>
            </div>
        </div>
</div>
