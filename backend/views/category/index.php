<?php

use common\models\Category;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\CategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = \Yii::t('app', 'Quản lý danh mục ');
$this->params['breadcrumbs'][] = $this->title;
?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'id' => 'grid-category-id',
    'filterModel' => $searchModel,
    'panel' => [
        'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> ' . $this->title . ' </h3>',
        'type' => 'primary',
        'before' => Html::a('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('app', 'Tạo tài danh mục mới'), ['create'], ['class' => 'btn btn-success']),
        'showFooter' => false
    ],
    'responsive' => true,
    'pjax' => true,
    'hover' => true,
    'columns' => [
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute' => 'path_name',
            'label' => \Yii::t('app', 'Tên danh mục'),
            'value' => function ($model, $key, $index, $widget) {
                /** @var $model \common\models\Category */
                return $model->path_name ? $model->path_name : $model->display_name;
            },
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'format' => 'raw',
            'attribute' => 'images',
            'value' => function ($model, $key, $index, $widget) {
                /** @var $model \common\models\Category */
                $cat_image = Yii::getAlias('@cat_image');
                return $model->images ? Html::img('@web/' . $cat_image . '/' . $model->images, ['alt' => 'Thumbnail', 'width' => '50', 'height' => '50']) : '';
            },
            'filter' => false
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'status',
            'refreshGrid' => true,
            'editableOptions' => function ($model, $key, $index) {
                return [
                    'header' => \Yii::t('app', 'Trạng thái'),
                    'size' => 'md',
                    'displayValueConfig' => $model->listStatus,
                    'inputType' => \kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                    'data' => $model->listStatus,
                    'placement' => \kartik\popover\PopoverX::ALIGN_LEFT
                ];
            },
            'filterType' => GridView::FILTER_SELECT2,
            'filter' => Category::getListStatus(),
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => \Yii::t('app', 'Tất cả')],
        ],
        [

            'format' => 'raw',
            'attribute' => 'order_number',
            'value' => function ($model, $key, $index, $widget) {
                /** @var $model \common\models\Category */
                $up = Html::tag('i', '', ['class' => 'glyphicon glyphicon-arrow-up text-green', 'onclick' => "js:moveCategory(1,$model->id)"]);
                $down = Html::tag('i', '', ['class' => 'glyphicon glyphicon-arrow-down text-green', 'onclick' => "js:moveCategory(2,$model->id)"]);
                $result = '';
                switch ($model->checkPositionOnTree()) {
                    case 1:
                        $result = $down;
                        break;
                    case 2:
                        $result = $up;
                        break;
                    case 3:
                        $result = '';
                        break;
                    default:
                        $result = $up . '&nbsp;&nbsp;&nbsp;&nbsp;' . $down;
                }
                return $result;
            },
            'filter' => false
        ],
        [
            'class' => 'kartik\grid\ActionColumn',
            'template' => '{update}{delete}',
            'buttons' => [
                'delete' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::toRoute(['category/delete', 'id' => $model->id]), [
                        'title' => '' . \Yii::t('app', 'Xóa danh mục'),
                        'data-confirm' => Yii::t('app', 'Bạn thực sự muốn xóa danh mục này?')
                    ]);
                }
            ]
        ],
    ],
]); ?>

<?php
$urlCategory = Yii::$app->urlManager->createUrl("category");
Yii::info($urlCategory);
$js = <<<JS

function moveCategory(urlType,id) {
    var url;
    switch (urlType) {
        case 1:
            url = "move-up";
            break;
        case 2:
            url = "move-down";
            break;
        case 3:
            url = "move-back";
            break;
        case 4:
            url = "move-forward";
            break;
    }
    $.ajax({

        type:'GET',
        url: '{$urlCategory}'+'/'+ url,

        data: {'id':id},
        success:function(data) {
            $.pjax.reload({container:'#grid-category-id'});

        }
    });
}
JS;
$this->registerJs($js, $this::POS_HEAD);
