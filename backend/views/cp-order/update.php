<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Attribute */

$this->title = Yii::t('app','Cập nhật template đơn hàng: ') . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Quản lý template đơn hàng'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Thông tin template đơn hàng'), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-update">
    <div class="panel panel-success">
        <div class="panel-heading text-center"><h3 class="panel-title"><?= Yii::t('app','Cập nhật thông tin template đơn hàng') ?></h3></div>
        <div class="box ">
            <div class="box-body">
                <?= $this->render('_form', [
                    'model' => $model,
                    'model_attribute' => $model_attribute,
                ]) ?>
            </div>
        </div>
</div>
