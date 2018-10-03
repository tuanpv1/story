<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\editable\Editable;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = ''.\Yii::t('app', 'Quản lý nhóm quyền trang backend');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <?= GridView::widget([
        'id' => 'rbac-role',
        'dataProvider' => $dataProvider,
        'responsive' => true,
        'pjax' => false,
        'hover' => true,
        'panel' => [
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> ' . Yii::t('app', 'Danh sách nhóm quyền') . ' </h3>',
            'type' => 'primary',
            'before' => Html::a('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('app', 'Tạo nhóm quyền'), ['create-role'], ['class' => 'btn btn-success']),
//            'after' => Html::a('<i class="glyphicon glyphicon-repeat"></i> ' . Yii::t('app', 'Reset List'), ['index'], ['class' => 'btn btn-info']),
            'showFooter' => false
        ],
        'columns' => [
            [
                'class' => 'kartik\grid\SerialColumn',
                'header' => Yii::t('app', 'STT'),
            ],
            [
                'attribute' => 'name',
                'label'=>Yii::t('app','Nhóm quyền'),
                'class' => 'yii\grid\DataColumn',
//                'vAlign' => 'middle',
                'format' => 'raw',
//                'noWrap' => true,
                'value' => function ($model, $key, $index, $widget) {
                    /**
                     * @var $model \common\models\AuthItem
                     */
                    $res = $model->name;
                    $res .= " [". sizeof($model->children) . "]";
                    return $res;
                },
            ],
            'description',
//            'data',
//            'type',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{set-password} {view} {update} {delete}',
                'header' => Yii::t('app', 'Action'),
                'buttons'=> [
                    'view' => function ($url, $model, $key) {
                        return   Html::a('<span class="glyphicon glyphicon-eye-open"></span>',
                            Yii::$app->urlManager->createUrl(['rbac-backend/view-role','name'=>$model->name]),[
                            'title' => Yii::t('yii', 'View'),
                            'data-pjax' => '0',
                        ]) ;
                    },
                    'update' => function ($url, $model, $key) {
                        return   Html::a('<span class="glyphicon glyphicon-pencil"></span>',
                            Yii::$app->urlManager->createUrl(['rbac-backend/update-role','name'=>$model->name]),[
                                'title' => Yii::t('yii', 'Update'),
                                'data-pjax' => '0',
                            ]) ;
                    },
                   'delete' => function ($url, $model) {
                       return Html::a('<span class="glyphicon glyphicon-trash"></span>', Yii::$app->urlManager->createUrl(['rbac-backend/delete-role','name'=>$model->name]), [
                           'title' => Yii::t('yii', 'Delete'),
                           'data-confirm' => Yii::t('yii', 'Bạn có chắc chắn xóa nhóm quyền này?'),
                           'data-method' => 'post',
                           'data-pjax' => '0',
                       ]);
                   }
                 ],
            ],
        ],
    ]); ?>

</div>


