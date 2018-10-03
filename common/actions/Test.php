<?php
namespace  common\actions;
use yii\base\Action;

/**
 * Created by PhpStorm.
 * User: Linh
 * Date: 21/06/2016
 * Time: 3:20 PM
 */
class Test extends Action
{
    public $view ='/common/actions/views/test';
    public function run()
    {
        $this->controller->render($this->view);
    }
}