<?php

use common\models\Attribute;
use common\models\User;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Attribute */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="col-md-offset-2 col-md-11">
    <div class="user-form">

        <?php $form = ActiveForm::begin(['id' => 'form_create_attribute']); ?>
        <div class="row">
            <div class="col-md-2">
                <?= Yii::t('app', 'Tên thuộc tính') ?><span style="color: red">(*)</span>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'name')->textInput(['placeholder' => Yii::t('app', ''), 'maxlength' => 255])->label(false) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2">
                <?= Yii::t('app', 'Trạng thái') ?><span style="color: red">(*)</span>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'status')->dropDownList(Attribute::listStatus())->label(false) ?>
            </div>
        </div>
        <div class="form-actions">
            <div class="row">
                <div class="col-md-offset-3 col-md-8">
                    <div class="form-group">
                        <?php if ($model->isNewRecord) { ?>
                            <?= Html::submitButton(Yii::t('app', 'Tạo thuộc tính'), ['class' => 'btn btn-success']) ?>
                        <?php } else { ?>
                            <?= Html::submitButton(Yii::t('app', 'Cập nhật'), ['class' => 'btn btn-primary']) ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

