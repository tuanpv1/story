<?php

namespace common\models;

use common\helpers\Encrypt;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * PromotionCodeSearch represents the model behind the search form of `common\models\PromotionCode`.
 */
class PromotionCodeSearch extends PromotionCode
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['code', 'receiver', 'promotion_id'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
    public function search($params, $cp_id)
    {
        $query = PromotionCode::find()
            ->innerJoin('promotion', 'promotion.id = promotion_code.promotion_id')
            ->where(['promotion.dealer_id' => $cp_id]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }


        // grid filtering conditions
        $query->andFilterWhere([
            'promotion_code.id' => $this->id,
            'promotion_code.status' => $this->status,
        ]);


        if ($this->code) {
            $query->andFilterWhere(['like', 'promotion_code.code', Encrypt::encryptCode($this->code, Yii::$app->params['key_encrypt'])]);
        }
        if ($this->promotion_id) {
            $query->andFilterWhere(['like', 'promotion.name', $this->promotion_id]);
        }
        if ($this->receiver){
            if (strtoupper($this->receiver) == "N/A"){
//                var_dump($this->receiver);die();
                $query->andFilterWhere(['promotion_code.status' => PromotionCode::STATUS_NOT_USED]);
            }else{
                $query->andFilterWhere(['like', 'promotion_code.receiver', $this->receiver]);
            }
        }

        return $dataProvider;
    }
}
