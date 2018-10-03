<?php

use common\models\CpOrder;
use common\models\CpOrderAsm;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\CpOrderAsm */

$this->title = Yii::t('app','Thông tin đơn hàng: ');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Quản lý đơn hàng'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="panel panel-success">
    <div class="panel-heading text-center"><h3 class="panel-title"><?= Yii::t('app','Thông tin chi tiết đơn hàng') ?></h3></div>
    <div class="box ">
        <div class="box-body">
            <div class="col-md-offset-2 col-md-8">
                <?php
                $column = [
                    [
                        'attribute'=>'dealer_id',
                        'format'=>'raw',
                        'value'=> $model->getCpName($model->dealer_id),
                    ],
                    [
                        'attribute'=>'cp_order_id',
                        'format'=>'raw',
                        'value'=>$model->getCpOrderName($model->cp_order_id),
                    ]
                ];
                if ($model_attribute) {
                    /** @var \common\models\Attribute $item */
                    foreach ($model_attribute as $item) {
                        $column[] = [
                            'label' => $item->name,
                            'value'=> CpOrder::getAttributeValue($item->id,$model->cp_order_id),
                        ];
                    }
                }
                $column[] =
                    [
                        'attribute'=>'status',
                        'format'=>'raw',
                        'value'=>($model->status == CpOrderAsm::STATUS_ACTIVE)  ?
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
                            <?= Html::button(Yii::t('app','Quay lại'), [
                                'class' => 'btn btn-danger',
                                'onclick'=>"document.location.href='".Yii::$app->request->referrer."'",
                            ]) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
