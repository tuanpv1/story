<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace common\assets;

use yii\web\AssetBundle;
use yii\web\View;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class QuestionAsset extends AssetBundle
{

    public $sourcePath = '@common/templates/advance';
    public $css = [

        'css/new.css',
        'css/style-responsive.css',
        'css/font-awesome.css',



        'css/custom.css',
        'css/front-end-style.css'
    ];
    public $js = [


    ];
    public $depends = [


    ];
    public $jsOptions = array(
        'position' => View::POS_END
    );
}
