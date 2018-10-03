<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = Yii::t('app','Log In');
$this->registerJs("jQuery('#reveal-password').change(function(){jQuery('#loginform-password').attr('type',this.checked?'text':'password');})");


$fieldOptions1 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}"
];

$fieldOptions2 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}"
];
?>

<div class="login-box" style="width: 500px">
    <div class="login-logo">
        <a href="#"><b><?=Yii::t('app','VIETTALK PROMOTION')?></b></a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg" style="font-size: xx-large"><?=Yii::t('app','Đăng Nhập')?></p>

        <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>

        <div class="row">
            <div class="col-md-4">
                <?= Yii::t('app','Tên đăng nhập')?>
            </div>
            <div class="col-md-8">
                <?= $form
                    ->field($model, 'username', $fieldOptions1)
                    ->label(false)
                    ->textInput(['placeholder' => $model->getAttributeLabel('username')]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4" style="margin-top: 25px;">
                <?= Yii::t('app','Mật Khẩu')?>
            </div>
            <div class="col-md-8">
                <?= \nsept\passfield\PassfieldWidget::widget([
                    'form'      => $form,
                    'model'     => $model,
                    'attribute' => 'password',
                    'pluginOptions' => [
                        // Custom icons
                        'iconShow' => '<i class="glyphicon glyphicon-eye-open"></i>',
                        'iconHide' => '<i class="glyphicon glyphicon-eye-close"></i>',
                        // Custom titles
                        'titleShow' => 'Show',
                        'titleHide' => 'Hide'
                    ]
                ]) ?>
            </div>
        </div>


        <div class="row" style="margin-top: 10px">
            <div class="col-xs-4">
                <!-- <?//= $form->field($model, 'rememberMe')->checkbox() ?>-->
            </div>
            <!-- /.col -->
            <div class="col-xs-4">
                <?= Html::submitButton(Yii::t('app','Đăng nhập'), ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'login-button']) ?>


            </div>
            <div class="col-xs-4">
                <?= Html::Button(Yii::t('app','Hủy'), ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'cancel-button']) ?>
            </div>
            <!-- /.col -->
        </div>


        <div class="row" style="text-align: center;margin-top: 20px">
            <a href="<?= \yii\helpers\Url::to(['site/request-password-reset']) ?>"><b><?=Yii::t('app','Quên mật khẩu')?></b></a>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
    <!-- /.login-box-body -->
</div><!-- /.login-box -->
