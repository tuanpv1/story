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
    <div class="panel-heading text-center"><h3 class="panel-title"><?= Yii::t('app','Thông tin chi tiết tài khoản') ?></h3></div>
    <div class="box ">
        <div class="box-body">
            <ul class="nav nav-tabs ">
                <li class="<?= ($active == 1) ? 'active' : '' ?>">
                    <a href="#tab1" data-toggle="tab" >
                        <?= \Yii::t('app', 'Thông tin chung') ?></a>
                </li>
                <li class=" <?= ($active == 2) ? 'active' : '' ?>">
                    <a href="#tab2" data-toggle="tab" >
                        <?= \Yii::t('app', 'Phân quyền') ?> </a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane <?= ($active == 1) ? 'active' : '' ?>" id="tab1">
                    <?=$this->render('_detail',['model'=>$model])?>
                </div>
                <div class="tab-pane <?= ($active == 2) ? 'active' : '' ?>" id="tab2">
                    <?=$this->render('_user_role',['model'=>$model])?>
                </div>
            </div>
        </div>
    </div>
</div>
