<?php

namespace common\models;

use common\helpers\Encrypt;
use Exception;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "promotion_code".
 *
 * @property int $id
 * @property string $code
 * @property int $expired_at
 * @property int $status 0 inactive 10 active
 * @property int $created_at
 * @property int $updated_at
 * @property string $receiver
 * @property string $receiver_info
 * @property int $promotion_id
 *
 * @property LogPromotionCode[] $logPromotionCodes
 * @property Promotion $promotion
 */
class PromotionCode extends \yii\db\ActiveRecord
{
    const STATUS_NOT_USED = 0;
    /** chưa sử dung*/
    const STATUS_USED = 10;
    /** đã sử dung*/
    const STATUS_EXPIRED = 1;
    /** đã hết hạn*/

    const EXCEL_ROW1 = 'STT';
    const EXCEL_ROW2 = 'Mã ưu đãi';
    const EXCEL_ROW3 = 'Chương trình ưu đãi';
    const EXCEL_ROW4 = 'Đại lý';
    const EXCEL_ROW5 = 'Thời gian tạo';

    const ERROR_SUCCESS = 0; // Mã khuyễn mãi hoạt động đối với check và thành công đối với sử dụng mã

    const ERROR_INVALID_SIGNATURE = 1; // Chữ kí không đúng
    const ERROR_INVALID_CODE = 2; // Mã khuyến mãi không đúng
    const ERROR_CODE_EXPIRED = 3; // Mã khuyến mãi hết hạn
    const ERROR_USED_CODE = 4; // Mã khuyến mãi Đã được sử dụng
    const ERROR_INVALID_PARTNER = 5; // Partner không đúng

    const ERROR_MISS_RECEIVER = 6; // Thiếu người dùng
    const ERROR_MISS_RECEIVER_INFO = 7; // Thiếu json người dùng
    const ERROR_MISS_CODE = 8; // Thiếu Code
    const ERROR_MISS_SIGN = 9; // THiếu chữ kí
    const ERROR_MISS_TRANSACTION_ID = 10; // Thiếu transaction id của partner
    const ERROR_MISS_PARTNER_ID = 11; // Thiếu id partner

    const ERROR_SERVER = 12; // Lỗi hệ thống

