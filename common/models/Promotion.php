<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "promotion".
 *
 * @property int $id
 * @property int $dealer_id
 * @property string $name
 * @property int $status
 * @property int $active_time
 * @property int $expired_time
 * @property int $cp_order_id
 * @property string $total_promotion_code
 * @property string $file
 * @property int $created_at
 * @property int $updated_at
 * @property int $gen_code
 *
 * @property AttributeValue[] $attributeValues
 * @property CpOrder $cpOrder
 * @property User $cp
 * @property PromotionCode[] $promotionCodes
 */
class Promotion extends \yii\db\ActiveRecord
{
    public $attribute_value;

    const STATUS_ACTIVE = 10;
    const STATUS_PENDING = 9;
    const STATUS_INACTIVE = 0;
    const STATUS_DELETE = 1; // xóa trạng thái

    const  STATUS_GEN_CODE = 0;
    /** chưa gen mã khuyến mại*/
    const  STATUS_GEN_CODED = 1;

    /** đã gen mã khuyến mại*/

    public static function listStatus()
    {
        $lst = [
            self::STATUS_ACTIVE => \Yii::t('app', 'Active'),
            self::STATUS_PENDING => \Yii::t('app', 'Pending'),
            self::STATUS_INACTIVE => \Yii::t('app', 'Deactive'),
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

    public function getStatusName()
    {
        $lst = self::listStatus();
        if (array_key_exists($this->status, $lst)) {
            return $lst[$this->status];
        }
        return $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'promotion';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dealer_id', 'name', 'status', 'total_promotion_code', 'active_time', 'expired_time'], 'required', 'message' => '{attribute} không được để trống, vui lòng nhập lại.'],
            [['dealer_id', 'status', 'cp_order_id', 'total_promotion_code', 'created_at', 'updated_at', 'expired_time', 'active_time', 'gen_code'], 'integer'],
            [['name'], 'string', 'max' => 100, 'tooLong'=>Yii::t('app','{attribute} không được vượt quá 100 ký tự')],
            [['file'], 'string', 'max' => 500],
            [['cp_order_id'], 'exist', 'skipOnError' => true, 'targetClass' => CpOrder::className(), 'targetAttribute' => ['cp_order_id' => 'id']],
            [['dealer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Dealer::className(), 'targetAttribute' => ['dealer_id' => 'id']],
            ['attribute_value', 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'dealer_id' => Yii::t('app', 'Tên đại lý'),
            'name' => Yii::t('app', 'Tên chương trình ưu đãi'),
            'status' => Yii::t('app', 'Trạng thái'),
            'cp_order_id' => Yii::t('app', 'Cp Order ID'),
            'total_promotion_code' => Yii::t('app', 'Số lượng mã'),
            'created_at' => Yii::t('app', 'Thời gian tạo'),
            'updated_at' => Yii::t('app', 'Thời gian thay đổi thông tin'),
            'expired_time' => Yii::t('app', 'Thời gian hết hạn'),
            'active_time' => Yii::t('app', 'Thời gian active'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttributeValues()
    {
        return $this->hasMany(AttributeValue::className(), ['promotion_id' => 'id']);
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
    public function getCp()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPromotionCodes()
    {
        return $this->hasMany(PromotionCode::className(), ['promotion_id' => 'id']);
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

    public function getValueCurrent($id)
    {
        $model = AttributeValue::findOne([
            'attribute_id' => $id,
            'dealer_id' => User::findOne(Yii::$app->user->id)->dealer_id,
            'type' => AttributeValue::TYPE_BALANCE,
            'status' => AttributeValue::STATUS_ACTIVE
        ]);
        if ($model) {
            return $model->value;
        } else {
            return 0;
        }
    }

    public function getNameCp()
    {
        $model = Dealer::findOne($this->dealer_id);
        if ($model) {
            return $model->full_name;
        }
        return '';
    }

    public function getNameCodeCp()
    {
        $model = Dealer::findOne($this->dealer_id);
        if ($model) {
            return $model->name_code;
        }
        return '';
    }

    public function getDisplayAttribute()
    {
        $array_values = AttributeValue::find()
            ->andWhere(['type' => AttributeValue::TYPE_PROMOTION])
            ->andWhere(['promotion_id' => $this->id])
            ->andWhere(['status' => AttributeValue::STATUS_ACTIVE])
            ->all();
        if (!$array_values) {
            return '';
        }
        $columns = [];
        /** @var AttributeValue $item */
        foreach ($array_values as $item) {
            $columns[] = [
                'label' => Yii::t('app', 'Số ') . Attribute::findOne($item->attribute_id)->name . Yii::t('app', ' tương ứng với một mã'),
                'value' => $item->value,
            ];
        }
        return $columns;
    }

    public function listPromotionByCP($cp_id)
    {
        $listPromotion = Promotion::find()->where(['dealer_id' => $cp_id])
            ->andWhere(['status' => Promotion::STATUS_ACTIVE])
            ->andWhere(['>', 'expired_time', time()])
            ->andWhere(['gen_code' => Promotion::STATUS_GEN_CODE])->all();
        $lst = [];
        /** @var \common\models\Promotion $promotion */
        foreach ($listPromotion as $promotion) {
            $lst[$promotion->id] = $promotion->name;
        }
        return $lst;
    }

    public function listDealer()
    {
        $listDealer = Dealer::find()->all();
        $lst = [];
        /** @var \common\models\Promotion $promotion */
        foreach ($listDealer as $dealer) {
            $lst[$dealer->id] = $dealer->full_name;
        }
        return $lst;
    }

    public function getDisplayValue($id)
    {
        $model = AttributeValue::find()
            ->andWhere(['promotion_id' => $this->id])
            ->andWhere(['type' => AttributeValue::TYPE_PROMOTION])
            ->andWhere(['status' => AttributeValue::STATUS_ACTIVE])
            ->andWhere(['attribute_id' => $id])
            ->one();
        if ($model) {
            return $model->value;
        } else {
            return 0;
        }
    }

}
