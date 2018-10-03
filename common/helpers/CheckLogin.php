<?php

namespace common\helpers;

use Yii;

/**
 * Created by PhpStorm.
 * User: MyPT
 * Date: 9/24/2018
 * Time: 10:18 AM
 */
class CheckLogin
{

    public function getCountError()
    {
        $count = Yii::$app->session->get('count') ? Yii::$app->session->get('count') : 0;;
        return $count;
    }

    public function setCountError($count)
    {
        Yii::$app->session->set('count', $count);
        if ($count == (Yii::$app->params['numberLogin'] + 1)) {
            Yii::$app->session->set('timeExpired', time());
        }
    }

    public function showError($count)
    {
        if ($count > Yii::$app->params['numberLogin']) {
            if ($count == (Yii::$app->params['numberLogin'] + 1)) {
                if ((time() - Yii::$app->session->get('timeExpired')) > (Yii::$app->params['timeOutLogin'] * 60)) {
                    Yii::$app->session->set('count', 0);
                    Yii::$app->session->set('timeExpired', 0);
                    return true;
                }else{
                    Yii::$app->session->set('timeExpired', time());
                }
            }
            return false;
        } else {
            return true;
        }
    }
}