<?php
/**
 * Created by PhpStorm.
 * User: VisioN
 * Date: 27.04.2015
 * Time: 16:59
 */

namespace common\widgets;


class YouTubeAsset extends \yii\web\AssetBundle{
    public $sourcePath = '@common/widgets/js';
    public $js = [
        'api_you_tube.js',
    ];
}


