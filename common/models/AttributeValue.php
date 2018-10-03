<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "attribute_value".
 *
 * @property int $id
 * @property int $attribute_id
 * @property int $cp_order_id
 * @property int $dealer_id
 * @property double $value
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $type 1 data of table order 2 data of table promotion
 * @property int $promotion_id
 *
 * @property CpOrder $cpOrder
 * @property Promotion $promotion
 * @property Attribute $attribute0
 */
class AttributeValue extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 10;

    const TYPE_CP_ORDER = 1; // Đơn hàng
    const TYPE_PROMOTION = 2; // Chương trình khuyến mãi
    const TYPE_BALANCE = 3; // Tài khoản ví

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'attribute_value';
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
            [['attribute_id', 'status','value', 'type', ], 'required'],
            [['attribute_id', 'cp_order_id', 'status', 'created_at', 'updated_at', 'type', 'promotion_id', 'dealer_id'], 'integer'],
            [['value'], 'double'],
            [['cp_order_id'], 'exist', 'skipOnError' => true, 'targetClass' => CpOrder::className(), 'targetAttribute' => ['cp_order_id' => 'id']],
            [['promotion_id'], 'exist', 'skipOnError' => true, 'targetClass' => Promotion::className(), 'targetAttribute' => ['promotion_id' => 'id']],
            [['attribute_id'], 'exist', 'skipOnError' => true, 'targetClass' => Attribute::className(), 'targetAttribute' => ['attribute_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'attribute_id' => 'Attribute ID',
            'cp_order_id' => 'Cp Order ID',
            'value' => 'Value',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'type' => 'Type',
            'promotion_id' => 'Promotion ID',
            'dealer_id' => 'CP',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCpOrder()
    {
        return $this->hasOne(CpOrder::className(), ['id' => 'cp_order_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPromotion()
    {
        return $this->hasOne(Promotion::className(), ['id' => 'promotion_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttribute0()
    {
        return $this->hasOne(Attribute::className(), ['id' => 'attribute_id']);
    }
}
