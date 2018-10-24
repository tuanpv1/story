<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ContentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="content-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'display_name') ?>

    <?= $form->field($model, 'ascii_name') ?>

    <?= $form->field($model, 'image') ?>

    <?= $form->field($model, 'author') ?>

    <?php // echo $form->field($model, 'short_description') ?>

    <?php // echo $form->field($model, 'description') ?>

    <?php // echo $form->field($model, 'total_like') ?>

    <?php // echo $form->field($model, 'total_view') ?>

    <?php // echo $form->field($model, 'approved_at') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'tags') ?>

    <?php // echo $form->field($model, 'created_user_id') ?>

    <?php // echo $form->field($model, 'is_series') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'episode_order') ?>

    <?php // echo $form->field($model, 'total_episode') ?>

    <?php // echo $form->field($model, 'parent_id') ?>

    <?php // echo $form->field($model, 'admin_note') ?>

    <?php // echo $form->field($model, 'country') ?>

    <?php // echo $form->field($model, 'rating') ?>

    <?php // echo $form->field($model, 'rating_count') ?>

    <?php // echo $form->field($model, 'total_dislike') ?>

    <?php // echo $form->field($model, 'total_favorite') ?>

    <?php // echo $form->field($model, 'type') ?>

    <?php // echo $form->field($model, 'honor') ?>

    <?php // echo $form->field($model, 'code') ?>

    <?php // echo $form->field($model, 'language') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
