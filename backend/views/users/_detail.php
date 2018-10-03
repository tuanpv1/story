<?php
/**
 * Created by PhpStorm.
 * User: TuanPV
 * Date: 5/29/2018
 * Time: 5:03 PM
 */
use common\models\User;
use kartik\detail\DetailView;
use yii\helpers\Html;

/** @var $model User */
?>
<div class="col-md-offset-2 col-md-8">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'username',
            'email:email',
            'phone',
            'full_name',
//            [
//                'attribute'=>'type',
//                'format'=>'raw',
//                'value'=> $model->getTypeName(),
//            ],
            [
                'attribute'=>'status',
                'format'=>'raw',
                'value'=>($model->status ==User::STATUS_ACTIVE)  ?
                    '<span class="label label-success">'.$model->getStatusName().'</span>' :
                    '<span class="label label-danger">'.$model->getStatusName().'</span>',
            ],
        ],
    ]) ?>
    <div class="form-actions">
        <div class="row">
            <div class="col-md-offset-5 col-md-8">
                <?= Html::a(Yii::t('app','Cập nhật thông tin'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
    </div>
</div>
