<?php
/**
 * Created by PhpStorm.
 * User: qhuy
 * Date: 7/17/15
 * Time: 9:37 AM
 */

namespace common\assets;


use yii\web\AssetBundle;

class KCFinderAsset extends AssetBundle{
    public $sourcePath = '@vendor/sunhater/kcfinder';
    public $depends = [
        'common\assets\CKEditorAsset',
    ];
}