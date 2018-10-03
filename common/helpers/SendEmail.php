<?php

namespace common\helpers;
use common\models\User;

class SendEmail
{
    public function sendEmail($email, $content, $account = false)
    {
        if ($account) {
            $subject = \Yii::t('app', 'Thông tin tài khoản tại ' . \Yii::$app->name);
        } else {
            $subject = \Yii::t('app', 'Cấp lại mật khẩu tại ' . \Yii::$app->name);
        }
        return \Yii::$app->mailer->compose()
            ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name . ' robot'])
            ->setTo($email)
            ->setSubject($subject)
            ->setHtmlBody($content)
            ->send();
    }

    public function sendPass($id, $password = null)
    {
        $model = User::findOne($id);
        // Hệ thống tự sinh mật khẩu rồi gửi về
        if ($password == null) {
            $ramdom = new GetRamdom();
            $new_pass = $ramdom->get_rand_alphanumeric(8);
            $model->setPassword($new_pass);
            $content = $model->getMessage($model->username, $new_pass, $model->type);
        } else {
            $content = $model->getMessageUser($model->username, $password);
        }
        if ($model->save()) {
            try {
                $this->sendEmail($model->email, $content);
                return true;
            } catch (\Exception $exception) {
                \Yii::error($exception);
            }
        }else{
            \Yii::error($model->getErrors());
        }
        return false;
    }

    public function sendAccountCp($id, $password)
    {
        /* @var $user User */
        $user = User::findOne($id);
        $content = User::getMessageCp($user->username, $password);
        $this->sendEmail($user->email, $content, true);
    }
}

?>
