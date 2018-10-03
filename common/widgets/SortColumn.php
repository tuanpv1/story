<?php
namespace common\widgets;

use common\assets\CKEditorAsset;
use dosamigos\ckeditor\CKEditorWidgetAsset;
use kotchuprik\sortable\grid\Column;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\InputWidget;

class SortColumn extends Column
{
    public $moveIcon='fa fa-arrows';
    protected function renderDataCellContent($model, $key, $index)
    {
        return Html::tag('div', "<i class='$this->moveIcon' aria-hidden=\"true\"></i>", [
            'class' => 'sortable-widget-handler',
            'data-id' => $model->id,
            'style'=>'text-align:center'
        ]);
    }
} 