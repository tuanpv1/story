<?php
/**
 * Created by PhpStorm.
 * User: TuanPV
 * Date: 10/24/2018
 * Time: 2:09 PM
 */
use common\models\Content;
use kartik\grid\GridView;
use kartik\helpers\Html;

$this->title = Yii::t('app', 'Danh sách chương');
/** @var Content $model */
?>

<?= GridView::widget([
    'dataProvider' => $episodeProvider,
    'filterModel' => $episodeSearch,
    'pjax' => true,
    'panel' => [
        'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> ' . $this->title . ' </h3>',
        'type' => 'primary',
        'before' => Html::a('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('app', 'Tạo chương mới'), ['create', 'parent_id' => $model->id], ['class' => 'btn btn-success']),
        'showFooter' => false
    ],
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'class' => '\kartik\grid\DataColumn',
            'format' => 'raw',
            'width' => '60px',
            'attribute' => 'image',
            'value' => function ($model, $key, $index, $widget) {
                /** @var $model \common\models\Content */
                $cat_image = Yii::getAlias('@content_images');
                return $model->image ? Html::img('@web/' . $cat_image . '/' . $model->image, ['alt' => 'Thumbnail', 'width' => '60px', 'height' => 'auto']) : '';
            },
            'filter' => false
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'format' => 'raw',
            'width' => '100px',
            'attribute' => 'code',
            'value' => function ($model, $key, $index, $widget) {
                /** @var $model \common\models\Content */
                return '#' . $model->code;
            },
            'filter' => false
        ],
        'display_name',
        [
            'class' => '\kartik\grid\DataColumn',
            'format' => 'raw',
            'width' => '50px',
            'attribute' => 'total_view',
            'value' => function ($model, $key, $index, $widget) {
                /** @var $model \common\models\Content */
                return $model->total_view;
            },
            'filter' => false
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'episode_order',
            'width' => '50px',
            'refreshGrid' => true,
            'editableOptions' => function ($model, $key, $index) {
                /* @var $model \common\models\Content */
                return [
                    'header' => 'Sắp xếp',
                    'size' => 'md',
                    'inputType' => \kartik\editable\Editable::INPUT_TEXT,
                    'formOptions' => [
                        'action' => \yii\helpers\Url::to([
                            'content/update-order',
                            'id' => $model->id
                        ]),
                        'enableClientValidation' => false,
                        'enableAjaxValidation' => false,
                    ],
                ];
            },
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
            'filter' => Content::getListStatus(),
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => \Yii::t('app', 'Tất cả')],
        ],
        ['class' => 'yii\grid\ActionColumn'],
    ],
]); ?>