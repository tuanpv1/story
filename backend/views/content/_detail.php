<?php
/**
 * Created by PhpStorm.
 * User: TuanPV
 * Date: 10/24/2018
 * Time: 2:06 PM
 */
use common\models\User;
use kartik\detail\DetailView;
use kartik\helpers\Html;
/** @var \common\models\Content $model */
?>
<?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        'code',
        'display_name',
        [
            'attribute' => 'image',
            'format' => 'raw',
            'value' => $model->image ? Html::img('@web/' . Yii::getAlias('@content_images') . '/' . $model->image) : ''
        ],
        'author',
        [
            'attribute' => 'created_user_id',
            'value' => $model->getUserCreated()
        ],
        'total_like',
        'total_view',
        [
            'attribute' => 'approved_at',
            'value' => date('d/m/Y H:i:s',$model->approved_at)
        ],
        [
            'attribute' => 'created_at',
            'value' => date('d/m/Y H:i:s',$model->created_at)
        ],
        [
            'attribute' => 'updated_at',
            'value' => date('d/m/Y H:i:s',$model->updated_at)
        ],
        'tags',
        'status',
        'total_episode',
        'admin_note',
        'country',
        'total_dislike',
        'total_favorite',
        [
            'attribute' => 'type',
            'value' => $model->getTypeName()
        ],
        'language',
        'short_description:html',
        'description:html',
    ],
]) ?>
