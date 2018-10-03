<?php

use common\auth\filters\Yii2Auth;
use common\models\Attribute;
use common\models\CpOrder;
use common\models\CpOrderAsm;
use common\models\User;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\AttributeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app','Quản lý đơn hàng');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">
    <?php
    $gridColumn = [
        [
            'class' => 'yii\grid\SerialColumn',
            'header' => Yii::t('app','STT'),
        ],
        [
            'attribute' => 'dealer_id',
            'format' => 'raw',
            'width'=>'20%',
            'value' => function ($model, $key, $index, $widget) {
                /**
                 * @var $model \common\models\CpOrderAsm
                 */
                return $model->getCpName($model->dealer_id);
            },
            'filterInputOptions' => ['placeholder' => Yii::t('app', 'Search'),'class' => 'form-control input-sm'],
        ],
        [
            'attribute' => 'cp_order_id',
            'format' => 'raw',
            'width'=>'20%',
            'value' => function ($model, $key, $index, $widget) {
                /**
                 * @var $model \common\models\CpOrderAsm
                 */
                return $model->getCpOrderName($model->cp_order_id);
            },
            'filterInputOptions' => ['placeholder' => Yii::t('app', 'Search'),'class' => 'form-control input-sm'],
        ],
    ];
    if ($model_attribute) {
        /** @var \common\models\Attribute $item */
        foreach ($model_attribute as $item) {
            $gridColumn[] = [
                'attribute' => $item->id,
                'label' => $item->name,
                'width'=>'10%',
                'content'=>function($model, $key, $index, $column){
                    /** @var $model \common\models\CpOrderAsm */
                    return CpOrder::getAttributeValue($column->attribute,$model->cp_order_id);
                },
            ];
        }
    }
    $gridColumn[]=
        [
            'attribute' => 'transaction_time',
            'width' => '20%',
            'filterType' => GridView::FILTER_DATE,
            'filterWidgetOptions' => [
                'pluginOptions'=>[
                    'format' => 'dd-mm-yyyy',
                ]
            ],
            'value' => function($model){
                return date('d-m-Y', $model->transaction_time);
            },
        ];
    $gridColumn[] =
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute'=>'status',
            'width'=>'15%',
            'format'=>'raw',
            'value' => function ($model, $key, $index, $widget) {
                /**
                 * @var $model \common\models\CpOrderAsm
                 */
                    return $model->getStatusName();
            },
            'filter' => CpOrderAsm::listStatus(),
            'filterType' => GridView::FILTER_SELECT2,
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => "".\Yii::t('app', 'Tất cả')],
        ];
    $gridColumn[]=
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{view}{update}',
            'header' => Yii::t('app', 'Hành động'),
            'buttons' => [
                'view' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::toRoute(['view', 'id' => $model->id]), [
                        'title' => '' . \Yii::t('app', 'Chi tiết đơn hàng'),
                    ]);
                },
                'update' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::toRoute(['update', 'id' => $model->id]), [
                        'title' => '' . \Yii::t('app', 'Cập nhật trạng thái đơn hàng'),
                    ]);
                },
            ]
        ];
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'panel' => [
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> ' . Yii::t('app','Danh sách đơn hàng') . ' </h3>',
            'type' => 'primary',
            'before' => Html::a('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('app', 'Tạo đơn hàng cho đại lý'), ['create'], ['class' => 'btn btn-success']),
//            'after' => Html::a('<i class="glyphicon glyphicon-repeat"></i> ' . Yii::t('app', 'Reset List'), ['index'], ['class' => 'btn btn-info']),
            'showFooter' => false
        ],
        'columns' => $gridColumn,
    ]); ?>
</div>
