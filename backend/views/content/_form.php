<?php

use common\models\Category;
use common\models\Content;
use kartik\widgets\FileInput;
use kartik\widgets\Select2;
use wbraganca\tagsinput\TagsinputWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Content */
/* @var $form yii\widgets\ActiveForm */
$showPreview = !$model->isNewRecord && !empty($model->image);
?>

<div class="content-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'display_name')->textInput(['maxlength' => true]) ?>

    <?php
    if (!$model->parent_id) {
        echo $form->field($model, 'is_series')->checkbox(['label' => 'Bộ'])->label(false);
    }
    ?>

    <?php if ($showPreview) { ?>
        <div class="col-md-12">
            <div class="col-sm-offset-3 col-sm-5">
                <?php echo Html::img($model->getImageLink(), ['class' => 'file-preview-image']) ?>
            </div>
        </div>
    <?php } ?>

    <?= $form->field($model, 'image')->widget(FileInput::classname(), [
        'options' => ['multiple' => true, 'accept' => 'image/*'],
        'pluginOptions' => [
            'previewFileType' => 'image',
            'showUpload' => false,
            'showPreview' => (!$showPreview) ? true : false,
        ]
    ]); ?>

    <?= $form->field($model, 'author')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'total_like')->textInput() ?>

    <?= $form->field($model, 'total_view')->textInput() ?>

    <?= $form->field($model, 'tags')->widget(TagsinputWidget::classname(), [
        'clientOptions' => [
            'trimValue' => true,
            'allowDuplicates' => false,
            'delimiter' => ',',
        ],
        'options' => ['class' => 'form-control input-circle'],
    ]) ?>

    <?= $form->field($model, 'status')->dropDownList(Content::getListStatus()) ?>

    <?= $form->field($model, 'admin_note')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'country')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->dropDownList(Content::getListType()) ?>

    <?= $form->field($model, 'language')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'short_description')->widget(\dosamigos\ckeditor\CKEditor::className(), ['options' => ['rows' => 6],
        'preset' => 'basic']) ?>

    <?= $form->field($model, 'description')->widget(\dosamigos\ckeditor\CKEditor::className(), ['options' => ['rows' => 8],
        'preset' => 'full']) ?>

    <?= $form->field($model, 'list_cat_id')->widget(Select2::classname(), ['data' => ArrayHelper::map(
        Category::find()->andWhere(['status' => Category::STATUS_ACTIVE])
            ->all(), 'id', 'display_name'),
        'options' => ['placeholder' => 'Chọn danh mục'],
        'pluginOptions' => ['allowClear' => true]]);
    ?>
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
