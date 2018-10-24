<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "city".
 *
 * @property int $id
 * @property string $name Tỉnh/thành phố
 * @property string $code Mã Tỉnh/thành phố
 * @property string $ascii_name
 */
class City extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'city';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'code'], 'required'],
            [['name'], 'string', 'max' => 100],
            [['code'], 'string', 'max' => 50],
            [['ascii_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'code' => 'Code',
            'ascii_name' => 'Ascii Name',
        ];
    }
}
