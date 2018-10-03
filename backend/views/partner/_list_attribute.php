<?php

use common\models\Attribute;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t("app", "Thêm thuộc tính");
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','Xem thông tin đối tác'), 'url' => ['view','id' => $id_partner]];
$this->params['breadcrumbs'][] = $this->title;

$tb = Yii::t("app", "Chưa chọn thuộc tính! Xin vui lòng chọn ít nhất một thuộc tính để duyệt.");
$tb1 = Yii::t("app", "Chưa chọn thuộc tính! Xin vui lòng chọn ít nhất một thuộc tính để cập nhật.");
$loi = Yii::t("app", "Lỗi hệ thống");

$updateLink = Url::to(['partner/add-attribute-to-partner']);

$js = <<<JS
    function updatePartnerAttribute(partner){    
    feedbacks = $("#partner-index-grid").yiiGridView("getSelectedRows");
    jQuery.post(
    '{$updateLink}',
    { 
        ids:feedbacks,
        idPartner:partner
    }
    )
    .done(function(result) {
    if(result.success){
        alert(result.message);
        jQuery.pjax.reload({container:'#partner-index-grid'});
    }else{
        alert(result.message);
    }
    })
        .fail(function() {
            alert('#{$loi}');
        });
    }
JS;
$this->registerJs($js, \yii\web\View::POS_HEAD);
?>
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="portlet light">
            <div class="portlet-body">
                <?php
                $gridColumn = [
                    [
                        'class' => 'kartik\grid\CheckboxColumn',
                    ],
                    [
                        'attribute' => 'name',
                        'format' => 'raw',
                        'width' => '30%',
                        'value' => function ($model, $key, $index, $widget) {
                            /**
                             * @var $model \common\models\Attribute
                             */
                            $action = "attribute/view";
                            $res = Html::a('<kbd>' . $model->name . '</kbd>', [$action, 'id' => $model->id]);
                            return $res;
                        },
                    ],
                    [
                        'class' => '\kartik\grid\DataColumn',
                        'attribute' => 'status',
                        'width' => '20%',
                        'format' => 'raw',
                        'value' => function ($model, $key, $index, $widget) {
                            /**
                             * @var $model Attribute
                             */
                            if ($model->status == Attribute::STATUS_ACTIVE) {
                                return '<span class="label label-success">' . $model->getStatusName() . '</span>';
                            } else {
                                return '<span class="label label-danger">' . $model->getStatusName() . '</span>';
                            }
                        },
                        'filter' => Attribute::listStatus(),
                        'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
                        'filterWidgetOptions' => [
                            'pluginOptions' => ['allowClear' => true],
                        ],
                        'filterInputOptions' => ['placeholder' => "" . \Yii::t('app', 'Tất cả')],
                    ],
                ];
                ?>

                <?= \kartik\grid\GridView::widget([
                    'dataProvider' => $dataProvider,
                    'id' => 'partner-index-grid',
                    'responsive' => true,
                    'pjax' => true,
                    'hover' => true,
                    'panel' => [
                        'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> ' .  Yii::t('app','Danh sách thuộc tính khuyến mại') . ' </h3>',
                        'type' => 'primary',
                        'showFooter' => false
                    ],
                    'columns' => $gridColumn,
                ]); ?>
            </div>
        </div>
    </div>
</div>
<div class="form-actions">
    <div class="row">
        <div class="col-md-offset-3 col-md-9">
            <?= Html::button('<i class="glyphicon glyphicon-circle-arrow-right"></i>' . Yii::t('app', 'Cập nhật'), [
                'type' => 'button',
                'title' => Yii::t('app', 'Cập nhật'),
                'class' => 'btn btn-primary',
                'onclick' => 'updatePartnerAttribute("' . $id_partner . '");'
            ]) ?>
        </div>
    </div>
</div>