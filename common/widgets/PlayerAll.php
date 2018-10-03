<?php

namespace common\widgets;


class PlayerAll extends \yii\bootstrap\Widget
{
    const PLAYER_NATIVE = 0;
    const PLAYER_HTML5 = 1;
    const PLAYER_JWPLAYER = 2;

    /**
     * @var $video \common\models\LessonItem
     */
    public $video;
    public $player_type = self::PLAYER_NATIVE;
    public $protocol;
    public $width = '100%';
    public $height = 'auto';
    public $ajax = false;

    public function init()
    {
        parent::init();
        $this->player_type = self::PLAYER_NATIVE;
    }

    public function run()
    {
        return $this->render('playerall', [
            'video' => $this->video,
            'player_type' => $this->player_type,
            'protocol' => $this->protocol,
            'width' => $this->width,
            'height' => $this->height,
            'ajax' => $this->ajax
        ]);
    }

}
