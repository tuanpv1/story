<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Content */

$this->title = Yii::t('app', 'xem chi tiết nội dung ') . $model->display_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Quản lý nội dung'), 'url' => ['index']];
if ($model->parent_id) {
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app','Bộ: ').$model->getParentName(), 'url' => ['view', 'id' => $model->parent_id, 'active' => 2]];
}
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="panel panel-success">
    <div class="panel-heading text-center"><h3 class="panel-title"><?= $this->title ?></h3></div>
    <div class="box ">
        <div class="box-body">
            <p>
                <?= Html::a(Yii::t('app', 'Cập nhật'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Are you sure you want to delete this item?',
                        'method' => 'post',
                    ],
                ]) ?>
            </p>

            <ul class="nav nav-tabs nav-justified">
                <li class="<?php echo $active == 1 ? 'active' : ''; ?>">
                    <a href="#tab_info" data-toggle="tab">
                        <?= \Yii::t('app', 'Thông tin'); ?></a>
                </li>
                <?php if ($model->is_series) { ?>
                    <li class="<?php echo $active == 2 ? 'active' : ''; ?>">
                        <a href="#tab_episode" data-toggle="tab">
                            Danh sách chương </a>
                    </li>
                <?php } ?>
            </ul>
            <div class="tab-content">
                <div class="tab-pane <?php echo $active == 1 ? 'active' : ''; ?>" id="tab_info">
                    <?= $this->render('_detail', [
                        'model' => $model,
                    ]) ?>
                </div>
                <?php if ($model->is_series) { ?>
                    <div class="tab-pane <?php echo $active == 2 ? 'active' : ''; ?>" id="tab_episode">
                        <?= $this->render('_episode', [
                            'model' => $model,
                            'episodeSearch' => $searchEpisodeModel,
                            'episodeModel' => $episodeModel,
                            'episodeProvider' => $episodeProvider,
                        ]) ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
