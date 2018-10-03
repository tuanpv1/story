<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\PromotionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app','Danh sách chương trình ưu đãi');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="promotion-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'panel' => [
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> ' . Html::encode($this->title) . ' </h3>',
            'type' => 'primary',
            'before' => Html::a('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('app', 'Tạo đại chương trình ưu đãi mới'), ['create'], ['class' => 'btn btn-success']),
//            'after' => Html::a('<i class="glyphicon glyphicon-repeat"></i> ' . Yii::t('app', 'Reset List'), ['index'], ['class' => 'btn btn-info']),
            'showFooter' => false
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'name',
                'format' => 'raw',
                'width'=>'20%',
                'value' => function ($model, $key, $index, $widget) {
                    /**
                     * @var $model \common\models\Promotion
                     */

                    return $model->name;
                },
                'filterInputOptions' => ['placeholder' => Yii::t('app', 'Search'),'class' => 'form-control input-sm'],
            ],
            'total_promotion_code',
            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'created_at',
                'value' => function ($model, $key, $index, $widget) {
                    /**
                     * @var $model \common\models\Promotion
                     */
                    return date('d-m-Y H:i', $model->created_at);
                },
            ],
            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'active_time',
                'value' => function ($model, $key, $index, $widget) {
                    /**
                     * @var $model \common\models\Promotion
                     */
                    return date('d-m-Y H:i', $model->active_time);
                },
            ],
            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'expired_time',
                'value' => function ($model, $key, $index, $widget) {
                    /**
                     * @var $model \common\models\Promotion
                     */
                    return date('d-m-Y H:i', $model->expired_time);
                },
            ],
            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'status',
                'width' => '15%',
                'format' => 'raw',
                'value' => function ($model, $key, $index, $widget) {
                    /**
                     * @var $model \common\models\Promotion
                     */
                    if ($model->status == \common\models\Promotion::STATUS_ACTIVE) {
                        return '<span class="label label-success">' . $model->getStatusName() . '</span>';
                    } else {
                        return '<span class="label label-danger">' . $model->getStatusName() . '</span>';
                    }
                },
                'filter' => \common\models\Promotion::listStatus(),
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => "" . \Yii::t('app', 'Tất cả')],
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}{update}{delete}{genCode}',
                'header' => Yii::t('app', 'Hành động'),
                'buttons' => [
                    'genCode' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-triangle-bottom"></span>', Url::to(['promotion-code/gen-code', 'id' => $model->id]), [
                            'title' => '' . \Yii::t('app', 'Gen mã'),
                        ]);
                    },
                ]
            ],
        ],
    ]); ?>
</div>
