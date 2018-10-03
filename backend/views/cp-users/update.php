<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = Yii::t('app','Cập nhật thông tin đại lý: ') . $model->full_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Quản lý đại lý'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Thông tin đại lý'), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="course-create">
    <div class="panel panel-success">
        <div class="panel-heading"><h3 class="panel-title"><?= $this->title ?></h3></div>
        <div class="box ">

            <div class="box-body">
                <?= $this->render('_form', [
                    'model' => $model,
                ]) ?>
            </div>
        </div>
    </div>
</div>
