<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "subscriber_token".
 *
 * @property int $id
 * @property int $subscriber_id
 * @property string $package_name
 * @property string $msisdn
 * @property string $token
 * @property int $type 1 - wifi password 2 - access token 
 * @property string $ip_address
 * @property int $created_at
 * @property int $expired_at
 * @property string $cookies
 * @property int $status
 * @property int $channel
 * @property string $device_name
 * @property string $device_model
 * @property string $device_id
 *
 * @property Subscriber $subscriber
 */
class SubscriberToken extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subscriber_token';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['subscriber_id', 'package_name', 'token'], 'required'],
            [['subscriber_id', 'type', 'created_at', 'expired_at', 'status', 'channel'], 'integer'],
            [['package_name', 'device_name', 'device_model', 'device_id'], 'string', 'max' => 255],
            [['msisdn'], 'string', 'max' => 20],
            [['token'], 'string', 'max' => 100],
            [['ip_address'], 'string', 'max' => 45],
            [['cookies'], 'string', 'max' => 1000],
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
            'package_name' => 'Package Name',
            'msisdn' => 'Msisdn',
            'token' => 'Token',
            'type' => 'Type',
            'ip_address' => 'Ip Address',
            'created_at' => 'Created At',
            'expired_at' => 'Expired At',
            'cookies' => 'Cookies',
            'status' => 'Status',
            'channel' => 'Channel',
            'device_name' => 'Device Name',
            'device_model' => 'Device Model',
            'device_id' => 'Device ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriber()
    {
        return $this->hasOne(Subscriber::className(), ['id' => 'subscriber_id']);
    }
}
