<?php

namespace backend\models;

use common\models\User;
use Yii;
use yii\base\Model;

/**
 * Login form
 */
class PasswordForm extends Model
{
    public $password_old;
    public $password_new;
    public $password_new_confirm;


    private $_user;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['password_old'], 'required', 'message' => Yii::t('app', '{attribute} không được để trống, vui lòng nhập lại.')],
            [['password_new'], 'required', 'message' => Yii::t('app', '{attribute} không được để trống, vui lòng nhập lại.')],
            [['password_new_confirm'], 'required', 'message' => Yii::t('app', '{attribute} không được để trống, vui lòng nhập lại.')],
            // rememberMe must be a boolean value
            // password is validated by validatePassword()
            ['password_old', 'validatePassword'],
            [['password_new'], 'string', 'min' => 8],
            ['password_new', 'validateValue'],
            [
                ['password_new_confirm'],
                'compare',
                'compareAttribute' => 'password_new',
                'message' => Yii::t('app', 'Mật khẩu xác nhận không khớp, vui lòng nhập lại'),
//                'on' => 'change-password'
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'password_old' => Yii::t('app', 'Mật khẩu cũ'),
            'password_new' => Yii::t('app', 'Mật khẩu mới'),
            'password_new_confirm' => Yii::t('app', 'Xác nhận mật khẩu mới'),
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateValue($attribute, $params)
    {

        $mes = Yii::t('app', 'Mật khẩu bắt buộc chứa cả ký tự in hoa, in thường, ký tự đặc biệt và số');

        $ucl = preg_match('/[A-Z]/', $this->password_new); // Uppercase Letter
        $lcl = preg_match('/[a-z]/', $this->password_new); // Lowercase Letter
        $dig = preg_match('/\d/', $this->password_new); // Numeral
        $nos = preg_match("/^(?=.*[*()@!#$%^&]).*$/", $this->password_new); // Non-alpha/num characters (allows underscore)

        if(!$ucl) {
            $this->addError($attribute,$mes);
        }

        if(!$lcl) {
            $this->addError($attribute,$mes);
        }

        if(!$dig) {
            $this->addError($attribute,$mes);
        }

        if(!$nos) {
            $this->addError($attribute,$mes);
        }
    }

    public function validatePassword($attribute, $params)
    {
        $user = User::findOne(Yii::$app->user->id);
        if (!$user || !$user->validatePassword($this->password_old)) {
            $this->addError($attribute, 'Mật khẩu cũ chưa đúng, vui lòng thử lại');
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
}
