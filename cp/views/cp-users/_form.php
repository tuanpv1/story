<?php

use common\models\User;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(['id' => 'form_create_user_admin']); ?>
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-2">
            <?= Yii::t('app', 'Tên truy cập ') ?><span style="color: red">(*)</span>
        </div>
        <div class="col-md-6">
                <?= $form->field($model, 'username')->textInput(['readonly' => true])->label(false) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-2">
            <?= Yii::t('app', 'Email') ?><span style="color: red">(*)</span>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'email')->textInput(['maxlength' => true])->label(false) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-2">
            <?= Yii::t('app', 'Số điện thoại') ?><span style="color: red">(*)</span>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'phone_number')->textInput(['maxlength' => true])->label(false) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-2">
            <?= Yii::t('app', 'Tên đơn vị đối tác') ?><span style="color: red">(*)</span>
        </div>
        <div class="col-md-6">
                <?= $form->field($model, 'full_name')->textInput(['readonly' => true])->label(false) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-2">
            <?= Yii::t('app', 'Mã đại lý của đối tác') ?><span style="color: red">(*)</span>
        </div>
        <div class="col-md-6">
                <?= $form->field($model, 'name_code')->textInput(['readonly' => true])->label(false) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-2">
            <?= Yii::t('app', 'Trạng thái hoạt động') ?><span style="color: red">(*)</span>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'status')->dropDownList(User::listStatus(),['disabled' => true])->label(false) ?>
    </div>
    <div class="row">
        <div class="col-md-12" style="text-align: center">
            <div class="form-group">
                    <?= Html::a(Yii::t('app', 'Đổi mật khẩu'), ['change-password'], ['class' => 'btn btn-info']) ?>
                    <?= Html::submitButton(Yii::t('app', 'Cập nhật thông tin'), ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
