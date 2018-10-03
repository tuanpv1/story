<?php

namespace common\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class InteractAsset extends AssetBundle
{
    public $sourcePath = '@common/templates/advance';
    public $css = [

    ];
    public $js = [
        'js/interactjs/interact.min.js'
    ];

}
