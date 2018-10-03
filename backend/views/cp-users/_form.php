<?php

use common\models\User;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Dealer */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin([
        'id' => 'form_create_user_admin',
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
    ]); ?>
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-2">
            <?= Yii::t('app', 'Tên truy cập') ?><span style="color: red">(*)</span>
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
            <?php if ($model->isNewRecord) { ?>
                <?= $form->field($model, 'full_name')->textInput(['placeholder' => Yii::t('app', '3-100 ký tự'), 'maxlength' => 100])->label(false) ?>
            <?php } else { ?>
                <?= $form->field($model, 'full_name')->textInput(['readonly' => true])->label(false) ?>
            <?php } ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-2">
            <?= Yii::t('app', 'Mã đại lý của đối tác') ?><span style="color: red">(*)</span>
        </div>
        <div class="col-md-6">
            <?php if ($model->isNewRecord) { ?>
                <?= $form->field($model, 'name_code')->textInput(['placeholder' => Yii::t('app', 'Tên viết tắt gồm 3 ký tự'), 'maxlength' => 3])->label(false) ?>
            <?php } else { ?>
                <?= $form->field($model, 'name_code')->textInput(['readonly' => true])->label(false) ?>
            <?php } ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-2">
            <?= Yii::t('app', 'Trạng thái hoạt động') ?><span style="color: red">(*)</span>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'status')->dropDownList(User::listStatus())->label(false) ?>
        </div>
        <div class="row">
            <div class="col-md-12" style="text-align: center">
                <div class="form-group">
                    <?php if ($model->isNewRecord) { ?>
                        <?= Html::submitButton(Yii::t('app', 'Tạo tài khoản'), ['class' => 'btn btn-success']) ?>
                        <?= Html::resetButton(Yii::t('app', 'Nhập lại'), ['class' => 'btn btn-warning']) ?>
                    <?php } else { ?>
                        <?= Html::a(Yii::t('app', 'Reset mật khẩu'), ['reset-password', 'id' => $model->id], ['class' => 'btn btn-info']) ?>
                        <?= Html::submitButton(Yii::t('app', 'Cập nhật thông tin'), ['class' => 'btn btn-success']) ?>
                    <?php } ?>
                </div>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
