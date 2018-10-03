<?php

namespace common\widgets\yii2_crlcu_multi_select;

use yii\widgets\InputWidget;
use yii\helpers\Html;
use yii\helpers\Json;

class MultiSelectListbox extends InputWidget
{
    /**
     * @var array listbox items
     */
    public $items = [];

    /**
     * @var string|array selected items
     */
    public $selection;

    public $show_add_remove_all = true;

    /**
     * @var array listbox options
     */
    public $options = [];


    public $right_item = [];

    /**
     * @var array dual listbox options
     */
    public $clientOptions = [];

    public $id_list_from = 'multiselect';

    /**
     * @inheritdoc
     */
    public function run()
    {

        $id_list_to = 'multiselect_to';
        $name_model = '';
        $value = '';
        if ($this->hasModel()) {
            $value = $this->model->{$this->attribute};
        }
        if (!empty($value)) {

        }
        $item_selected = $this->right_item;

        $hidden = '';
        foreach ($item_selected as $item) {
            $hidden .= $item . "-";
        }
        $hidden_value = $hidden ? substr($hidden, 0, strlen($hidden) - 1) : '';

        $item_available = $this->items;
        Html::addCssClass($this->options, 'form-control');

        if ($this->hasModel()) {
            $id_list_to = Html::getInputId($this->model, $this->attribute);
            $name_model = Html::getInputName($this->model, $this->attribute);
//            $this->id_list_from = 'multiselect-'.Html::getInputId($this->model, $this->attribute);
        }
        $content = "\t" . Html::beginTag('div', ['class' => 'col-md-5']) . "\n";
        $content .= Html::listBox($this->id_list_from . "[]", $this->id_list_from, $item_available, array_merge($this->options, ['id' => $this->id_list_from]));
        $content .= "\t" . Html::endTag('div') . "\n";

        $content .= "\t" . Html::beginTag('div', ['class' => 'col-md-2']) . "\n";
        $content .= $this->renderButton();
        $content .= "\t" . Html::endTag('div') . "\n";

        $content .= "\t" . Html::beginTag('div', ['class' => 'col-md-5']) . "\n";
        if ($this->hasModel()) {
            $content .= Html::activeListBox($this->model, $this->attribute, $item_selected, $this->options);
        } else {
            $content .= Html::listBox($this->name, $this->selection, $item_selected, $this->options);
        }
        $content .= "\t" . Html::endTag('div') . "\n";
        $content .= Html::hiddenInput($name_model, $hidden_value, ['id' => $id_list_to . "-hidden"]);
        echo $content;
        $this->clientOptions['right'] = "#$id_list_to";
        $this->registerClientScript();
    }

    public function renderButton()
    {
        $content = '';
        if ($this->show_add_remove_all) {
            $content .= Html::button('<i class="glyphicon glyphicon-forward"></i>', ['id' => 'multiselect_rightAll', 'class' => 'btn btn-block']);
        }
        $content .= Html::button('<i class="glyphicon glyphicon-chevron-right"></i>', ['id' => 'multiselect_rightSelected', 'class' => 'btn btn-block']);
        $content .= Html::button('<i class="glyphicon glyphicon-chevron-left"></i>', ['id' => 'multiselect_leftSelected', 'class' => 'btn btn-block']);
        if ($this->show_add_remove_all) {
            $content .= Html::button('<i class="glyphicon glyphicon-backward"></i>', ['id' => 'multiselect_leftAll', 'class' => 'btn btn-block']);
        }

        return $content;
    }

    /**
     * Registers the required JavaScript.
     */
    public function registerClientScript()
    {
        $view = $this->getView();
        MultiSelectListboxAsset::register($view);
        $id = $this->id_list_from;
        $options = empty($this->clientOptions) ? '' : Json::encode($this->clientOptions);
        $view->registerJs("jQuery('#$id').multiselect($options);");
    }
}
