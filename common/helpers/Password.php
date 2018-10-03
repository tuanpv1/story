<?php
/**
 * Created by PhpStorm.
 * User: Linh
 * Date: 21/07/2016
 * Time: 6:01 PM
 */

namespace common\helpers;


use common\models\User;
use yii\base\InvalidParamException;

class Password
{
    /**
     * @param $password
     * @param $user User
     * @return bool
     */
    public static function validatePassword($password, $user)
    {

        if ($user->password_hash === sha1($password)) {
            $user->setPassword($password); // update len password moi
            $user->save();
            return true;
        }
        try {
            return \Yii::$app->security->validatePassword($password, $user->password_hash);
        } catch (InvalidParamException $ex) { // tranh loi Hash is invalid khi nhap password cu (sha1) khong dung
            return false;
        }


    }
}