<?php
/**
 * @var $video \common\models\LessonItem
 * @var $player_type int
 * @var $protocol int
 * @var $this \yii\web\View
 * @var $width
 * @var $height
 * @var $ajax
 */
use common\widgets\Player;
use yii\validators\UrlValidator;
use yii\web\View;

$video_id = '';

$player_id = rand(1,100).'_player';
if (!empty($video)) {
    $domain_validate = new UrlValidator();
    $video_id = $video;
    if ($domain_validate->validate($video)) {
        if (stripos($video, 'youtu.be') > 0) {
            $arr = explode('/', $video);
            if (count($arr) > 0) {
                $video_id = array_pop($arr);
            }
        } else {
            $arr = explode('v=', $video);
            if (count($arr) > 0) {
                $video_id = array_pop($arr);
            }
        }
    }
}
echo \common\widgets\YouTube::widget([
    'videoId' => $video_id,
    'width' => $width,
    'height' => $height,
    'ajax' => $ajax
]);
?>
