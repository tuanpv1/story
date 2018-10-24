<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "subscriber_transaction".
 *
 * @property int $id
 * @property int $subscriber_id
 * @property string $msisdn
 * @property int $payment_type 1 - thanh toan tra truoc = tien ao 2 - thanh toan tra truoc = sms 3 - nap tiep/thanh toan tra sau
 * @property int $type 1 : mua moi 2 : gia han 3 : subscriber chu dong huy 4 : bi provider huy,  5: pending,  6: restore 7 : mua dich vu 8 : mua de xem 9 : mua de download 10 : mua de tang 11: tang goi cuoc 100: nap tien qua sms
 * @property int $service_id
 * @property int $content_id
 * @property int $transaction_time
 * @property int $created_at
 * @property int $updated_at
 * @property int $status 10 : success 0 : fail 
 * @property string $shortcode đầu số nhắn tin
 * @property string $description
 * @property double $cost
 * @property int $channel Kenh thuc hien giao dich: WAP, SMS
 * @property string $event_id Ma phan biet cac nhom noi dung...
 * @property string $error_code
 * @property string $subscriber_activity_id
 * @property int $subscriber_service_asm_id
 * @property int $site_id
 * @property int $dealer_id
 * @property string $application
 * @property double $balance
 * @property string $currency
 * @property string $card_serial
 * @property string $card_code
 * @property int $transaction_voucher_id
 * @property int $white_list
 * @property string $cp_id
 * @property string $order_id
 * @property int $expired_time
 * @property string $smartgate_transaction_id
 * @property int $smartgate_transaction_timeout
 * @property double $balance_before_charge
 * @property string $gateway
 * @property int $number_month
 * @property int $is_first_package
 *
 * @property Subscriber $subscriber
 * @property SubscriberActivity $subscriberActivity
 * @property Content $content
 */
class SubscriberTransaction extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subscriber_transaction';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['subscriber_id', 'status', 'site_id'], 'required'],
            [['subscriber_id', 'payment_type', 'type', 'service_id', 'content_id', 'transaction_time', 'created_at', 'updated_at', 'status', 'channel', 'subscriber_activity_id', 'subscriber_service_asm_id', 'site_id', 'dealer_id', 'transaction_voucher_id', 'white_list', 'expired_time', 'smartgate_transaction_timeout', 'number_month', 'is_first_package'], 'integer'],
            [['cost', 'balance', 'balance_before_charge'], 'number'],
            [['msisdn', 'error_code'], 'string', 'max' => 20],
            [['shortcode'], 'string', 'max' => 45],
            [['description'], 'string', 'max' => 500],
            [['event_id'], 'string', 'max' => 10],
            [['application', 'card_serial', 'card_code', 'smartgate_transaction_id'], 'string', 'max' => 255],
            [['currency'], 'string', 'max' => 4],
            [['cp_id'], 'string', 'max' => 100],
            [['order_id', 'gateway'], 'string', 'max' => 50],
            [['subscriber_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subscriber::className(), 'targetAttribute' => ['subscriber_id' => 'id']],
            [['subscriber_activity_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubscriberActivity::className(), 'targetAttribute' => ['subscriber_activity_id' => 'id']],
            [['content_id'], 'exist', 'skipOnError' => true, 'targetClass' => Content::className(), 'targetAttribute' => ['content_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'subscriber_id' => 'Subscriber ID',
            'msisdn' => 'Msisdn',
            'payment_type' => 'Payment Type',
            'type' => 'Type',
            'service_id' => 'Service ID',
            'content_id' => 'Content ID',
            'transaction_time' => 'Transaction Time',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'status' => 'Status',
            'shortcode' => 'Shortcode',
            'description' => 'Description',
            'cost' => 'Cost',
            'channel' => 'Channel',
            'event_id' => 'Event ID',
            'error_code' => 'Error Code',
            'subscriber_activity_id' => 'Subscriber Activity ID',
            'subscriber_service_asm_id' => 'Subscriber Service Asm ID',
            'site_id' => 'Site ID',
            'dealer_id' => 'Dealer ID',
            'application' => 'Application',
            'balance' => 'Balance',
            'currency' => 'Currency',
            'card_serial' => 'Card Serial',
            'card_code' => 'Card Code',
            'transaction_voucher_id' => 'Transaction Voucher ID',
            'white_list' => 'White List',
            'cp_id' => 'Cp ID',
            'order_id' => 'Order ID',
            'expired_time' => 'Expired Time',
            'smartgate_transaction_id' => 'Smartgate Transaction ID',
            'smartgate_transaction_timeout' => 'Smartgate Transaction Timeout',
            'balance_before_charge' => 'Balance Before Charge',
            'gateway' => 'Gateway',
            'number_month' => 'Number Month',
            'is_first_package' => 'Is First Package',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriber()
    {
        return $this->hasOne(Subscriber::className(), ['id' => 'subscriber_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriberActivity()
    {
        return $this->hasOne(SubscriberActivity::className(), ['id' => 'subscriber_activity_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContent()
    {
        return $this->hasOne(Content::className(), ['id' => 'content_id']);
    }
}
