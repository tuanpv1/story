<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\AuthItem */

$this->title = '' . \Yii::t('app', 'Tạo nhóm quyền');
$this->params['breadcrumbs'][] = ['label' => '' . \Yii::t('app', 'Quản lý nhóm quyền'), 'url' => ['role']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-create">
    <div class="panel panel-success">
        <div class="panel-heading text-center"><h3 class="panel-title"><?= $this->title ?></h3></div>
        <div class="box ">
            <div class="box-body" style="margin: 25px 250px auto 250px">
                <?= $this->render('_form-role', [
                    'model' => $model,
                ]) ?>
            </div>
        </div>
    </div>
</div>
