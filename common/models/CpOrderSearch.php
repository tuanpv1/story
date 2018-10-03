<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * AttributeSearch represents the model behind the search form about `common\models\Attribute`.
 */
class CpOrderSearch extends CpOrder
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'created_at', 'name', 'updated_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
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
        $query = CpOrder::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        if ($this->created_at) {
            $from_date = strtotime($this->created_at . ' 00:00:00');
            $to_date = strtotime($this->created_at . ' 23:59:59');
            $query->andFilterWhere(['>=', 'cp_order.created_at', $from_date]);
            $query->andFilterWhere(['<=', 'cp_order.created_at', $to_date]);
        }

        return $dataProvider;
    }

    public function searchOrderByCP($params,$cp_id)
    {
        $query = CpOrderAsm::find()
            ->where(['user_id'=>$cp_id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        return $dataProvider;
    }
}
