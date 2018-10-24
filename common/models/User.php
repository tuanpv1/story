<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $full_name
 * @property string $phone
 * @property int $type
 * @property int $dealer_id
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property string $email
 * @property string $name_code Ma dai ly cua doi tac
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token Dung de reset mat khau qua mail
 * @property string $access_login_token
 *
 * @property UserActivity[] $UserActivitys
 * @property AuthAssignment[] $authAssignments
 * @property AuthItem[] $itemNames
 * @property Promotion[] $promotions
 * @property Dealer $dealer
 */
class User extends ActiveRecord implements IdentityInterface
{
    // luu gia tri data
    public $attribute_value;

    public $password_old;
    public $password_new;
    public $password_new_confirm;

    const STATUS_DELETED = 1;
    const STATUS_ACTIVE = 10;
    const STATUS_INACTIVE = 0;

    const TYPE_ADMIN = 1;
    const TYPE_CP = 2;

    public static function listStatus()
    {
        $lst = [
            self::STATUS_ACTIVE => \Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => \Yii::t('app', 'Deactive'),
        ];
        return $lst;
    }

    public static function listStatusCp()
    {
        $lst = [
            self::STATUS_ACTIVE => \Yii::t('app', 'Đang hoạt động'),
            self::STATUS_INACTIVE => \Yii::t('app', 'Tạm dừng'),
        ];
        return $lst;
    }

    public function getStatusName()
    {
        $lst = self::listStatus();
        if (array_key_exists($this->status, $lst)) {
            return $lst[$this->status];
        }
        return $this->status;
    }

    public function getStatusNameCp()
    {
        $lst = self::listStatusCp();
        if (array_key_exists($this->status, $lst)) {
            return $lst[$this->status];
        }
        return $this->status;
    }

    public static function listType()
    {
        $lst = [
            self::TYPE_ADMIN => \Yii::t('app', 'Admin cấp 2'),
        ];
        return $lst;
    }

    public function getTypeName()
    {
        $lst = self::listType();
        if (array_key_exists($this->type, $lst)) {
            return $lst[$this->type];
        }
        return $this->type;
    }

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE, 'type' => self::TYPE_ADMIN]);
    }

    public static function findByUsernameCp($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE, 'type' => self::TYPE_CP]);
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'password_hash'], 'filter', 'filter' => 'trim'],
            [['username', 'type', 'status', 'auth_key', 'password_hash', 'email'],
                'required',
                'message' => Yii::t('app', '{attribute} không được để trống, vui lòng nhập lại.')
            ],
            [['full_name', 'name_code'],
                'required',
                'message' => Yii::t('app', '{attribute} không được để trống, vui lòng nhập lại.'),
                'on' => 'action-with-cp'
            ],
            [['type', 'status', 'created_at', 'updated_at', 'dealer_id'], 'integer'],
            [['email'], 'email'],
            [['password_hash', 'password_reset_token', 'access_login_token'], 'string', 'max' => 255],
            [['name_code'], 'string', 'max' => 500],
            [['phone'], 'string', 'max' => 45],
            [['auth_key'], 'string', 'max' => 32],
            [['username'], 'string', 'max' => 12, 'min' => 4],
            [['full_name'], 'string', 'max' => 50, 'min' => 4,'on' => 'action-with-user'],
            [['username'], 'unique'],
            [['email'], 'unique', 'filter' => ['type' => $this->type]],
            [['name_code'], 'unique', 'on' => 'action-with-cp'],
