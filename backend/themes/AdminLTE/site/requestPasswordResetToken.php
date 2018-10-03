<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \backend\models\PasswordResetRequestForm */

$this->title = Yii::t('app', 'Quên mật khẩu');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="login-box" style="width: 500px">
    <div class="login-logo">
        <a href="#"><b><?= Yii::t('app', 'VIETTALK PROMOTION') ?></b></a>
    </div>
    <div class="login-box-body">
        <div class="site-request-password-reset">
            <h1 class="text-center"><?= Html::encode($this->title) ?></h1>

            <p><?= Yii::t('app', 'Vui lòng nhập email mà bạn đã dùng để đăng kí tài khoản. Chúng tôi sẽ gửi lại mật khẩu mới vào email của bạn!') ?></p>

            <div class="row">
                <?php $form = ActiveForm::begin([
                    'id' => 'request-password-reset-form',
                ]); ?>
                <?= $form->field($model, 'email') ?>
                <div class="form-group text-center">
                    <?= Html::submitButton('Nhận mật khẩu mới', ['class' => 'btn btn-success']) ?>
                    <?= Html::a('Quay lại', ['site/login'], ['class' => 'btn btn-primary']) ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