    const EXPIRED_FOREVER = 0; // Nếu mã có expired_at = 0 thì không có thời hạn sử dụng

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'promotion_code';
    }

    public static function checkPromotionCode($receiver, $receiver_info, $partner_id, $transaction_partner_id, $code_decrypt)
    {
        // Mã hóa lại theo key mã hóa db để tìm mã
        $promotion_code = Encrypt::encryptCode($code_decrypt, Yii::$app->params['key_encrypt']);
        $type = LogPromotionCode::TYPE_CHECK_CODE;
        $model = PromotionCode::findOne(['code' => $promotion_code]);
        // Trả về mã không tồn tại
        if (!$model) {
            return ['success' => false, 'error_code' => PromotionCode::ERROR_INVALID_CODE];
        }
        // kiểm tra đã được dùng chưa
        if ($model->status == self::STATUS_USED) {
            return ['success' => false, 'error_code' => PromotionCode::ERROR_USED_CODE];
        }
        // Kiểm tra mã hết hạn
        if ($model->expired_at != PromotionCode::EXPIRED_FOREVER) {
            if ($model->expired_at <= time() || $model->status == self::STATUS_EXPIRED) {
                return ['success' => false, 'error_code' => PromotionCode::ERROR_CODE_EXPIRED];
            }
        }
        if ($model->status == self::STATUS_NOT_USED) {
            $promotion = Promotion::findOne($model->promotion_id);
            if (!$promotion) {
                return ['success' => false, 'error_code' => PromotionCode::ERROR_INVALID_CODE];
            }
            $model_dealer = Dealer::findOne($promotion->dealer_id);
            if(!$model_dealer){
                return ['success' => false, 'error_code' => PromotionCode::ERROR_INVALID_CODE];
            }
            if($model_dealer->status != Dealer::STATUS_ACTIVE){
                return ['success' => false, 'error_code' => PromotionCode::ERROR_INVALID_CODE];
            }
            $log = LogPromotionCode::addNewRecord($model->id, $receiver, $receiver_info, $transaction_partner_id, $partner_id, LogPromotionCode::STATUS_SUCCESS, PromotionCode::ERROR_SUCCESS, Yii::t('app', 'Kiểm tra mã code thành công ' . $code_decrypt), $type, $code_decrypt);
            if ($log) {
                return ['success' => true, 'error_code' => PromotionCode::ERROR_SUCCESS, 'dealer_id' => $promotion->dealer_id, 'transaction_server_id' => $log->id];
            } else {
                return ['success' => false, 'error_code' => PromotionCode::ERROR_SERVER];
            }
        }

        return ['success' => false, 'error_code' => PromotionCode::ERROR_SERVER];
    }

    public static function addPhoneToCode($receiver, $receiver_info, $partner_id, $transaction_partner_id, $code_decrypt)
    {
        // Mã hóa lại theo key mã hóa db để tìm mã
        $promotion_code = Encrypt::encryptCode($code_decrypt, Yii::$app->params['key_encrypt']);
        $type = LogPromotionCode::TYPE_USE_CODE;
        $model = PromotionCode::findOne(['code' => $promotion_code]);
        // Trả về mã không tồn tại
        if (!$model) {
            return ['success' => false, 'error_code' => PromotionCode::ERROR_INVALID_CODE];
        }
        // kiểm tra đã được dùng chưa
        if ($model->status == self::STATUS_USED) {
            if (LogPromotionCode::addNewRecord($model->id, $receiver, $receiver_info, $transaction_partner_id, $partner_id, LogPromotionCode::STATUS_ERROR, PromotionCode::ERROR_USED_CODE, Yii::t('app', 'Mã đã được sử dụng'), $type, $code_decrypt)) {
                return ['success' => false, 'error_code' => PromotionCode::ERROR_USED_CODE];
            } else {
                return ['success' => false, 'error_code' => PromotionCode::ERROR_SERVER];
            }
        }
        // Kiểm tra mã hết hạn
        if ($model->expired_at != PromotionCode::EXPIRED_FOREVER) {
            if ($model->expired_at <= time() || $model->status == self::STATUS_EXPIRED) {
                if (LogPromotionCode::addNewRecord($model->id, $receiver, $receiver_info, $transaction_partner_id, $partner_id, LogPromotionCode::STATUS_ERROR, PromotionCode::ERROR_CODE_EXPIRED, Yii::t('app', 'Mã đã hết hạn sử dụng'), $type, $code_decrypt)) {
                    return ['success' => false, 'error_code' => PromotionCode::ERROR_CODE_EXPIRED];
                } else {
                    return ['success' => false, 'error_code' => PromotionCode::ERROR_SERVER];
                }
            }
        }
        if ($model->status == self::STATUS_NOT_USED) {
            $promotion = Promotion::findOne($model->promotion_id);
            if (!$promotion) {
                return ['success' => false, 'error_code' => PromotionCode::ERROR_INVALID_CODE];
            }
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $model->receiver = $receiver;
                $model->status = self::STATUS_USED;
                $model->receiver_info = $receiver_info;
                if (!$model->save()) {
                    if (LogPromotionCode::addNewRecord($model->id, $receiver, $receiver_info, $transaction_partner_id, $partner_id, LogPromotionCode::STATUS_ERROR, PromotionCode::ERROR_INVALID_CODE, Yii::t('app', 'Lỗi hệ thống ' . $model->getFirstError()), $type, $code_decrypt)) {
                        return ['success' => false, 'error_code' => PromotionCode::ERROR_SERVER];
                    }
                }

                $des = Yii::t('app', 'Sử dụng mã ' . $promotion_code . ' cho người dùng ' . $receiver . ' thành công');
                $error_code = PromotionCode::ERROR_SUCCESS;
                $model_log = LogPromotionCode::addNewRecord($model->id, $receiver, $receiver_info, $transaction_partner_id, $partner_id, LogPromotionCode::STATUS_SUCCESS, $error_code, $des, $type, $code_decrypt);
                if ($model_log) {
                    $array_value = [];
                    // Tìm thuộc tính động và giá trị của mã để trả về
                    $attribute_values = AttributeValue::find()
                        ->select('attribute_value.*')
                        ->innerJoin('partner_attribute_asm', 'partner_attribute_asm.attribute_id = attribute_value.attribute_id')
                        ->andWhere(['partner_attribute_asm.partner_id' => $partner_id])
                        ->andWhere(['partner_attribute_asm.status' => PartnerAttributeAsm::STATUS_ACTIVE])
                        ->andWhere(['attribute_value.promotion_id' => $promotion->id])
                        ->andWhere(['attribute_value.status' => AttributeValue::STATUS_ACTIVE])
                        ->orderBy(['partner_attribute_asm.order' => SORT_ASC])
                        ->all();
                    if ($attribute_values) {
                        foreach ($attribute_values as $attribute_value) {
                            array_push($array_value, $attribute_value->value);
                        }
                    }
                    $transaction->commit();
                    return [
                        'success' => true,
                        'error_code' => PromotionCode::ERROR_SUCCESS,
                        'transaction_server_id' => $model_log->id,
                        'value' => $array_value,
                        'cp_id' => $promotion->getNameCodeCp(),
                        'name_cp' => $promotion->getNameCp()
                    ];
                } else {
                    return ['success' => false, 'error_code' => PromotionCode::ERROR_SERVER];
                }
            } catch (Exception $e) {
                $transaction->rollBack();
                return ['success' => false, 'error_code' => PromotionCode::ERROR_SERVER];
            }
        }
        return ['success' => false, 'error_code' => PromotionCode::ERROR_SERVER];
    }

    public static function rollBack($receiver, $receiver_info, $partner_id, $transaction_partner_id, $code_decrypt)
    {
        // Mã hóa lại theo key mã hóa db để tìm mã
        $promotion_code = Encrypt::encryptCode($code_decrypt, Yii::$app->params['key_encrypt']);
        $type = LogPromotionCode::TYPE_USE_CODE;
        $model = PromotionCode::findOne(['code' => $promotion_code]);
        // Trả về mã không tồn tại
        if (!$model) {
            return ['success' => false, 'error_code' => PromotionCode::ERROR_INVALID_CODE];
        }

        // Rollback
        $promotion = Promotion::findOne($model->promotion_id);
        if (!$promotion) {
            return ['success' => false, 'error_code' => PromotionCode::ERROR_INVALID_CODE];
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model->receiver = '';
            $model->status = self::STATUS_NOT_USED;
            $model->receiver_info = '';
            if (!$model->save()) {
                if (LogPromotionCode::addNewRecord($model->id, $receiver, $receiver_info, $transaction_partner_id, $partner_id, LogPromotionCode::STATUS_ERROR, PromotionCode::ERROR_INVALID_CODE, Yii::t('app', 'Lỗi hệ thống ' . $model->getFirstError()), $type, $code_decrypt)) {
                    return ['success' => false, 'error_code' => PromotionCode::ERROR_SERVER];
                }
            }

            $des = Yii::t('app', 'Roll back mã ' . $promotion_code . ' thành công');
            $error_code = PromotionCode::ERROR_SUCCESS;
            $model_log = LogPromotionCode::addNewRecord($model->id, $receiver, $receiver_info, $transaction_partner_id, $partner_id, LogPromotionCode::STATUS_SUCCESS, $error_code, $des, $type, $code_decrypt);
            if ($model_log) {
                $transaction->commit();
                return ['success' => true, 'error_code' => PromotionCode::ERROR_SUCCESS, 'transaction_server_id' => $model_log->id];
            } else {
                return ['success' => false, 'error_code' => PromotionCode::ERROR_SERVER];
            }
        } catch (Exception $e) {
            $transaction->rollBack();
            return ['success' => false, 'error_code' => PromotionCode::ERROR_SERVER];
        }
    }

    public static function validateApi($receiver, $promotion_code, $signature, $receiver_info, $transaction_partner_id, $partner_id)
    {
        Yii::info(
            'Nhan $receiver: ' . $receiver .
            ' Nhan $promotion_code: ' . $promotion_code .
            ' Nhan $signature: ' . $signature .
            ' Nhan $receiver_info: ' . $receiver_info .
            ' Nhan $transaction_partner_id: ' . $transaction_partner_id .
            ' Nhan $partner_id: ' . $partner_id
        );

        if (empty($receiver)) {
            return ['success' => false, 'error_code' => PromotionCode::ERROR_MISS_RECEIVER];
        }

        if (empty($receiver_info)) {
            return ['success' => false, 'error_code' => PromotionCode::ERROR_MISS_RECEIVER_INFO];
        }

        if (empty($promotion_code)) {
            return ['success' => false, 'error_code' => PromotionCode::ERROR_MISS_CODE];
        }

        if (empty($signature)) {
            return ['success' => false, 'error_code' => PromotionCode::ERROR_MISS_SIGN];
        }

        if (empty($transaction_partner_id)) {
            return ['success' => false, 'error_code' => PromotionCode::ERROR_MISS_TRANSACTION_ID];
        }

        if (empty($partner_id)) {
            return ['success' => false, 'error_code' => PromotionCode::ERROR_MISS_PARTNER_ID];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'expired_at', 'status', 'promotion_id'], 'required'],
            [['expired_at', 'status', 'created_at', 'updated_at', 'promotion_id'], 'integer'],
            [['code', 'receiver'], 'string', 'max' => 45],
            [['receiver_info'], 'string', 'max' => 500],
            [['code'], 'unique'],
            [['promotion_id'], 'exist', 'skipOnError' => true, 'targetClass' => Promotion::className(), 'targetAttribute' => ['promotion_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => Yii::t('app', 'Mã ưu đãi'),
            'expired_at' => Yii::t('app', 'Thời gian hết hạn'),
            'status' => Yii::t('app', 'Trạng thái'),
            'created_at' => Yii::t('app', 'Thời gian tạo'),
            'updated_at' => 'Updated At',
            'receiver' => Yii::t('app', 'Thuê bao sử dụng'),
            'promotion_id' => Yii::t('app', 'Tên chương trình ưu đãi'),
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
     * @return \yii\db\ActiveQuery
     */
    public function getLogPromotionCodes()
    {
        return $this->hasMany(LogPromotionCode::className(), ['promotion_code_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPromotion()
    {
        return $this->hasOne(Promotion::className(), ['id' => 'promotion_id']);
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

    public static function listStatus()
    {
        $lst = [
            self::STATUS_NOT_USED => \Yii::t('app', 'Chưa sử dụng'),
            self::STATUS_USED => \Yii::t('app', 'Đang sử dụng'),
            self::STATUS_EXPIRED => \Yii::t('app', 'Đã hết hạn'),
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

    public function getPromotionName()
    {
        /** @var Promotion $model */
        $model = Promotion::findOne($this->promotion_id);
        if ($model) {
            return $model->name;
        } else {
            return '';
        }
    }

    public function getCell($attr, $rowIdx)
    {
        switch ($attr) {
            case self::EXCEL_ROW1:
                return "A$rowIdx";
            case self::EXCEL_ROW2:
                return "B$rowIdx";
            case self::EXCEL_ROW3:
                return "C$rowIdx";
            case self::EXCEL_ROW4:
                return "D$rowIdx";
            case self::EXCEL_ROW5:
                return "E$rowIdx";
        }
        return '';
    }
}
