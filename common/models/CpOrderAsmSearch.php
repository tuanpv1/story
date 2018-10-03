<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CpOrderAsmSearch represents the model behind the search form about `common\models\CpOrderAsm`.
 */
class CpOrderAsmSearch extends CpOrderAsm
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'cp_order_id', 'dealer_id', 'transaction_time'], 'safe'],
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
        $query = CpOrderAsm::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        if(isset($params['cp_id'])){
            $model = Dealer::findOne($params['cp_id']);
            if($model){
                $this->dealer_id = $model->full_name;
            }else{
                $this->dealer_id = '';
            }
        }
        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
        ]);

        if($this->cp_order_id){
            $query->leftJoin('cp_order','cp_order.id=cp_order_asm.cp_order_id')
                ->andFilterWhere(['like', 'cp_order.name', $this->cp_order_id]);
        }

        if($this->dealer_id){
            $query->leftJoin('dealer','dealer.id=cp_order_asm.dealer_id')
                ->andFilterWhere(['like', 'dealer.full_name', $this->dealer_id]);
        }

        if ($this->transaction_time) {
            $from_date = strtotime($this->transaction_time . ' 00:00:00');
            $to_date = strtotime($this->transaction_time . ' 23:59:59');
            $query->andFilterWhere(['>=', 'cp_order_asm.transaction_time', $from_date]);
            $query->andFilterWhere(['<=', 'cp_order_asm.transaction_time', $to_date]);
        }

        return $dataProvider;
    }

    public function searchOrderByCP($params,$cp_id)
    {
        $query = CpOrderAsm::find()
            ->where(['dealer_id'=>$cp_id]);

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

        if($this->cp_order_id){
            $query->leftJoin('cp_order','cp_order.id=cp_order_asm.cp_order_id')
                ->andFilterWhere(['like', 'cp_order.name', $this->cp_order_id]);
        }

        if ($this->transaction_time) {
            $from_date = strtotime($this->transaction_time . ' 00:00:00');
            $to_date = strtotime($this->transaction_time . ' 23:59:59');
            $query->andFilterWhere(['>=', 'cp_order_asm.transaction_time', $from_date]);
            $query->andFilterWhere(['<=', 'cp_order_asm.transaction_time', $to_date]);
        }

        return $dataProvider;
    }
}
