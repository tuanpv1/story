<?php

namespace backend\models;

use common\helpers\GetRamdom;
use common\models\User;
use yii\base\Model;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{
    public $email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email','message' => 'Email không đúng. Vui lòng nhập lại!'],
            ['email', 'exist',
                'targetClass' => '\common\models\User',
                'filter' => ['status' => User::STATUS_ACTIVE],
                'message' => 'Email không đúng. Vui lòng nhập lại!'
            ],
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return boolean whether the email was send
     */
    public function sendEmail()
    {
        /* @var $user User */
        $user = User::findOne([
            'status' => User::STATUS_ACTIVE,
            'email' => $this->email,
        ]);

        if ($user) {
            // Hệ thống tự sinh mật khẩu rồi gửi về
            $ramdom = new GetRamdom();
            $new_pass = $ramdom->get_rand_alphanumeric(8);
            $user->setPassword($new_pass);
            $content = $user->getMessage($user->username, $new_pass, $user->type);
            if ($user->save()) {
                return \Yii::$app->mailer->compose()
                    ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name . ' robot'])
                    ->setTo($this->email)
                    ->setSubject('Password reset for ' . \Yii::$app->name)
                    ->setHtmlBody($content)
                    ->send();
            }
        }

        return false;
    }
}
