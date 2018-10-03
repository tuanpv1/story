<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = Yii::t('app','Tạo mới tài khoản');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Quản lý tài khoản'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">
    <div class="panel panel-success">
        <div class="panel-heading text-center"><h3 class="panel-title"><?= Yii::t('app','Tạo tài khoản Admin') ?></h3></div>
            <div class="box ">
                <div class="box-body">
                    <?= $this->render('_form', [
                        'model' => $model,
                    ]) ?>
                </div>
        </div>
</div>
