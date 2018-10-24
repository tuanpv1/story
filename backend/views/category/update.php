<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Category */

$this->title = \Yii::t('app', 'Cập nhật danh mục');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => Yii::$app->urlManager->createUrl(['/category/index'])];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="panel panel-success">
    <div class="portlet-title">
        <div class="panel-heading text-center"><h3
                    class="panel-title"><?= $this->title ?></h3></div>
    </div>
    <div class="portlet-body form">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>