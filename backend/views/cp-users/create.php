<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = Yii::t('app','Tạo đại lý');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Quản lý đại lý'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="course-create">
    <div class="panel panel-success">
        <div class="panel-heading"><h3 class="panel-title"><?= $this->title ?></h3></div>
        <div class="box ">

            <div class="box-body">
                <?= $this->render('_form', [
                    'model' => $model,
                    'model_attribute' => $model_attribute,
                ]) ?>
            </div>
        </div>
    </div>
</div>