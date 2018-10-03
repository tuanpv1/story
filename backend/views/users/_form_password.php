<?php

use kartik\widgets\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
?>


<?php $form = ActiveForm::begin([
    'method' => 'post',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'fullSpan' => 6,
    'action' => ['users/change-password'],
    'formConfig' => [
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'showLabels' => true,
        'labelSpan' => 2,
        'deviceSize' => ActiveForm::SIZE_SMALL,
    ],
    'enableAjaxValidation' => true,
    'enableClientValidation' => false,
]);
$formId = $form->id;
?>
<div class="col-md-offset-3 col-md-12">
    <div class="row">
        <div class="col-md-2">
            <?= Yii::t('app', 'Mật khẩu cũ') ?><span style="color: red">(*)</span>
        </div>
        <div class="col-md-10">
            <?= $form->field($model, 'password_old')->passwordInput(['placeholder' => ''.\Yii::t('app', 'Nhập mật khẩu hiện tại')])->label(false) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">
            <?= Yii::t('app', 'Mật khẩu mới') ?><span style="color: red">(*)</span>
        </div>
        <div class="col-md-10">
            <?= $form->field($model, 'password_new')->passwordInput(['placeholder' => ''.\Yii::t('app', 'Nhập mật khẩu mới')])->label(false) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">
            <?= Yii::t('app', 'Xác nhận mật khẩu mới') ?><span style="color: red">(*)</span>
        </div>
        <div class="col-md-10">
            <?= $form->field($model, 'password_new_confirm')->passwordInput(['placeholder' => ''.\Yii::t('app', 'Nhập lại mật khẩu')])->label(false) ?>
        </div>
    </div>
    <div class="form-actions">
        <div class="row">
            <div class="col-md-offset-2 col-md-10">
                <?= Html::submitButton(''.\Yii::t('app', 'Đổi mật khẩu'),
                    ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton(Yii::t('app', 'Nhập lại'), ['class' => 'btn btn-warning']) ?>
            </div>
        </div>
    </div>
</div>




<?php ActiveForm::end(); ?>


