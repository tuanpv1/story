<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "subscriber_feedback".
 *
 * @property int $id
 * @property int $subscriber_id
 * @property string $content
 * @property string $title
 * @property int $create_date
 * @property int $status
 * @property string $status_log
 * @property int $is_responsed
 * @property string $response_date
 * @property string $response_user_id
 * @property string $response_detail
 * @property int $site_id
 * @property int $content_id
 *
 * @property Subscriber $subscriber
 */
class SubscriberFeedback extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subscriber_feedback';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['subscriber_id', 'content', 'status', 'site_id'], 'required'],
            [['subscriber_id', 'create_date', 'status', 'is_responsed', 'response_user_id', 'site_id', 'content_id'], 'integer'],
            [['status_log'], 'string'],
            [['response_date'], 'safe'],
            [['content', 'response_detail'], 'string', 'max' => 5000],
            [['title'], 'string', 'max' => 500],
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
            'content' => 'Content',
            'title' => 'Title',
            'create_date' => 'Create Date',
            'status' => 'Status',
            'status_log' => 'Status Log',
            'is_responsed' => 'Is Responsed',
            'response_date' => 'Response Date',
            'response_user_id' => 'Response User ID',
            'response_detail' => 'Response Detail',
            'site_id' => 'Site ID',
            'content_id' => 'Content ID',
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
