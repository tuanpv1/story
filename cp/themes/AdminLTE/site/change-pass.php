<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = Yii::t('app','Đổi mật khẩu');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">
    <div class="panel panel-success">
        <div class="panel-heading text-center"><h3 class="panel-title"><?= $this->title ?></h3></div>
        <div class="box ">
            <div class="box-body">
                <?= $this->render('_form_password', [
                    'model' => $model,
                ]) ?>
            </div>
        </div>
    </div>
</div>
