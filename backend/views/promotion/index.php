<?php

use common\models\Promotion;
use common\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\PromotionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app','Danh sách chương trình ưu đãi');;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="promotion-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'panel' => [
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> ' . Html::encode($this->title) . ' </h3>',
            'type' => 'primary',
            'showFooter' => false
        ],
        'columns' => [
            [
                    'class' => 'yii\grid\SerialColumn',
                'header' => Yii::t('app','STT'),
            ],
            [
                'class' => '\kartik\grid\DataColumn',
                'header' => Yii::t('app','Tên đại lý'),
                'width'=>'20%',
                'attribute' => 'nameCp',
                'value' => function ($model, $key, $index, $widget) {
                    /**
                     * @var $model \common\models\Promotion
                     */
                    return $model->getNameCp();
                },
                'filter' => \common\models\Promotion::listDealer(),
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => "" . \Yii::t('app', 'Tất cả')],
            ],
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
        ],
    ]); ?>
</div>