//            ['attribute_value', 'integer'],
            [['attribute_value'], 'default', 'value' => 0],
            ['attribute_value', 'safe'],
        ];
    }

    public function beforeValidate()
    {
        foreach (array_keys($this->getAttributes()) as $attr){
            if(!empty($this->$attr)){
                $this->$attr = \yii\helpers\HtmlPurifier::process($this->$attr);
            }
        }
        return parent::beforeValidate();// to keep parent validator available
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => Yii::t('app', 'Tên truy cập'),
            'full_name' => Yii::t('app', 'Tên đầy đủ'),
            'phone' => Yii::t('app', 'Số điện thoại'),
            'type' => Yii::t('app', 'Quyền'),
            'status' => Yii::t('app', 'Trạng thái'),
            'email' => 'Email',
            'name_code' => Yii::t('app', 'Mã đại lý'),
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'access_login_token' => 'Access Login Token',
            'created_at' => Yii::t('app', 'Ngày tạo'),
            'updated_at' => Yii::t('app', 'Ngày cập nhật'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDealer()
    {
        return $this->hasOne(Dealer::className(), ['id' => 'dealer_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserActivitys()
    {
        return $this->hasMany(UserActivity::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthAssignments()
    {
        return $this->hasMany(AuthAssignment::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemNames()
    {
        return $this->hasMany(AuthItem::className(), ['name' => 'item_name'])->viaTable('auth_assignment', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPromotions()
    {
        return $this->hasMany(Promotion::className(), ['cp_id' => 'id']);
    }

    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int)end($parts);
        return $timestamp + $expire >= time();
    }

    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function getMessage($username, $pass, $type)
    {
        if ($type == User::TYPE_ADMIN) {
            $link = Yii::$app->params['link_login_be'];
        } else {
            $link = Yii::$app->params['link_login_cp'];
        }
        return Yii::t('app', 'Tài khoản của bạn đã được thiết lập mật khẩu mới:<br> 
                Tên truy cập: ' . $username . '<br>
                Mật khẩu: ' . $pass . '<br>
                Vui lòng đăng nhập lại tại đây: ' . $link);
    }

    public function getMessageUser($username, $pass)
    {
        return Yii::t('app', 'Tài khoản quản trị đại lý Viettalk của bạn đã được thiết lập.<br>
                Tên truy cập: ' . $username . '<br>
                Mật khẩu: ' . $pass . '<br>
                Đăng nhập lại tại đây: ' . Yii::$app->params['link_login_be']);
    }

    public static function listUser()
    {
        $listUser = User::find()->all();
        $lst = [];
        foreach ($listUser as $user) {
            $lst[$user->id] = $user->username;
        }
        return $lst;
    }

    public static function getMessageCp($username, $pass)
    {
        return Yii::t('app', 'Tài khoản đại lý Viettalk của bạn đã được thiết lập. <br>
            Tên truy cập: ' . $username . '<br>
            Mật khẩu: ' . $pass . '<br>
            Vui lòng đăng nhập lại tại đây: ' . Yii::$app->params['link_login_cp']);

    }

    public function getAuthItemProvider($acc_type = null)
    {
        if ($acc_type) {
            return new ActiveDataProvider([
                'query' => $this->getAuthItems()->andWhere(['acc_type' => $acc_type])
            ]);
        } else {
            return new ActiveDataProvider([
                'query' => $this->getAuthItems()
            ]);
        }
    }

    public function getAuthItems()
    {
        return AuthItem::find()->andWhere(['name' => AuthAssignment::find()->select(['item_name'])->andWhere(['user_id' => $this->id])]);
    }

    public function getMissingRoles($acc_type = AuthItem::ACC_TYPE_BACKEND)
    {
        $roles = AuthItem::find()->andWhere(['type' => AuthItem::TYPE_ROLE, 'acc_type' => $acc_type])
            ->andWhere('name not in (select item_name from auth_assignment where user_id = :id)', [':id' => $this->id]);

        return $roles->all();
    }

    /**
     * @return string
     */
    public function getRolesName($id)
    {
        $str = "";
        $roles = AuthItem::find()->andWhere(['name' => AuthAssignment::find()->select(['item_name'])->andWhere(['user_id' => $id])])->all();
        $action = 'rbac-backend/update-role';
        foreach ($roles as $role) {
//            $res = Html::a($role['description'], [$action, 'name' => $role['name']]);
            $res = $role['description'];
            $res .= " [" . sizeof($role['children']) . "]";
            $str = $str . $res . '  ,';
        }
        return $str;
    }
}
