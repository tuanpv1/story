<?php
namespace backend\models;
use yii\base\Model;

class ViewUserForm extends Model
{
    public $username;

    /**
     * @var \common\models\User
     */
    private $user;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'safe'],
        ];
    }

}
