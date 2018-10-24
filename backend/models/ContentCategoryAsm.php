<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "content_category_asm".
 *
 * @property int $id
 * @property int $content_id
 * @property int $category_id
 */
class ContentCategoryAsm extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'content_category_asm';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['content_id', 'category_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'content_id' => 'Content ID',
            'category_id' => 'Category ID',
        ];
    }
}
