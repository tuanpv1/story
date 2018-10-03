<?php

use common\models\AttributeValue;
use common\models\Dealer;
use common\models\Promotion;
use kartik\datecontrol\DateControl;
use kartik\helpers\Html;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Promotion */
/* @var $form yii\widgets\ActiveForm */

$message_error_number = Yii::t('app', 'Vui lòng nhập số có giá trị lớn hơn 0!');
$message_error_code = Yii::t('app', 'Vui lòng nhập số lượng mã trước');
$message_error_balance = Yii::t('app', 'Tài khoản ví không đủ để tạo chương trình ưu đãi');
$title = Yii::t('app', 'Bạn có chắc chắn tạo chương trình ưu đãi ');
$message_error_zero = Yii::t('app', 'Số lượng mã phải lớn hơn 0');
$check = 0;
if ($model->isNewRecord) {
    $check = 1;
}

$arr = [];
if ($model_attribute) {
    /** @var \common\models\Attribute $item */
    foreach ($model_attribute as $item) {
        $arr[] = $item->id;
    }
}
$arr = json_encode($arr);

$js = <<<JS
function  validateValueInput(id) {
    var valueInput = $('#promotion-attribute_value-'+id).val();
    if(valueInput == '' || valueInput == null){
        $('#valueError_'+id).text($('#nameAttribute_'+id).val());
        $('#valueError_'+id).show();
    }else {
        $('#valueError_'+id).hide();
        if (isNumericA(valueInput) == false){
             $('#valueError_'+id).text('$message_error_number');
             $('#valueError_'+id).show();
        }else {
            $('#valueError_'+id).hide();
            var numberCode = $('#promotion-total_promotion_code').val();
            if(numberCode == ''){
                $("#promotion-total_promotion_code").focus();
                $('#valueError_'+id).text('$message_error_code');
                $('#valueError_'+id).show();
            }else{
                if(numberCode <= 0){
                    $('#errorZero').text('$message_error_zero');
                    $('#errorZero').show();
                }else{
                    $('#errorZero').hide();
                    $('#valueError_'+id).hide();
                    var currentValue = $('#current_value_'+id).val();
                    var  total = valueInput*numberCode;
                    if(total > currentValue){
                        $('#valueError_'+id).text('$message_error_balance');
                        $('#valueError_'+id).show();
                    }else{
                        $('#valueError_'+id).hide();
                        return true;
                    }
                }
            }
        }   
    }
}

function isNumericA(num) {
     if(num >= 0){
         return true;
     }else {
         return false;
     }
}

function callToAttribute() {
    var arr = $arr;
    arr.forEach(function(element) {
        validateValueInput(element);
    });
}
JS;

$js_submit = <<<JS
$('#create-promotion-form').on('beforeSubmit', function (e) {

    var arr = $arr;
    var  n = 0;
    
    arr.forEach(function(element) {
        if(validateValueInput(element) != true){
            n = n + 1;
        }
    });
    
    if(n!=0){
        return false;
    }
    
    if($check == 1){
        var promotionName = $('#promotion-name').val();
        if(confirm('$title' + promotionName + '?')){
            return true
        }else{
            return false
        }
    }
    
    
});

JS;
$this->registerJs($js, \yii\web\View::POS_HEAD);
$this->registerJs($js_submit, \yii\web\View::POS_READY);
?>

<div class="promotion-form">

    <?php $form = ActiveForm::begin([
        'id' => 'create-promotion-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
    ]); ?>
    <div class="row">
        <div class="col-md-2">
            <?= Yii::t('app', 'Đại lý') ?>
        </div>
        <div class="col-md-6">
            <b><?= Dealer::findOne(Yii::$app->user->identity->dealer_id)->full_name ?></b>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-2">
            <?= Yii::t('app', 'Tên chương trình') ?><span style="color: red">(*)</span>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'name')->textInput()->label(false) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-2">
            <?= Yii::t('app', 'Số lượng mã') ?><span style="color: red">(*)</span>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'total_promotion_code')->textInput(['maxlength' => true, 'onchange' => 'callToAttribute()'])->label(false) ?>
            <p id="errorZero" style="color:red;"></p>
        </div>
    </div>

    <div class="row">
        <?php
        if ($model_attribute) {
            /** @var \common\models\Attribute $item */
            foreach ($model_attribute as $item) {
                ?>
                <div class="col-md-7">
                    <div class="col-md-4">
                        <?= Yii::t('app', 'Số ') ?><?= $item->name ?> <?= Yii::t('app', ' tương ứng với một mã') ?><span style="color: red">(*)</span>
                        <input type="hidden" id="nameAttribute_<?= $item->id ?>"
                               value="<?= Yii::t('app', 'Số ') ?><?= $item->name ?> <?= Yii::t('app', ' tương ứng với một mã không được để trống, vui lòng nhập lại!') ?>">
                    </div>
                    <div class="col-md-8">
                        <?= $form
                            ->field($model, 'attribute_value[' . $item->id . ']')
                            ->textInput(['
                                onkeyup' => 'validateValueInput(' . $item->id . ')',
                                'value' => $model->isNewRecord ? 0 : $model->getDisplayValue($item->id),
                            ])
                            ->label(false) ?>
                        <p style="color: red" id="valueError_<?= $item->id ?>"></p>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="col-md-5">
                        <?= Yii::t('app', 'Số ') ?><?= $item->name ?> <?= Yii::t('app', ' còn lại') ?>
                    </div>
                    <div class="col-md-7">
                        <input id="current_value_<?= $item->id ?>" type="text"
                               value="<?= $model->getValueCurrent($item->id) ?>" class="form-control" disabled="true">
                    </div>
                </div>
            <?php }
        } ?>
    </div>

    <div class="row">
        <div class="col-md-2">
            <?= Yii::t('app', 'Thời gian active') ?><span style="color: red">(*)</span>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'active_time')->widget(DateControl::classname(), [
                'type' => DateControl::FORMAT_DATETIME,
                'displayFormat' => 'd-M-y H:i:s',
                'saveFormat' => 'php:U',
                'displayTimezone' => 'Asia/Ho_Chi_Minh',
            ])->label(false);
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-2">
            <?= Yii::t('app', 'Thời gian hết hạn') ?><span style="color: red">(*)</span>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'expired_time')->widget(DateControl::classname(), [
                'type' => DateControl::FORMAT_DATETIME,
                'displayFormat' => 'd-M-y H:i:s',
                'saveFormat' => 'php:U',
                'displayTimezone' => 'Asia/Ho_Chi_Minh',
            ])->label(false);
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-2">
            <?= Yii::t('app', 'Trạng thái hoạt động') ?><span style="color: red">(*)</span>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'status')->dropDownList(Promotion::listStatus())->label(false) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Tạo chương trình ưu đãi') : Yii::t('app', 'Cập nhật thông tin'), ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('app', 'Nhập lại'), [''], ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
