<?php

/* @var $this yii\web\View */
/* @var $model common\models\Content */

$this->title = Yii::t('app', 'Cập nhật nội dung ') . $model->display_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Quản lý nội dung'), 'url' => ['index']];
if ($model->parent_id) {
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app','Bộ: ').$model->getParentName(), 'url' => ['view', 'id' => $model->parent_id, 'active' => 2]];
}
$this->params['breadcrumbs'][] = ['label' => $model->display_name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="panel panel-success">
    <div class="panel-heading text-center"><h3 class="panel-title"><?= $this->title ?></h3></div>
    <div class="box ">
        <div class="box-body">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
