<?php

use common\models\PromotionCode;
use common\models\Dealer;
use kartik\export\ExportMenu;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\PromotionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Quản lý mã ưu đãi';
$this->params['breadcrumbs'][] = $this->title;
//$url = Url::to(['promotion-code/send']);
$url = '';
$send = Yii::$app->session->get('file');
if (!empty($send)) {
    $url = Yii::getAlias('@file_export') . '/' . Yii::$app->session->get('file');
} else {
    $send = 0;
}
$js = <<<JS
    var check = '$send';
    if(check != 0){
        var url = '$url';
        window.open(url,'_blank');
    }
JS;
$this->registerJs($js, \yii\web\View::POS_READY);

Yii::$app->session->remove('file');
?>
<div class="promotion-index">
    <?php
    $gridColumns = [
        [
            'class' => 'yii\grid\SerialColumn',
            'header' => Yii::t('app','STT'),
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'header' => Yii::t('app','Tên đại lý'),
            'attribute' => 'nameCp',
            'value' => function ($model, $key, $index, $widget) {
                /**
                 * @var $model \common\models\Promotion
                 */
                return Dealer::findOne(Yii::$app->user->identity->dealer_id)->full_name;
            },
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute'=>'promotion_id',
            'value' => function ($model, $key, $index, $widget) {
                /**
                 * @var $model \common\models\PromotionCode
                 */
                return $model->getPromotionName();
            },
            'filterInputOptions' => ['placeholder' => Yii::t('app', 'Search'),'class' => 'form-control input-sm'],
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute'=>'receiver',
            'label'=>Yii::t('app','Thuê bao sử dụng'),
            'value' => function ($model, $key, $index, $widget) {
                /**
                 * @var $model \common\models\PromotionCode
                 */
                if($model->status== PromotionCode::STATUS_NOT_USED){
                    return 'N/A';
                }else{
                    return $model->receiver;
                }
            },
            'filterInputOptions' => ['placeholder' => Yii::t('app', 'Search'),'class' => 'form-control input-sm'],
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute'=>'code',
            'value' => function ($model, $key, $index, $widget) {
                /**
                 * @var $model \common\models\PromotionCode
                 */
                return \common\helpers\Encrypt::decryptCode( $model->code,Yii::$app->params['key_encrypt']);
            },
            'filterInputOptions' => ['placeholder' => Yii::t('app', 'Search'),'class' => 'form-control input-sm'],
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute'=>'created_at',
            'value' => function ($model, $key, $index, $widget) {
                /**
                 * @var $model \common\models\PromotionCode
                 */
                return date('d-m-Y H:i:s',$model->created_at);
            },
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute'=>'expired_at',
            'value' => function ($model, $key, $index, $widget) {
                /**
                 * @var $model \common\models\PromotionCode
                 */
                if($model->status== PromotionCode::STATUS_NOT_USED){
                    return 'N/A';
                }else{
                    return date('d-m-Y H:i:s',$model->expired_at);
                }
            },
        ],
        [
            'class' => '\kartik\grid\DataColumn',
            'attribute'=>'status',
            'width'=>'15%',
            'format'=>'raw',
            'value' => function ($model, $key, $index, $widget) {
                /**
                 * @var $model \common\models\PromotionCode
                 */
                return $model->getStatusName();
            },
            'filter' => PromotionCode::listStatus(),
            'filterType' => GridView::FILTER_SELECT2,
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => "".\Yii::t('app', 'Tất cả')],
        ],
    ];
    $expMenu = ExportMenu::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumns,
        'showConfirmAlert' => false,
        'fontAwesome' => true,
        'showColumnSelector' => false,
        'dropdownOptions' => [
            'label' => 'Xuất Excel',
            'class' => 'btn btn-primary'
        ],
        'exportConfig' => [
            ExportMenu::FORMAT_CSV => false,
            ExportMenu::FORMAT_EXCEL_X => [
                'label' => 'Microsoft Excel 2007+',
            ],
            ExportMenu::FORMAT_HTML => false,
            ExportMenu::FORMAT_PDF => false,
            ExportMenu::FORMAT_TEXT => false,
            ExportMenu::FORMAT_EXCEL =>[
                'label'=>'Microsoft Excel 95+'
            ],
        ],
        'target' => ExportMenu::TARGET_SELF,
        'filename' => "promotion_code"
    ]);

    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'panel' => [
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> ' .Yii::t('app','Danh sách mã ưu đãi'). ' </h3>',
            'type' => 'primary',
            'before'=>$expMenu,
//            'before'=>Html::a(Yii::t('app','Xuất Excel'), ['export-excel'], ['class' => 'btn btn-success']),
//            'after' => Html::a('<i class="glyphicon glyphicon-repeat"></i> ' . Yii::t('app', 'Reset List'), ['index'], ['class' => 'btn btn-info']),
            'showFooter' => false
        ],
        'columns' => $gridColumns,
    ]); ?>
</div>
