<?php

use common\models\PartnerAttributeAsm;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Partner */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Partners', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="partner-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'secret_key',
            [
                'attribute' => 'status',
                'value' => $model->getStatusName(),
            ],
            [
                'attribute' => 'created_at',
                'value' => date('d-m-Y H:i:s', $model->created_at),
            ],
            [
                'attribute' => 'updated_at',
                'value' => date('d-m-Y H:i:s', $model->updated_at),
            ]
        ],
    ]) ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'panel' => [
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> ' . Yii::t('app', 'Các thuộc tính khuyến mại trả về cho đối tác') . ' </h3>',
            'before' => Html::a('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('app', 'Thêm mới thuộc tính'), ['add-attribute','id_partner' => $model->id], ['class' => 'btn btn-success']),
            'type' => 'primary',
            'showFooter' => false
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'class' => '\kartik\grid\DataColumn',
                'attribute' => 'attribute_id',
                'value' => function ($model, $key, $index, $widget) {
                    /**
                     * @var $model \common\models\PartnerAttributeAsm
                     */
                    $attribute = \common\models\Attribute::findOne($model->attribute_id);
                    return $attribute ? $attribute->name : '';
                },
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'order',
                'refreshGrid' => true,
                'editableOptions' => function ($model, $key, $index) {
                    /**
                     * @var $model \common\models\PartnerAttributeAsm
                     */
                    return [
                        'header' => \Yii::t('app', 'Trạng thái'),
                        'size' => 'md',
                        'displayValueConfig' => $model->order,
                        'inputType' => \kartik\editable\Editable::INPUT_TEXT,
                        'placement' => \kartik\popover\PopoverX::ALIGN_LEFT,
                        'formOptions' => [
                            'action' => ['partner/update-order', 'id' => $model->id]
                        ],
                    ];
                },
            ],
            [
                'class' => 'kartik\grid\EditableColumn',
                'attribute' => 'status',
                'refreshGrid' => true,
                'editableOptions' => function ($model, $key, $index) {
                    /**
                     * @var $model \common\models\PartnerAttributeAsm
                     */
                    return [
                        'header' => \Yii::t('app', 'Trạng thái'),
                        'size' => 'md',
                        'displayValueConfig' => PartnerAttributeAsm::listStatus(),
                        'inputType' => \kartik\editable\Editable::INPUT_DROPDOWN_LIST,
                        'data' => PartnerAttributeAsm::listStatus(),
                        'placement' => \kartik\popover\PopoverX::ALIGN_LEFT,
                        'formOptions' => [
                            'action' => ['partner/update-status', 'id' => $model->id]
                        ],
                    ];
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => PartnerAttributeAsm::listStatus(),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],

                'filterInputOptions' => ['placeholder' => 'Tất cả'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{delete}',
                'header' => Yii::t('app', 'Hành động'),
                'buttons' => [
                    'delete' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::toRoute(['partner/delete-attribute', 'id' => $model->id]), [
                            'title' => '' . \Yii::t('app', 'Xóa thuộc tính khuyễn mãi'),
                            'data-confirm' => Yii::t('app', 'Bạn có chắc chắn muốn xóa thuộc tính trả về cho đại lý? Lưu ý thao tác không thể phục hồi!'),
                        ]);
                    },
                ]
            ],
        ],
    ]); ?>

</div>
