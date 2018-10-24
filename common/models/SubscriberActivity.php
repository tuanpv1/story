<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "subscriber_activity".
 *
 * @property string $id
 * @property int $subscriber_id
 * @property string $msisdn
 * @property int $action 1 - login 2 - logout 3 - xem 4 - download 5 - gift 6 - mua service 7 - chu dong huy service 8 - bi provider huy service 9 - gia han service ...
 * @property string $params
 * @property int $created_at
 * @property string $ip_address
 * @property int $status 10 - success 0 - fail
 * @property int $target_id
 * @property int $target_type
 * @property int $type
 * @property string $description
 * @property string $user_agent
 * @property int $channel sms, wap, web, android app, ios app...
 * @property int $site_id
 * @property int $device_id
 * @property int $type_subscriber
 *
 * @property Subscriber $subscriber
 * @property SubscriberTransaction[] $subscriberTransactions
 */
class SubscriberActivity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subscriber_activity';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['subscriber_id', 'site_id'], 'required'],
            [['subscriber_id', 'action', 'created_at', 'status', 'target_id', 'target_type', 'type', 'channel', 'site_id', 'device_id', 'type_subscriber'], 'integer'],
            [['params', 'description'], 'string'],
            [['msisdn'], 'string', 'max' => 20],
            [['ip_address'], 'string', 'max' => 45],
            [['user_agent'], 'string', 'max' => 255],
            [['subscriber_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subscriber::className(), 'targetAttribute' => ['subscriber_id' => 'id']],
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
            'action' => 'Action',
            'params' => 'Params',
            'created_at' => 'Created At',
            'ip_address' => 'Ip Address',
            'status' => 'Status',
            'target_id' => 'Target ID',
            'target_type' => 'Target Type',
            'type' => 'Type',
            'description' => 'Description',
            'user_agent' => 'User Agent',
            'channel' => 'Channel',
            'site_id' => 'Site ID',
            'device_id' => 'Device ID',
            'type_subscriber' => 'Type Subscriber',
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
    public function getSubscriberTransactions()
    {
        return $this->hasMany(SubscriberTransaction::className(), ['subscriber_activity_id' => 'id']);
    }
}
