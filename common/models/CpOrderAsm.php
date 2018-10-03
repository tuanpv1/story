<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "cp_order_asm".
 *
 * @property int $id
 * @property int $dealer_id
 * @property int $cp_order_id
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $transaction_time
 *
 */
class CpOrderAsm extends \yii\db\ActiveRecord
{
    public $attribute_value;

    const STATUS_ACTIVE = 10;
    const STATUS_INACTIVE = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cp_order_asm';
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
            [['dealer_id', 'status', 'cp_order_id'], 'required'],
            [['dealer_id', 'status', 'cp_order_id', 'created_at', 'updated_at','transaction_time'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dealer_id' => Yii::t('app','Tên đại lý'),
            'cp_order_id' => Yii::t('app','Loại đơn hàng'),
            'status' => Yii::t('app','Trạng thái'),
            'created_at' => Yii::t('app','Ngày tạo'),
            'updated_at' => 'Updated At',
            'transaction_time' => Yii::t('app','Thời gian tạo'),
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
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
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


    public static function listOrder()
    {
        $listOrder = CpOrder::find()->where(['status'=>CpOrder::STATUS_ACTIVE])->all();
        $lst = [];
        /** @var \common\models\CpOrder $item */
        foreach ($listOrder as $item) {
            $lst[$item->id] = $item->name;
        }
        return $lst;
    }

    public static function listCP()
    {
        $listCP = Dealer::find()->where(['status'=>Dealer::STATUS_ACTIVE])->all();
        $lst = [];
        /** @var \common\models\Dealer $dealer */
        foreach ($listCP as $dealer) {
            $lst[$dealer->id] = $dealer->full_name;
        }
        return $lst;
    }

    public static function getCpOrderName($order_id){
        $model = CpOrder::findOne($order_id);
        /** @var \common\models\CpOrder $model */
        if(!$model)
        {
            return '';
        }
        return $model->name;

    }

    public static function getCpName($id){
        $model = Dealer::findOne($id);
        /** @var \common\models\Dealer $model */
        if(!$model)
        {
            return '';
        }
        return $model->full_name;

    }
}
