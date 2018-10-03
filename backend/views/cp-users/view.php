<?php

use common\models\CpOrder;
use common\models\Dealer;
use common\models\User;
use kartik\form\ActiveForm;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Dealer */

$this->title = Yii::t('app', 'Thông tin tài khoản: ') . $model->getUserName($model->id);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Quản lý đại lý'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="panel panel-success">
    <div class="panel-heading text-center"><h3
                class="panel-title"><?= Yii::t('app', 'Thông tin tài khoản đại lý') ?></h3></div>
    <div class="box ">
        <div class="box-body">
            <div class="col-md-offset-2 col-md-8">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        [
                            'attribute' => 'username',
                            'label' => Yii::t('app', 'Tên truy cập'),
                            'value' => $model->getUserName($model->id),
                        ],
                        'email:email',
                        'phone_number',
                        [
                            'attribute' => 'full_name',
                            'label' => Yii::t('app', 'Tên đơn vị đối tác'),
                            'value' => $model->full_name,
                        ],
                        [
                            'attribute' => 'name_code',
                            'label' => Yii::t('app', 'Mã đại lý của đối tác'),
                            'value' => $model->name_code,
                        ],
                        [
                            'attribute' => 'status',
                            'format' => 'raw',
                            'value' => ($model->status == Dealer::STATUS_ACTIVE) ?
                                '<span class="label label-success">' . $model->getStatusName() . '</span>' :
                                '<span class="label label-danger">' . $model->getStatusName() . '</span>',
                        ],
                    ],
                ]) ?>

                <p>
                    <?= Html::a(Yii::t('app','Danh sách đơn hàng'),['cp-order-asm/index','cp_id'=>$model->id])?>
                </p>

                <p style="text-align: center">
                    <?= Html::a(Yii::t('app', 'Cập nhật thông tin'), ['update', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
                    <?= Html::a(Yii::t('app', 'Back'), ['index'], ['class' => 'btn btn-primary']) ?>
                </p>
            </div>
        </div>
    </div>
</div>
