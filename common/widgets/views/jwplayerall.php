<?php

use common\assets\JWPlayerAsset;
use common\widgets\JWPlayerAll;

$video_id = time();

if ($player_type == \common\widgets\JWPlayerAll::PLAYER_JWPLAYER) {
    JWPlayerAsset::register($this);

    $js = 'jwplayer.key="Tl/cGRKD5+mHxuBA9abJoeWYGnxLoRlF9Xt8VQHJS2nHMmlibF4GZ6FPp4Zk0206";';
    $this->registerJs($js, \yii\web\View::POS_END);

    $config = "jwplayer('player_" . $video_id . "').setup({
            playlist: [{
                
			    file: '" . $video_url . "',

			    type:'mp4'
			}],
			width: '" . $width . "',
			height: '" . $height . "',
			primary: 'flash',
			aspectratio: '16:9',
			skin: 'five',
            abouttext: 'About gmath player',
            aboutlink: 'http://gmath.vn/',
    });";
    $this->registerJs($config, \yii\web\View::POS_END);
    echo \yii\helpers\Html::tag('div', '', ['id' => 'player_' . $video_id]);
    ?>
<?php } else { ?>
    <div class="jumbotron">
        <div class="container-fluid padding-none">
            <div class="row padding-none">
                <!-- 16:9 aspect ratio -->
                <div class="player">
                    <?php if ($player_type == JWPlayerAll::PLAYER_HTML5) { ?>
                        <video id="videoplayer" controls preload="auto" width="<?= $width ?>" height="<?= $height ?>"
                               poster=""
                               style="min-height:240px;">
                            <source src="<?php echo $video_url ?>" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    <?php } else { ?>
                        <a href="<?= $video_url ?>">
                            <img class="film_img" src=""/>
                        </a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <!--    <div style="margin-top:100px"></div>-->
<?php } ?>