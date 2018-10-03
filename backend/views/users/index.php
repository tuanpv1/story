<?php

use common\auth\filters\Yii2Auth;
use common\models\User;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Quản lý tài khoản');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'panel' => [
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> ' . Yii::t('app', 'Danh sách tài khoản') . ' </h3>',
            'type' => 'primary',
            'before' => Html::a('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('app', 'Tạo tài khoản mới'), ['create'], ['class' => 'btn btn-success']),
//            'after' => Html::a('<i class="glyphicon glyphicon-repeat"></i> ' . Yii::t('app', 'Reset List'), ['index'], ['class' => 'btn btn-info']),
            'showFooter' => false
        ],
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'header' => Yii::t('app', 'STT'),
            ],
            [
                'attribute' => 'username',
                'format' => 'raw',
                'width' => '30%',
                'value' => function ($model, $key, $index, $widget) {
                    /**
                     * @var $model \common\models\User
                     */
                    $action = "users/view";
                    $res = Html::a('<kbd>' . $model->username . '</kbd>', [$action, 'id' => $model->id]);
                    return $res;
                },
            ],
//            [
//                'attribute' => 'type',
//                'format' => 'raw',
//                'label' => Yii::t('app', 'Quyền'),
//                'width' => '20%',
//                'value' => function ($model, $key, $index, $widget) {
//                    /**
//                     * @var $model \common\models\User
//                     */
//                    return $model->getTypeName();
//                },
//                'filter' => User::listType(),
//                'filterType' => GridView::FILTER_SELECT2,
//                'filterWidgetOptions' => [
//                    'pluginOptions' => ['allowClear' => true],
//                ],
//                'filterInputOptions' => ['placeholder' => "" . \Yii::t('app', 'Tất cả')],
//            ],
            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'status',
                'width' => '20%',
                'format' => 'raw',
                'value' => function ($model, $key, $index, $widget) {
                    /**
                     * @var $model \common\models\User
                     */
                    if ($model->status == User::STATUS_ACTIVE) {
                        return '<span class="label label-success">' . $model->getStatusName() . '</span>';
                    } else {
                        return '<span class="label label-danger">' . $model->getStatusName() . '</span>';
                    }

                },
                'filter' => User::listStatus(),
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => "" . \Yii::t('app', 'Tất cả')],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}{update}{delete}',
                'header' => Yii::t('app', 'Hành động'),
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::toRoute(['users/view', 'id' => $model->id]), [
                            'title' => '' . \Yii::t('app', 'Thông tin user'),
                        ]);
                    },
                    'update' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::toRoute(['users/update', 'id' => $model->id]), [
                            'title' => '' . \Yii::t('app', 'Cập nhật thông tin user'),
                        ]);
                    },
                    'delete' => function ($url, $model) {
//                  Nếu là chính nó thì không cho xoá
                        if ($model->id != Yii::$app->user->getId()) {
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::toRoute(['users/delete', 'id' => $model->id]), [
                                'title' => '' . \Yii::t('app', 'Xóa user'),
                                'data-confirm' => Yii::t('app', 'Bạn có chắc chắn muốn xóa tài khoản ') . $model->username . Yii::t('app', ' ?'),
                            ]);
                        }
                    }
                ]
            ],
        ],
    ]); ?>
</div>
