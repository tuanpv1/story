<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace common\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class MathquillAsset extends AssetBundle
{
    public $sourcePath = '@common/widgets';
    public $css = [
        'css/mathquill.css'
    ];
    public $js = [
        'js/mathquill.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}
