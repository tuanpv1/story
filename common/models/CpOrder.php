<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "cp_order".
 *
 * @property int $id
 * @property int $cp_id
 * @property int $status
 * @property int $expired_at
 * @property int $created_at
 * @property int $updated_at
 * @property string $name
 *
 * @property AttributeValue[] $attributeValues
 * @property Promotion[] $promotions
 */
class CpOrder extends \yii\db\ActiveRecord
{

    public $attribute_value;

    const STATUS_ACTIVE = 10;
    const STATUS_INACTIVE = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cp_order';
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
            [['name', 'status'], 'required','message'=>Yii::t('app', '{attribute} không được để trống, vui lòng nhập lại.')],
            [['status', 'expired_at', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 20,'min'=>3],
            [['attribute_value'], 'default', 'value'=> 0],
            [['attribute_value'], 'safe'],
//            [['attribute_value'], 'required','message'=>Yii::t('app', 'Trường bắt buộc không được để trống, vui lòng nhập lại.')],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app','Loại đơn hàng'),
            'status' => Yii::t('app','Trạng thái'),
            'created_at' => Yii::t('app','Ngày tạo'),
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttributeValues()
    {
        return $this->hasMany(AttributeValue::className(), ['cp_order_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPromotions()
    {
        return $this->hasMany(Promotion::className(), ['cp_order_id' => 'id']);
    }


    public static function listStatus()
    {
        $lst = [
            self::STATUS_ACTIVE => \Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => Yii::t('app','Deactive'),

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

    public function getAttributeValue($attribute_id,$order_id){
        /** @var $attribute \common\models\AttributeValue */
        $attribute = AttributeValue::findOne(['attribute_id'=>$attribute_id,'cp_order_id'=>$order_id,'type'=>AttributeValue::TYPE_CP_ORDER]);
        if($attribute){
            return $attribute->value;
        }
        return 0;
    }
}
