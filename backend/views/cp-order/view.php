<?php

use common\models\CpOrder;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\CpOrder */

$this->title = Yii::t('app','Thông tin template đơn hàng: ') . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Quản lý template đơn hàng'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="panel panel-success">
    <div class="panel-heading text-center"><h3 class="panel-title"><?= Yii::t('app','Thông tin chi tiết template đơn hàng') ?></h3></div>
    <div class="box ">
        <div class="box-body">
            <div class="col-md-offset-2 col-md-8">
                <?php
                $column[] = 'name';
                if ($model_attribute) {
                    /** @var \common\models\Attribute $item */
                    foreach ($model_attribute as $item) {
                        $column[] = [
                            'label' => $item->name,
                            'value'=> $model->getAttributeValue($item->id,$model->id),
                        ];
                    }
                }
                $column[]=
                    [
                        'attribute' => 'created_at',
                        'value' => function($model){
                            return date('d-m-Y', $model->created_at);
                        },
                    ];
                $column[] =
                    [
                        'attribute'=>'status',
                        'format'=>'raw',
                        'value'=>($model->status == CpOrder::STATUS_ACTIVE)  ?
                            '<span class="label label-success">'.$model->getStatusName().'</span>' :
                            '<span class="label label-danger">'.$model->getStatusName().'</span>',
                    ];
                ?>
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => $column,
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
