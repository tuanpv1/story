<?php

use common\models\Dealer;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Quản lý đại lý');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'panel' => [
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> ' . Html::encode($this->title) . ' </h3>',
            'type' => 'primary',
            'before' => Html::a('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('app', 'Tạo đại lý mới'), ['create'], ['class' => 'btn btn-success']),
//            'after' => Html::a('<i class="glyphicon glyphicon-repeat"></i> ' . Yii::t('app', 'Reset List'), ['index'], ['class' => 'btn btn-info']),
            'showFooter' => false
        ],
        'columns' => [
            [
                'attribute' => 'username',
                'label' => Yii::t('app', 'Tên truy cập'),
                'format' => 'raw',
                'value' => function ($model, $key, $index, $widget) {
                    /**
                     * @var $model \common\models\Dealer
                     */
                    return $model->getUserName($model->id);
                },
            ],
            [
                'attribute' => 'name_code',
                'format' => 'raw',
                'value' => function ($model, $key, $index, $widget) {
                    /**
                     * @var $model \common\models\Dealer
                     */
                    return $model->name_code;
                },
            ],
            [
                'attribute' => 'email',
                'format' => 'raw',
                'value' => function ($model, $key, $index, $widget) {
                    /**
                     * @var $model \common\models\Dealer
                     */
                    return $model->email;
                },
            ],
            [
                'attribute' => 'phone_number',
                'format' => 'raw',
                'value' => function ($model, $key, $index, $widget) {
                    /**
                     * @var $model \common\models\Dealer
                     */
                    return $model->phone_number;
                },
            ],
            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'status',
                'width' => '15%',
                'format' => 'raw',
                'value' => function ($model, $key, $index, $widget) {
                    /**
                     * @var $model \common\models\Dealer
                     */
                    return $model->getStatusName();
                },
                'filter' => Dealer::listStatus(),
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => "" . \Yii::t('app', 'Tất cả')],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}{update}',
                'header' => Yii::t('app', 'Hành động'),
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::toRoute(['cp-users/view', 'id' => $model->id]), [
                            'title' => '' . \Yii::t('app', 'Thông tin dại lý'),
                        ]);
                    },
                    'update' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::toRoute(['cp-users/update', 'id' => $model->id]), [
                            'title' => '' . \Yii::t('app', 'Cập nhật thông tin đại lý'),
                        ]);
                    },
//                    'delete' => function ($url, $model) {
//                        if ($model->status == Dealer::STATUS_INACTIVE) {
//                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::toRoute(['cp-users/delete', 'id' => $model->id]), [
//                                'title' => '' . \Yii::t('app', 'Xóa đại lý'),
//                                'data-confirm' => Yii::t('app', 'Bạn có chắc chắn muốn xóa đại lý ') . $model->getUserName($model->id) . Yii::t('app', ' ?'),
//                            ]);
//                        }
//
//                    }
                ]
            ],
        ],
    ]); ?>
</div>
