<?php

use common\models\Promotion;
use common\models\User;
use kartik\datecontrol\DateControl;
use kartik\form\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Promotion */

$this->title = Yii::t('app', 'Gen mã ưu đãi');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Quản lý mã ưu đãi'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$title = Yii::t('app', 'Bạn có chắc chắn gen mã từ chương trình '.$model->name.'?');
$js_submit = <<<JS
$('#form_gen_code').on('beforeSubmit', function (e) {
    if(confirm('$title')){
       return true
    }else{
       return false
    }             
});

JS;
$this->registerJs($js_submit, \yii\web\View::POS_READY);
?>
<div class="panel panel-success">
    <div class="panel-heading text-center"><?= Yii::t("app", "Tìm kiếm") ?></div>
    <div class="box ">
        <div class="box-body">
            <div class="row">
                <?php $form = ActiveForm::begin([
                    'fullSpan' => 12,
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => [
                        'type' => ActiveForm::TYPE_HORIZONTAL,
                        'showLabels' => true,
                        'labelSpan' => 4,
                        'deviceSize' => ActiveForm::SIZE_LARGE,
                    ],
                    'action' => \yii\helpers\Url::to(['promotion-code/get-view'])
                ]); ?>
                <div class="col-md-10">
                    <?= $form->field($model, 'name')->widget(Select2::classname(), [
                            'data' => ArrayHelper::map(
                                Promotion::find()
                                    ->andWhere(['dealer_id' => $model->dealer_id])
                                    ->andWhere(['gen_code' => Promotion::STATUS_GEN_CODE])
                                    ->andWhere(['status' => Promotion::STATUS_ACTIVE])
                                    ->andWhere(['>', 'expired_time', time()])
                                    ->all(), 'id', 'name'),
                            'options' => ['placeholder' => Yii::t('app', 'Chọn chương trình ưu đãi')],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]
                    ); ?>
                </div>

                <div class="form-group col-md-2">
                    <?= Html::submitButton(Yii::t('app', 'Tra cứu'), ['class' => 'btn btn-success']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
        <div class="panel panel-success">
            <div class="panel-heading text-center"><?= Yii::t("app", "Chương trình ưu đãi") ?></div>
            <div class="box ">
                <div class="box-body">
                    <div class="user-create">
                        <div class="col-md-offset-2 col-md-8">
                            <?php $form = ActiveForm::begin([
                                'method' => 'get',
                                'id' => 'form_gen_code',
                                'action' => Url::to(['promotion-code/gen-code', 'id' => $model->id]),
                            ]); ?><br>
                            <div class="row">
                                <div class="col-md-2">
                                    <?= Yii::t('app', 'Tên đại lý') ?>
                                </div>
                                <div class="col-md-10">
                                    <b><?= $model->getNameCp() ?></b><br><br>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <?= Yii::t('app', 'Tên chương trình') ?>
                                </div>
                                <div class="col-md-10">
                                    <?= $form->field($model, 'name')->textInput(['readonly' => true])->label(false) ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <?= Yii::t('app', 'Số lượng mã') ?>
                                </div>
                                <div class="col-md-10">
                                    <?= $form->field($model, 'total_promotion_code')->textInput(['readonly' => true])->label(false) ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <?= Yii::t('app', 'Thời gian tạo') ?>
                                </div>
                                <div class="col-md-10">
                                    <?= date('d-m-Y', $model->created_at) ?><br><br>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <?= Yii::t('app', 'Trạng thái') ?>
                                </div>
                                <div class="col-md-10">
                                    <?= $form->field($model, 'status')->dropDownList(Promotion::listStatus(), ['disabled' => true])->label(false) ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-offset-5 col-md-8">
                                    <?= Html::submitButton(Yii::t('app', 'Gen mã ưu đãi'), ['class' => 'btn btn-primary']) ?>
                                </div>
                            </div>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
