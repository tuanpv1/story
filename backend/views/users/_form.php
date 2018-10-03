<?php

use common\models\User;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
$user_name_show = Html::getInputId($model, 'username');
$title = Yii::t('app', 'Bạn có chắc chắn muốn tạo tài khoản ');
$check = 0;
if ($model->isNewRecord) {
    $check = 1;
}
$js = <<<JS
$('#form_create_user_admin').on('beforeSubmit', function (e) {
    var check = $check;
    if(check == 1){
        var useranme = $('#user-username').val();
        if(confirm('$title' + useranme + '?')){
            return true
        }else{
            return false        
        }
    }
});
JS;
$this->registerJs($js, \yii\web\View::POS_READY);
?>
<div class="col-md-offset-2 col-md-11">
    <div class="user-form">

        <?php $form = ActiveForm::begin(['id' => 'form_create_user_admin']); ?>
        <div class="row">
            <div class="col-md-2">
                <?= Yii::t('app', 'Tên đăng nhập') ?> <span style="color: red">(*)</span>
            </div>
            <div class="col-md-6">
                <?php if ($model->isNewRecord) { ?>
                    <?= $form->field($model, 'username')->textInput(['placeholder' => Yii::t('app', '4-12 ký tự'), 'maxlength' => 12])->label(false) ?>
                <?php } else { ?>
                    <?= $form->field($model, 'username')->textInput(['readonly' => true])->label(false) ?>
                <?php } ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2">
                <?= Yii::t('app', 'Email') ?><span style="color: red">(*)</span>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'email')->textInput(['maxlength' => true])->label(false) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2">
                <?= Yii::t('app', 'Số điện thoại') ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'phone')->textInput(['maxlength' => true])->label(false) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2">
                <?= Yii::t('app', 'Tên đầy đủ') ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'full_name')->textInput(['placeholder' => Yii::t('app', '4-50 ký tự'), 'maxlength' => 50])->label(false) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2">
                <?= Yii::t('app', 'Trạng thái hoạt động') ?><span style="color: red">(*)</span>
            </div>
            <div class="col-md-6">
                <?php
                if (Yii::$app->user->identity->username == 'admin') { ?>
                    <?= $form->field($model, 'status')->dropDownList(User::listStatus())->label(false) ?>
                <?php } else { ?>
                    <?= $form->field($model, 'status')->dropDownList(User::listStatus(), ['disabled' => true])->label(false) ?>
                <?php } ?>
            </div>
        </div>
        <div class="form-actions">
            <div class="row">
                <div class="col-md-offset-3 col-md-8">
                    <div class="form-group">
                        <?php if ($model->isNewRecord) { ?>
                            <?= Html::submitButton(Yii::t('app', 'Tạo tài khoản'), ['class' => 'btn btn-success']) ?>
                            <?= Html::resetButton(Yii::t('app', 'Nhập lại'), ['class' => 'btn btn-warning']) ?>
                        <?php } else { ?>
                            <?php if (Yii::$app->user->identity->username == 'admin' && $model->username != 'admin') { ?>
                                <?= Html::a(Yii::t('app', 'Reset mật khẩu'), ['reset-password', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
                            <?php } else { ?>
                                <?= Html::a(Yii::t('app', 'Đổi mật khẩu'), ['change-password'], ['class' => 'btn btn-success']) ?>
                            <?php } ?>
                            <?= Html::submitButton(Yii::t('app', 'Cập nhật'), ['class' => 'btn btn-primary']) ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

