<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "subscriber_favorite".
 *
 * @property int $id
 * @property int $subscriber_id
 * @property int $content_id
 * @property int $created_at
 * @property int $updated_at
 * @property int $site_id
 * @property int $type 1: video, 2: live, 3: music, 4:news, 5: clips, 6:karaoke, 7:radio, 8: live_content
 *
 * @property Subscriber $subscriber
 * @property Content $content
 */
class SubscriberFavorite extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subscriber_favorite';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['subscriber_id', 'content_id', 'site_id'], 'required'],
            [['subscriber_id', 'content_id', 'created_at', 'updated_at', 'site_id', 'type'], 'integer'],
            [['subscriber_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subscriber::className(), 'targetAttribute' => ['subscriber_id' => 'id']],
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
            'content_id' => 'Content ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'site_id' => 'Site ID',
            'type' => 'Type',
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
    public function getContent()
    {
        return $this->hasOne(Content::className(), ['id' => 'content_id']);
    }
}
