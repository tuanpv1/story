<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "access_system".
 *
 * @property int $id
 * @property int $subscriber_id
 * @property string $ip_address
 * @property string $user_agent
 * @property int $site_id
 * @property int $access_date
 * @property int $created_at
 * @property int $updated_at
 * @property string $action
 * @property string $request_detail
 * @property string $request_params
 *
 * @property Subscriber $subscriber
 */
class AccessSystem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'access_system';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['subscriber_id', 'site_id', 'access_date', 'created_at', 'updated_at'], 'integer'],
            [['access_date'], 'required'],
            [['request_params'], 'string'],
            [['ip_address'], 'string', 'max' => 45],
            [['user_agent', 'request_detail'], 'string', 'max' => 255],
            [['action'], 'string', 'max' => 126],
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
            'ip_address' => 'Ip Address',
            'user_agent' => 'User Agent',
            'site_id' => 'Site ID',
            'access_date' => 'Access Date',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'action' => 'Action',
            'request_detail' => 'Request Detail',
            'request_params' => 'Request Params',
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
