<?php

use common\models\CpOrder;
use common\models\CpOrderAsm;
use common\models\Dealer;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\CpOrderAsm */
/* @var $form yii\widgets\ActiveForm */
$title = Yii::t('app', 'Bạn có chắc chắn Tạo đơn hàng cho đại lý?');
$url =\yii\helpers\Url::to(['cp-order-asm/find-cp-order']);
$arr = [];
if ($model_attribute) {
    /** @var \common\models\Attribute $item */
    foreach ($model_attribute as $item) {
        $arr[] = $item->id;
    }
}
$arr = json_encode($arr);

$js = <<<JS
$('#form_create_order_cp').on('beforeSubmit', function (e) {
    if(confirm('$title')){
        return true
    }else{
        return false
    }
});

$('#order-id').on('change', function(){
    selectOrder = $('#order-id option:selected').val(); // the dropdown item selected value
    $.ajax({
        url : '$url',
        type :'POST',
        dataType:'json',
        data : { selectOrder : selectOrder },
        success : function(result){
        //console.log(result);
            if (result.output){
                result.output.forEach(function(element) {
                    $('#cporderasm-attribute_value-'+element['id']).val(element['value']);// json result
                });
            }else {
                var arr = $arr;
                arr.forEach(function(element) {
                    $('#cporderasm-attribute_value-'+element).val('');// json result
                });
            }
        //alert(result['id']);
        }
    });
});
JS;
$this->registerJs($js, \yii\web\View::POS_READY);
?>
<div class="col-md-offset-2 col-md-11">
    <div class="user-form">
        <?php if ($model->isNewRecord) { ?>
            <?php $form = ActiveForm::begin(['id' => 'form_create_order_cp']); ?>
        <?php } else { ?>
            <?php $form = ActiveForm::begin(['id' => 'form_update_order_cp']); ?>
        <?php } ?>
        <div class="row">
            <div class="col-md-2">
                <?= Yii::t('app', 'Đại lý') ?><span style="color: red">(*)</span>
            </div>
            <div class="col-md-6">
                <?php
                $dataDealer = [];
                $listDealer = Dealer::find()
                    ->where(['status'=>Dealer::STATUS_ACTIVE])
                    ->all();
                /**
                 * @var $dealer \common\models\Dealer
                 */
                foreach ($listDealer as $dealer) {
                    $name = $dealer->name_code.' - '.$dealer->full_name;
                    $dataDealer[$dealer->id]=$name;
                }
                ?>
                <?php if ($model->isNewRecord) { ?>
                    <?= $form->field($model, 'dealer_id')->widget(Select2::classname(), [
                            'data' => $dataDealer,
                            'options' => ['placeholder' => Yii::t('app', 'Tất cả')],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]
                    )->label(false); ?>
                <?php } else { ?>
                    <?= $form->field($model, 'dealer_id')->dropDownList( ArrayHelper::merge([],CpOrderAsm::listCP()), ['id'=>'dealer-id','disabled' => true])->label(false); ?>
                <?php } ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2">
                <?= Yii::t('app', 'Loại đơn hàng') ?><span style="color: red">(*)</span>
            </div>
            <div class="col-md-6">
                <?php if ($model->isNewRecord) { ?>
                    <?= $form->field($model, 'cp_order_id')->dropDownList( ArrayHelper::merge([''=>''.Yii::t('app','Tất cả')],CpOrderAsm::listOrder()), ['id'=>'order-id'])->label(false) ?>
                <?php } else { ?>
                    <?= $form->field($model, 'cp_order_id')->dropDownList( ArrayHelper::merge([],CpOrderAsm::listOrder()), ['id'=>'order-id','disabled' => true])->label(false) ?>
                <?php } ?>

            </div>
        </div>
        <?php
        if ($model_attribute) {
            /** @var \common\models\Attribute $item */
            foreach ($model_attribute as $item) {
                ?>
                <div class="row">
                    <div class="col-md-offset-2 col-md-2">
                        <?= $item->name ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($model, 'attribute_value[' . $item->id . ']')->textInput(['onkeypress'=>" return isNumberKey(event)",'onchange' => 'validateValueInput('.$item->id .')','placeholder' => Yii::t('app', '0'),'readonly' => true])->label(false) ?>
                    </div>
                </div>
            <?php }
        } ?>
        <div class="row">
            <div class="col-md-2">
                <?= Yii::t('app', 'Trạng thái') ?><span style="color: red">(*)</span>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'status')->dropDownList(CpOrderAsm::listStatus())->label(false) ?>
            </div>
        </div>
        <div class="form-actions">
            <div class="row">
                <div class="col-md-offset-3 col-md-8">
                    <div class="form-group">
                        <?php if ($model->isNewRecord) { ?>
                            <?= Html::submitButton(Yii::t('app', 'Tạo đơn hàng'), ['class' => 'btn btn-success']) ?>
                        <?php } else { ?>
                            <?= Html::submitButton(Yii::t('app', 'Cập nhật'), ['class' => 'btn btn-primary']) ?>
                        <?php } ?>
                        <?= Html::button(Yii::t('app','Quay lại'), [
                            'class' => 'btn btn-danger',
                            'onclick'=>"document.location.href='".Yii::$app->request->referrer."'",
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

