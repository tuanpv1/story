<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;

/**
 * DealerSearch represents the model behind the search form about `common\models\Dealer`.
 */
class DealerSearch extends Dealer
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status',  ], 'integer'],
            [['id', 'username', 'full_name', 'phone_number', 'email', 'name_code'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Dealer::find()->where(['<>','status',Dealer::STATUS_DELETED]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'name_code', $this->name_code]);
        $query->andFilterWhere(['like', 'phone_number', $this->phone_number]);
        $query->andFilterWhere(['like', 'email', $this->email]);

        if($this->username){
            $query->innerJoin('user','user.dealer_id = dealer.id')
                ->andWhere(['like','user.username',$this->username]);
        }

        return $dataProvider;
    }
}
