<?php

use common\auth\filters\Yii2Auth;
use common\models\Attribute;
use common\models\User;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\AttributeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app','Quản lý thuộc tính khuyến mại');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'panel' => [
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> ' .  Yii::t('app','Danh sách thuộc tính khuyến mại') . ' </h3>',
            'type' => 'primary',
            'before'=>Html::a('<i class="glyphicon glyphicon-plus"></i> '.Yii::t('app','Tạo thuộc tính mới'), ['create'], ['class' => 'btn btn-success']),
//            'after' => Html::a('<i class="glyphicon glyphicon-repeat"></i> ' . Yii::t('app', 'Reset List'), ['index'], ['class' => 'btn btn-info']),
            'showFooter' => false
        ],
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'header' => Yii::t('app','STT'),
            ],
            [
                'attribute' => 'name',
                'format' => 'raw',
                'width'=>'30%',
                'value' => function ($model, $key, $index, $widget) {
                    /**
                     * @var $model \common\models\Attribute
                     */
                    $action = "attribute/view";
                    $res = Html::a('<kbd>'.$model->name.'</kbd>', [$action, 'id' => $model->id ]);
                    return $res;
                },
            ],
            [
                'class' => '\kartik\grid\DataColumn',
                'attribute'=>'status',
                'width'=>'20%',
                'format'=>'raw',
                'value' => function ($model, $key, $index, $widget) {
                    /**
                     * @var $model \common\models\Attribute
                     */
                    if($model->status == Attribute::STATUS_ACTIVE){
                        return '<span class="label label-success">'.$model->getStatusName().'</span>';
                    }else{
                        return '<span class="label label-danger">'.$model->getStatusName().'</span>';
                    }
                },
                'filter' => Attribute::listStatus(),
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => "".\Yii::t('app', 'Tất cả')],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{view}{update}{delete}',
                'header'=>Yii::t('app','Hành động'),
                'buttons'=>[
                'view' => function ($url,$model) {
                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::toRoute(['view','id'=>$model->id]), [
                    'title' => ''.\Yii::t('app', 'Thông tin thuộc tính'),
                    ]);
                },
                'update' => function ($url,$model) {
                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::toRoute(['update','id'=>$model->id]), [
                    'title' => ''.\Yii::t('app', 'Cập nhật thông tin'),
                    ]);
                },
                'delete' => function ($url,$model) {
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::toRoute(['delete','id'=>$model->id]), [
                    'title' => ''.\Yii::t('app', 'Xóa thuộc tính'),
                    'data-confirm' => Yii::t('app', 'Bạn có chắc chắn muốn xóa thuộc tính ').$model->name.Yii::t('app',' ?'),
                ]);
        }
    ]
            ],
        ],
    ]); ?>
</div>
