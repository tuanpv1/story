<?php

use common\assets\ToastAsset;
use common\auth\models\ActionPermission;
use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\editable\Editable;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ArrayDataProvider */

$this->title = ''.\Yii::t('app', 'Tạo quyền trang đại lý');
$this->params['breadcrumbs'][] = $this->title;
?>

<?php

$tableId = "rbac-permission-generator";

$generateUrl = \yii\helpers\Url::to(['rbac-cp/generate-permission-confirm']);

$js = <<<JS
function generatePermission(){
    actions = $("#$tableId").yiiGridView("getSelectedRows");
    if(actions.length <= 0){
        alert("Chưa chọn action nào! Xin vui lòng chọn ít nhất một action để tạo permission.");
        return;
    }

    jQuery.post(
        '{$generateUrl}',
        { ids:actions }
        )
        .done(function(result) {
            if(result.success){
                alert(result.message);
                jQuery.pjax.reload({container:'#{$tableId}'});
            }else{
                alert(result.message);
            }
        })
        .fail(function() {
            alert("server error");
    });
}
JS;

$this->registerJs($js, View::POS_END);
?>

<div class="user-index">

    <p>
        <?= Html::a(''.\Yii::t('app', 'Quản lý quyền'), ['permission'], ['class' => 'btn btn-success']) ?>
        <?=
        Html::button('<i class="glyphicon glyphicon-ok"></i> Tạo', [
            'type' => 'button',
            'title' => 'Generate operations',
            'class' => 'btn btn-success',
            'onclick' => 'generatePermission();'
        ])?>
    </p>

    <?= GridView::widget([
        'id' => $tableId,
        'dataProvider' => $dataProvider,
        'responsive' => true,
        'pjax' => true,
        'hover' => true,
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => ''.\Yii::t('app', 'Danh sách action chưa tạo permission')
        ],
        'toolbar' => [
            [
                'content' =>''

            ],
        ],
        'columns' => [
            [
                'class' => '\kartik\grid\CheckboxColumn',
//                'checkboxOptions' => function($model, $key, $index, $column) {
//                    /* @var $model ActionAuthItem */
//                    $existed = $model->isExisted();
//                    return ['checked' => !$existed, 'disabled' => $existed];
//                }
            ],
            ['class' => 'kartik\grid\SerialColumn'],
            'name',
            'appAlias',
            'route',
//            'data',
//            'type',

        ],
    ]); ?>


</div>


