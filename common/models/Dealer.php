<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $full_name
 * @property string $phone_number
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property string $email
 * @property string $name_code Ma dai ly cua doi tac
 *
 * @property ActivityUser[] $activityUsers
 * @property AuthAssignment[] $authAssignments
 * @property AuthItem[] $itemNames
 * @property Promotion[] $promotions
 */
class Dealer extends ActiveRecord
{
    // luu gia tri data
    public $attribute_value;
    public $username;

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

    public function getStatusName()
    {
        $lst = self::listStatus();
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

    public function beforeValidate()
    {
        foreach (array_keys($this->getAttributes()) as $attr){
            if(!empty($this->$attr)){
                $this->$attr = \yii\helpers\HtmlPurifier::process($this->$attr);
            }
        }
        return parent::beforeValidate();// to keep parent validator available
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

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dealer';
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
            [['status', 'email', 'full_name', 'name_code', 'phone_number', 'username'],
                'required',
                'message' => Yii::t('app', '{attribute} không được để trống, vui lòng nhập lại.')
            ],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['email'], 'email'],
            ['username', 'filter', 'filter' => 'trim'],
            [
                ['username'],
                'unique',
                'targetClass' => '\common\models\User',
                'on' => 'create-dealer'
            ],
            [['name_code'], 'string', 'max' => 3, 'min' => 3],
            [['phone_number'], 'string', 'max' => 45],
            [['username'], 'string', 'max' => 12, 'min' => 4],
            [['full_name'], 'string', 'max' => 100, 'min' => 3],
            [['email', 'name_code','phone_number'], 'unique'],
            [['attribute_value'], 'default', 'value' => 0],
            ['attribute_value', 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'full_name' => Yii::t('app', 'Tên đầy đủ'),
            'phone_number' => Yii::t('app', 'Số điện thoại'),
            'status' => Yii::t('app', 'Trạng thái'),
            'email' => 'Email',
            'name_code' => Yii::t('app', 'Mã đại lý'),
            'created_at' => Yii::t('app', 'Ngày tạo'),
            'updated_at' => Yii::t('app', 'Ngày cập nhật'),
            'username' => Yii::t('app', 'Tên truy cập'),
        ];
    }

    public static function getUserName($id)
    {
        $model = User::findOne(['dealer_id' => $id]);
        /** @var $model \common\models\User */
        if (!$model) {
            return '';
        }
        return $model->username;
    }
}