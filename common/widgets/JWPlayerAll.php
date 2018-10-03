<?php

namespace common\widgets;

use common\helpers\StreamingHelper;
use common\models\ContentProfile;
use common\models\VideoStream;
use frontend\widgets\Content;
use Mobile_Detect;

class JWPlayerAll extends \yii\bootstrap\Widget
{
    const PLAYER_NATIVE = 0;
    const PLAYER_HTML5 = 1;
    const PLAYER_JWPLAYER = 2;

    /**
     * @var $lesson_item \common\models\LessonItem
     */
    public $video_url;
    public $player_type = self::PLAYER_NATIVE;
    public $protocol;
    public $width = '100%';
    public $height = 'auto';

    public function init()
    {
        parent::init();
        $this->player_type = self::PLAYER_JWPLAYER;
//        $this->protocol = StreamingHelper::getSupportedStreamingProtocol();
//        $detector = new Mobile_Detect();
//        if ($detector->isMobile() || $detector->isTablet()) {
//            if($this->protocol == ContentProfile::STREAMING_HLS){
//                $this->player_type = self::PLAYER_HTML5;
//            }else{
//                $this->player_type = self::PLAYER_NATIVE;
//            }
//        } else {
//            $this->player_type = self::PLAYER_JWPLAYER;
//        }
    }

    public function run()
    {
        return $this->render('jwplayerall', [
            'video_url' => $this->video_url,
            'player_type' => $this->player_type,
//            'protocol' => $this->protocol,
            'width' => $this->width,
            'height' => $this->height
        ]);
    }

}
