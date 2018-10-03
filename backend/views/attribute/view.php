<?php

use common\models\Attribute;
use common\models\User;
use kartik\form\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Attribute */

$this->title = Yii::t('app','Thông tin thuộc tính: ') . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Quản lý thuộc tính khuyến mại'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="panel panel-success">
    <div class="panel-heading text-center"><h3 class="panel-title"><?= Yii::t('app','Thông tin chi tiết thuộc tính') ?></h3></div>
    <div class="box ">
        <div class="box-body">
            <div class="col-md-offset-2 col-md-8">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'name',
                        [
                            'attribute'=>'status',
                            'format'=>'raw',
                            'value'=>($model->status == Attribute::STATUS_ACTIVE)  ?
                                '<span class="label label-success">'.$model->getStatusName().'</span>' :
                                '<span class="label label-danger">'.$model->getStatusName().'</span>',
                        ],
                    ],
                ]) ?>
                <div class="form-actions">
                    <div class="row">
                        <div class="col-md-offset-5 col-md-8">
                            <?= Html::a(Yii::t('app','Cập nhật thông tin'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
