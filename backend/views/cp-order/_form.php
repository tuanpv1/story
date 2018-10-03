<?php

use common\models\Attribute;
use common\models\User;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Attribute */
/* @var $form yii\widgets\ActiveForm */
$title = Yii::t('app', 'Bạn có chắc chắn Tạo đơn hàng ');
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
function isNumberKey(evt)
    {
       var charCode = (evt.which) ? evt.which : event.keyCode;
       if (charCode > 31 && (charCode < 48 || charCode > 57))
          return false;
       return true;
    }
    
function  validateValueInputA(id) {
    var valueInput = $('#cporder-attribute_value-'+id).val();
    if(valueInput == '' || valueInput == null){
        $('#error_'+id).text($('#nameAttribute_'+id).val());
        $('#error_'+id).show();
    }else {
        if (isNumericA(valueInput) == false){
        $('#cporder-attribute_value-'+id).focus();
        $('#error_'+id).text('Vui lòng nhập số nguyên dương!');
        $('#error_'+id).show();
    }else {
        $('#error_'+id).hide();
        return true;
    }}
}

function isNumericA(num) {
     if(num >=0){
         return true;
     }else {
         return false;
     }
}
JS;

$js_submit = <<<JS
$('#form_create_order').on('beforeSubmit', function (e) {
    var arr = $arr;
    var  n = 0;
    
    arr.forEach(function(element) {
        if(validateValueInputA(element) != true){
            n = n + 1;
        }
    });
    
    if(n!=0){
        return false;
    }
    
    if($check == 1){
        var name = $('#cporder-name').val();
        if(confirm('$title' + name + '?')){
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
<div class="col-md-offset-2 col-md-11">
    <div class="user-form">
            <?php $form = ActiveForm::begin(['id' => 'form_create_order']); ?>
        <div class="row">
            <div class="col-md-2">
                <?= Yii::t('app', 'Loại đơn hàng') ?><span style="color: red">(*)</span>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'name')->textInput(['placeholder' => Yii::t('app', 'Giới hạn 3-20 ký tự'), 'maxlength' => 20])->label(false) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2">
                <?= Yii::t('app', 'Trạng thái') ?><span style="color: red">(*)</span>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'status')->dropDownList(Attribute::listStatus())->label(false) ?>
            </div>
        </div>
        <?php
        if ($model_attribute) {
            /** @var \common\models\Attribute $item */
            foreach ($model_attribute as $item) {
                ?>
                <div class="row">
                    <div class="col-md-2">
                        <?= $item->name ?><span style="color: red">(*)</span>
                        <input type="hidden" id="nameAttribute_<?= $item->id ?>"
                               value="<?= $item->name ?> <?= Yii::t('app', ' không được để trống, vui lòng nhập lại!') ?>">
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'attribute_value[' . $item->id . ']')
                            ->textInput([
//                                    'onkeypress'=>" return isNumberKey(event)",
                                    'onkeyup' => 'validateValueInputA(' . $item->id . ')',
                                    'onchange' => 'validateValueInputA(' . $item->id . ')',
                                    'placeholder' => Yii::t('app', '0')
                                ,])->label(false) ?>
                        <p style="color: red" id="error_<?= $item->id ?>"></p>
                    </div>
                </div>
            <?php }
        } ?>
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

