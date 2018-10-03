<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\AuthRule;

/* @var $this yii\web\View */
/* @var $model common\models\AuthItem */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
    ]); ?>
    <div class="row">
        <div class="col-md-3">
            <?= Yii::t('app', 'Tên nhóm quyền') ?><span style="color: red">(*)</span>
        </div>
        <div class="col-md-9">
            <?= $form->field($model, 'name')->textInput(['placeholder' => Yii::t('app', '4-12 ký tự'), 'maxlength' => 12])->label(false)?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <?= Yii::t('app', 'Mô tả') ?>
        </div>
        <div class="col-md-9">
            <?= $form->field($model, 'description')->textInput(['placeholder' => Yii::t('app', '4-30 ký tự'), 'maxlength' => 30])->label(false) ?>
        </div>
    </div>

    <div class="form-group text-center">
        <?= Html::submitButton($model->isNewRecord ? ''.\Yii::t('app', 'Tạo nhóm quyền') : ''.\Yii::t('app', 'Cập nhật'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>