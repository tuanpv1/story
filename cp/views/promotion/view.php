<?php

use common\models\AttributeValue;
use common\models\Promotion;
use kartik\widgets\Select2;
use kartik\form\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Promotion */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Promotions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="panel panel-success">
    <div class="panel-heading text-center"><h3
                class="panel-title"><?= Yii::t('app', 'Thông tin chi tiết tài khoản') ?></h3></div>
    <div class="box ">
        <div class="box-body">
            <div class="col-md-offset-2 col-md-8">
                <?php $form = ActiveForm::begin([
                    'fullSpan' => 12,
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => [
                        'type' => ActiveForm::TYPE_HORIZONTAL,
                        'showLabels' => true,
                        'labelSpan' => 4,
                        'deviceSize' => ActiveForm::SIZE_LARGE,
                    ],
                    'action' => \yii\helpers\Url::to(['promotion/get-view'])
                ]); ?>
                <div class="col-md-10">
                    <?= $form->field($model, 'name')->widget(Select2::classname(), [
                            'data' => ArrayHelper::map(
                                Promotion::find()
                                    ->andWhere(['dealer_id' => $model->dealer_id])
                                    ->all(), 'id', 'name'),
                            'options' => ['placeholder' => Yii::t('app','Chọn chương trình ưu đãi')],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]
                    ); ?>
                </div>

                <div class="form-group col-md-2">
                    <?= Html::submitButton(Yii::t('app', 'Tra cứu'), ['class' => 'btn btn-success']) ?>
                </div>

                <?php
                ActiveForm::end();
                $columns = $model->getDisplayAttribute();
                $column2 = [
                    [
                        'attribute' => 'active_time',
                        'value' => date('d-m-Y H:i:s', $model->active_time),
                    ],
                    [
                        'attribute' => 'expired_time',
                        'value' => date('d-m-Y H:i:s', $model->expired_time),
                    ]
                ];
                echo DetailView::widget([
                    'model' => $model,
                    'attributes' => array_merge([
                        [
                            'attribute' => 'dealer_id',
                            'value' => $model->getNameCp(),
                        ],
                        'name',
                        'total_promotion_code',
                        [
                            'attribute' => 'status',
                            'format' => 'raw',
                            'value' => ($model->status == Promotion::STATUS_ACTIVE) ?
                                '<span class="label label-success">' . $model->getStatusName() . '</span>' :
                                '<span class="label label-danger">' . $model->getStatusName() . '</span>',
                        ],
                    ], $columns, $column2)
                ]) ?>
                <div class="form-actions">
                    <div class="row">
                        <div class="col-md-offset-5 col-md-8">
                            <?= Html::a(Yii::t('app', 'Thay đổi thông tin'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                            <?= Html::a(Yii::t('app', 'Back'), ['index'], ['class' => 'btn btn-default']) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
