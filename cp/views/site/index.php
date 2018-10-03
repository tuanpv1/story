<?php

/* @var $this yii\web\View */

use common\auth\filters\Yii2Auth;
use common\models\User;
use kartik\detail\DetailView;
use kartik\form\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = "";
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="panel panel-success">
    <div class="panel-heading text-center"><?=Yii::t("app","Thông tin tài khoản")?></div>
    <div class="box ">
        <div class="box-body">
            <div class="user-create">
                <div class="col-md-offset-2 col-md-8">
                    <?php $form = ActiveForm::begin(
                        ['method' => 'get',
                            'action' => Url::to(['users/info']),]
                    ); ?>
                    <div class="row">
                        <div class="col-md-2">
                            <?= Yii::t('app','Tên đăng nhập')?>
                        </div>
                        <div class="col-md-10">
                            <?= $form->field($model, 'username')->textInput(['readonly'=>true])->label(false) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <?= Yii::t('app','Email')?>
                        </div>
                        <div class="col-md-10">
                            <?= $form->field($model, 'email')->textInput(['readonly'=>true])->label(false) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <?= Yii::t('app','Số điện thoại')?>
                        </div>
                        <div class="col-md-10">
                            <?= $form->field($model, 'phone')->textInput(['readonly'=>true])->label(false) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <?= Yii::t('app','Tên đầy đủ')?>
                        </div>
                        <div class="col-md-10">
                            <?= $form->field($model, 'full_name')->textInput(['readonly'=>true])->label(false) ?>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 15px">
                        <div class="col-md-2">
                            <?= Yii::t('app','Trạng thái hoạt động')?>
                        </div>
                        <div class="col-md-10">
                            <?= $form->field($model, 'status')->dropDownList(User::listStatus(),['disabled'=>true])->label(false)?>
                        </div>
                    </div>
                </div>
                <div class="form-actions">
                    <div class="row">
                        <div class="col-md-offset-5 col-md-8">
                            <?= Html::a(Yii::t('app','Cập nhật thông tin'), ['cp-users/update', 'id' => $model->dealer_id], ['class' => 'btn btn-primary']) ?>
                        </div>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

