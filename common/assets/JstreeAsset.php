<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace common\assets;

use yii\web\AssetBundle;
use yii\web\View;

class JstreeAsset extends AssetBundle
{

    public $basePath = '@common/template';
    public $css = [
        'advance/plugins/jstree/dist/themes/default/style.min.css'
    ];
    public $js = [
        'advance/js/jquery.min.js',
        'advance/plugins/jstree/dist/jstree.min.js',
        'advance/js/scripts/ui-tree.js'
    ];
    public $depends = [
        'yii\web\YiiAsset'
    ];

}
