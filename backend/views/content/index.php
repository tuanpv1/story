<?php

use common\models\Content;
use kartik\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ContentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app','Quản lý nội dung');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'panel' => [
        'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> ' .$this->title . ' </h3>',
        'type' => 'primary',
        'before' => Html::a('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('app', 'Tạo nội dung mới'), ['create'], ['class' => 'btn btn-success']),
        'showFooter' => false
    ],
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'class' => '\kartik\grid\DataColumn',
            'width' => '60px',
            'format' => 'raw',
            'attribute' => 'image',
            'value' => function ($model, $key, $index, $widget) {
                /** @var $model \common\models\Content */
                $cat_image = Yii::getAlias('@content_images');
                return $model->image ? Html::img('@web/' . $cat_image . '/' . $model->image, ['alt' => 'Thumbnail', 'width' => '60px', 'height' => 'auto']) : '';
            },
        ],
        'code',
        'display_name',
        'author',
        'total_view',
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
            'filter' => Content::getListStatus(),
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => \Yii::t('app', 'Tất cả')],
        ],
        ['class' => 'yii\grid\ActionColumn'],
    ],
]); ?>
