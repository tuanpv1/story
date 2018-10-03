<?php

use common\assets\ToastAsset;
use kartik\widgets\ActiveForm;
use kartik\grid\GridView;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model common\models\AuthItem */

$this->title = '' . \Yii::t('app', 'Cập nhật nhóm quyền: ') . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '' . \Yii::t('app', 'Quản lý nhóm quyền backend'), 'url' => ['role']];
//$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view-role', 'name' => $model->name]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">

    <div class="col-md-6">
        <div class="panel panel-success">
            <div class="panel-heading"><h3 class="panel-title"><i
                        class="fa fa-globe"></i><?= \Yii::t('app', 'Thông tin chung') ?></h3></div>
            <div class="box ">
                <div class="box-body">
                    <?= $this->render('_form-role', [
                        'model' => $model,
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
    <?php
    $formID = "add-permission-form";
    $childrenGridId = 'rbac-role-children';

    $revokeUrl = \yii\helpers\Url::to(['rbac-backend/role-revoke-auth-item']);

    $js = <<<JS
function revokeItem(item){
    if(confirm("Bạn có thực sự muốn xóa quyền '" + item + "' khỏi nhóm quyền '" + "$model->name" + "' không?")){
    jQuery.post(
        '{$revokeUrl}'
        ,{ parent: "$model->name", child:item}
        )
        .done(function(result) {
            if(result.success){
                alert(result.message);
                jQuery.pjax.reload({container:'#{$childrenGridId}'});
            }else{
                alert(result.message);
            }
        })
        .fail(function() {
            alert("server error");
    });
    }
}
JS;

    $this->registerJs($js, View::POS_END);

    $js = <<<JS
// get the form id and set the event
jQuery('#{$formID}').on('beforeSubmit', function(e) {
    \$form = jQuery('#{$formID}');
   $.post(
        \$form.attr("action"), // serialize Yii2 form
        \$form.serialize()
    )
        .done(function(result) {
            if(result.success){
                alert(result.message);
                jQuery.pjax.reload({container:'#{$childrenGridId}'});
            }else{
                alert(result.message);
            }
        })
        .fail(function() {
            alert("server error");
        });
    return false;
}).on('submit', function(e){
    e.preventDefault();
});
JS;
    $this->registerJs($js, View::POS_END);
    ?>
    <div class="col-md-6">
        <div class="panel panel-success">
            <div class="panel-heading"><h3 class="panel-title"><i
                        class="fa fa-globe"></i><?= \Yii::t('app', 'Quyền') ?></h3></div>
            <div class="box ">
                <div class="box-body">
                    <div class="portlet-body">
                        <h3><?= \Yii::t('app', 'Quyền') ?></h3>
                        <?= GridView::widget([
                            'id' => $childrenGridId,
                            'dataProvider' => $model->getChildrenProvider(),
                            'responsive' => true,
                            'pjax' => true,
                            'hover' => true,
                            'columns' => [
                                ['class' => 'kartik\grid\SerialColumn'],
                                [
                                    'attribute' => 'name',
                                    'label'=> Yii::t('app','Tên quyền'),
                                    'format' => 'html',
                                    'vAlign' => 'middle',
                                    'value' => function ($model, $key, $index, $widget) {
                                        /**
                                         * @var $model \common\models\AuthItem
                                         */
                                        $action = $model->type == \common\models\AuthItem::TYPE_ROLE ? 'rbac-backend/update-role' : 'rbac-backend/update-permission';
                                        $res = $model->description;
                                        if ($model->type == \common\models\AuthItem::TYPE_ROLE) {
                                            $res .= " [" . sizeof($model->children) . "]";
                                        }
                                        return $res;
                                    },
                                ],
                                [
                                    'attribute' => 'description',
                                    'vAlign' => 'middle',
                                ],
                                [
                                    'attribute' => 'type',
                                    'format' => 'html',
                                    'vAlign' => 'middle',
                                    'value' => function ($model, $key, $index, $widget) {
                                        /**
                                         * @var $model \common\models\AuthItem
                                         */
                                        $res = $model->type == \common\models\AuthItem::TYPE_PERMISSION ? "" . \Yii::t('app', 'Quyền') : "" . \Yii::t('app', 'Nhóm quyền');
                                        return $res;
                                    },
                                ],
                                ['class' => 'yii\grid\ActionColumn',
                                    'template' => '{revoke}',
                                    'buttons' => [
                                        'revoke' => function ($url, $model1, $key) {
                                            return Html::button('<i class="glyphicon glyphicon-remove-circle"></i> Revoke', [
                                                'type' => 'button',
                                                'title' => '' . \Yii::t('app', 'Xóa quyền'),
                                                'class' => 'btn btn-danger',
                                                'onclick' => "revokeItem('$model1->name');"
                                            ]);
                                        },
                                    ],
                                ],
                            ],
                        ]); ?>

                        <h3><?= \Yii::t('app', 'Thêm quyền') ?></h3>
                        <?php

                        $form = ActiveForm::begin([
                            'id' => $formID,
                            'action' => ['rbac-backend/role-add-auth-item', 'name' => $model->name]
                        ]);
                        ?>

                        <div class="form-group">
                            <?php

//                            $roles = \yii\helpers\ArrayHelper::map($model->getMissingRoles(), "name", "description");
                            $permissions = \yii\helpers\ArrayHelper::map($model->getMissingPermissions(), "name", "description");
                            $data = ["" . \Yii::t('app', 'Quyền') => $permissions];
                            echo Select2::widget([
                                'name' => 'addItems',
                                'data' => $data,
                                'options' => [
                                    'placeholder' => '' . \Yii::t('app', 'Chọn quyền'),
                                    'multiple' => true
                                ],
                            ]);
                            ?>
                        </div>

                        <div class="form-group">
                            <?= Html::submitButton('' . \Yii::t('app', 'Thêm quyền'),
                                ['class' => 'btn btn-primary']) ?>
                        </div>

                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
