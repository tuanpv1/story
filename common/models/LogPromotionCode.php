<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "log_promotion_code".
 *
 * @property int $id
 * @property int $promotion_code_id
 * @property int $status
 * @property int $type
 * @property string $des
 * @property string $receiver
 * @property string $receiver_info
 * @property int $created_at
 * @property int $updated_at
 * @property int $error_code
 * @property int $partner_id
 * @property int $partner_transaction_id
 * @property string $promotion_code
 *
 * @property PromotionCode $promotionCode
 */
class LogPromotionCode extends \yii\db\ActiveRecord
{
    const STATUS_SUCCESS = 10;
    const STATUS_ERROR = 0;

    const TYPE_CHECK_CODE = 1;
    const TYPE_USE_CODE = 2;
    const TYPE_ROLL_BACK_CODE = 3; // Sử dụng trường hợp đối tác áp dụng mã không thành công

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'log_promotion_code';
    }

    public static function addNewRecord($promotion_code_id, $receiver, $receiver_info, $transaction_partner_id, $partner_id, $status, $error_code, $des, $type, $promotion_code)
    {
        $model_log = new LogPromotionCode();
        $model_log->promotion_code_id = $promotion_code_id;
        $model_log->promotion_code = $promotion_code;
        $model_log->receiver = $receiver;
        $model_log->receiver_info = $receiver_info;
        $model_log->partner_transaction_id = $transaction_partner_id;
        $model_log->partner_id = $partner_id;
        $model_log->status = $status;
        $model_log->type = $type;
        $model_log->error_code = $error_code;
        $model_log->des = $des;
        if (!$model_log->save()) {
            Yii::info($model_log->getErrors());
            return false;
        }
        return $model_log;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'type', 'error_code'], 'required'],
            [['promotion_code_id', 'status', 'type', 'created_at', 'updated_at', 'error_code', 'partner_transaction_id', 'partner_id'], 'integer'],
            [['des'], 'string', 'max' => 500],
            [['receiver'], 'string', 'max' => 45],
            [['receiver_info', 'promotion_code'], 'string', 'max' => 500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'promotion_code_id' => 'Promotion Code ID',
            'status' => 'Status',
            'type' => 'Type',
            'des' => 'Des',
            'phone' => 'Phone',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'error_code' => 'Error Code',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPromotionCode()
    {
        return $this->hasOne(PromotionCode::className(), ['id' => 'promotion_code_id']);
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
}
