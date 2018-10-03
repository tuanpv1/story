<?php

use common\auth\filters\Yii2Auth;
use common\models\Attribute;
use common\models\CpOrder;
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
            'attribute' => 'name',
            'format' => 'raw',
            'width'=>'20%',
            'value' => function ($model, $key, $index, $widget) {
                /**
                 * @var $model \common\models\Attribute
                 */
                $action = "cp-order/view";
                $res = Html::a('<kbd>'.$model->name.'</kbd>', [$action, 'id' => $model->id ]);
                return $res;
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
                    'width'=>'15%',
                    'content'=>function($model, $key, $index, $column){
                        /** @var $model \common\models\CpOrder */
                        return $model->getAttributeValue($column->attribute,$model->id);
                    },
                ];
            }
    }
    $gridColumn[]=
        [
            'attribute' => 'created_at',
            'width' => '20%',
            'filterType' => GridView::FILTER_DATE,
            'filterWidgetOptions' => [
                'pluginOptions'=>[
                    'format' => 'dd-mm-yyyy',
                ]
            ],
            'value' => function($model){
                return date('d-m-Y', $model->created_at);
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
                 * @var $model \common\models\Attribute
                 */
                if($model->status == CpOrder::STATUS_ACTIVE){
                    return '<span class="label label-success">'.$model->getStatusName().'</span>';
                }else{
                    return '<span class="label label-danger">'.$model->getStatusName().'</span>';
                }
            },
            'filter' => CpOrder::listStatus(),
            'filterType' => GridView::FILTER_SELECT2,
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => "".\Yii::t('app', 'Tất cả')],
        ];
    $gridColumn[]=
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{view}{update}{delete}',
            'header' => Yii::t('app', 'Hành động'),
            'buttons' => [
                'view' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::toRoute(['view', 'id' => $model->id]), [
                        'title' => '' . \Yii::t('app', 'Chi tiết template đơn hàng'),
                    ]);
                },
                'update' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::toRoute(['update', 'id' => $model->id]), [
                        'title' => '' . \Yii::t('app', 'Cập nhật template đơn hàng'),
                    ]);
                },
                'delete' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::toRoute(['delete', 'id' => $model->id]), [
                        'title' => '' . \Yii::t('app', 'Xóa template đơn hàng'),
                        'data-confirm' => Yii::t('app', 'Bạn có chắc chắn muốn xóa template đơn hàng ') . $model->name . Yii::t('app', ' ?'),
                    ]);
                }
            ]
        ];
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'panel' => [
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> ' .  Yii::t('app','Danh sách template đơn hàng') . ' </h3>',
            'type' => 'primary',
            'before'=>Html::a('<i class="glyphicon glyphicon-plus"></i> '.Yii::t('app','Tạo template đơn hàng mới'), ['create'], ['class' => 'btn btn-success']),
//            'after' => Html::a('<i class="glyphicon glyphicon-repeat"></i> ' . Yii::t('app', 'Reset List'), ['index'], ['class' => 'btn btn-info']),
            'showFooter' => false
        ],
        'columns' =>$gridColumn ,
    ]); ?>
</div>
