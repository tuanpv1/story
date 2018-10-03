<?php

use common\models\User;
use kartik\form\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = Yii::t('app','Thông tin tài khoản: ') . $model->username;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Quản lý tài khoản'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="panel panel-success">
    <div class="panel-heading text-center"><?=Yii::t("app","Tìm kiếm")?></div>
    <div class="box ">
        <div class="box-body">
            <div class="row">
                <?php $form = ActiveForm::begin(
                    ['method' => 'get',
                        'action' => Url::to(['users/info']),]
                ); ?>
                <div class="col-md-4" style="text-align: right">
                    <?=Yii::t('app','Tên truy cập')?>
                </div>
                <div class="col-md-4">
                    <?php if(Yii::$app->user->identity->getId() == 1){ ?>
                        <?= $form->field($user, 'username')->dropDownList( ArrayHelper::merge([],User::listUser()), ['id'=>'id'])->label(false); ?>
                    <?php }else{ ?>
                        <?= $form->field($user, 'username')->dropDownList( ArrayHelper::merge([],User::listUser()), ['id'=>'id','disabled'=>true])->label(false); ?>
                    <?php } ?>

                </div>
                <div class="col-md-4">
                    <?php if(Yii::$app->user->identity->getId() == 1){ ?>
                        <?= \yii\helpers\Html::submitButton(''.\Yii::t('app', 'Tra cứu'), ['class' => 'btn btn-primary']) ?>
                    <?php }else{ ?>
                        <?= \yii\helpers\Html::submitButton(''.\Yii::t('app', 'Tra cứu'), ['class' => 'btn btn-primary','disabled'=>true]) ?>
                    <?php } ?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
        <div class="panel panel-success">
            <div class="panel-heading text-center"><?=Yii::t("app","Thông tin tài khoản")?></div>
            <div class="box ">
                <div class="box-body">
                    <div class="user-create">
                        <div class="col-md-offset-2 col-md-8">
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
                            <div class="row">
                                <div class="col-md-2">
                                    <?= Yii::t('app','Quyền')?>
                                </div>
                                <div class="col-md-10">
                                    <?= $form->field($model, 'type')->dropDownList(User::listType(),['disabled'=>true])->label(false)?>
                                </div>
                            </div>
                            <div class="row">
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
                                    <?= Html::a(Yii::t('app','Cập nhật thông tin'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                                    <?= Html::button(Yii::t('app','Quay lại'), [
                                        'class' => 'btn btn-danger',
                                        'onclick'=>"document.location.href='".Yii::$app->request->referrer."'",
//                                        'data' => [
//                                            'confirm' => 'Are you sure you want to delete this item?',
//                                            'method' => 'post',
//                                        ],
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
