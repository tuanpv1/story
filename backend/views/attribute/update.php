<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Attribute */

$this->title = Yii::t('app','Cập nhật thông tin thuộc tính: ') . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Quản lý thuộc tính khuyến mại'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Thông tin thuộc tính'), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-update">
    <div class="panel panel-success">
        <div class="panel-heading text-center"><h3 class="panel-title"><?= Yii::t('app','Cập nhật thông tin chi tiết thuộc tính khuyến mại') ?></h3></div>
        <div class="box ">
            <div class="box-body">
                <?= $this->render('_form', [
                    'model' => $model,
                ]) ?>
            </div>
        </div>
</div>
