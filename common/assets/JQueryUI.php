<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace common\assets;

use yii\web\AssetBundle;
use yii\web\View;

class JQueryUI extends AssetBundle
{

    public $sourcePath = '@common/widgets';
    public $css = [

    ];
    public $js = [
        'js/jquery-ui.min.js',
    ];
    public $depends = [
    ];

}
