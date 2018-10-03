<?php

namespace common\widgets\yii2_crlcu_multi_select;

use yii\web\AssetBundle;

class MultiSelectListboxAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@common/widgets/yii2_crlcu_multi_select/crlcu-multiselect';

    /**
     * @inheritdoc
     */
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->js = [
            'multiselect.min.js',
        ];
        parent::init();
    }
}
