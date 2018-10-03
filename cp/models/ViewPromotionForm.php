<?php
namespace cp\models;
use yii\base\Model;

class ViewPromotionForm extends Model
{
    public $promotion_name;

    /**
     * @var \common\models\Promotion
     */

    private $promotion;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['promotion_name', 'safe'],
        ];
    }

}
