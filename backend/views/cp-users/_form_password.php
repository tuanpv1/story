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
<div class="col-md-offset-4 col-md-8">
    <?= $form->field($model,
        'password_old')->passwordInput(['placeholder' => ''.\Yii::t('app', 'Nhập mật khẩu hiện tại')]) ?>
    <?= $form->field($model,
        'password_new')->passwordInput(['placeholder' => ''.\Yii::t('app', 'Nhập mật khẩu có độ dài  tối thiểu 8 kí tự')]) ?>
    <?= $form->field($model, 'password_new_confirm')->passwordInput(['placeholder' => ''.\Yii::t('app', 'Nhập lại mật khẩu')]) ?>
    <div class="form-actions">
        <div class="row">
            <div class="col-md-offset-2 col-md-10">
                <?= Html::submitButton(''.\Yii::t('app', 'Cập nhật'),
                    ['class' => 'btn btn-primary']) ?>
                <?= Html::a(''.\Yii::t('app', 'Hủy thao tác'), ['info'], ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>
            </div>
        </div>
    </div>
</div>




<?php ActiveForm::end(); ?>


