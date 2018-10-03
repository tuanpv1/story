<?php

namespace common\models;

use backend\models\Checked;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "partner_attribute_asm".
 *
 * @property int $id
 * @property int $partner_id
 * @property int $attribute_id
 * @property int $order
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 */
class PartnerAttributeAsm extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 10;
    const STATUS_INACTIVE = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'partner_attribute_asm';
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
            [['partner_id', 'attribute_id', 'order', 'status', 'created_at', 'updated_at'], 'integer'],
            [['order'], 'validateNumber'],
        ];
    }

    public function validateNumber($attribute, $params)
    {
        if ($this->order < 1) {
            $this->addError($attribute, \Yii::t('app', 'Order phải có số thứ tự từ 1 trở lên'));
        }else{
            $model = PartnerAttributeAsm::find()
                ->andWhere(['partner_id' => $this->partner_id, 'order' => $this->order])
                ->andWhere(['<>', 'id', $this->id])
                ->one();
            if ($model) {
                $this->addError($attribute, \Yii::t('app', 'Order không được trùng'));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'partner_id' => 'Partner ID',
            'attribute_id' => 'Attribute ID',
            'order' => 'Order',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

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

    public static function saveChecked($id)
    {
        $checked = new Checked();
        $checked->addChecked($id);
    }
}
