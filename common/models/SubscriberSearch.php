<?php

namespace common\models;

use api\models\SubscriberServiceAsm;
use backend\models\ReportSubscriberForm;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SubscriberSearch represents the model behind the search form about `common\models\Subscriber`.
 */
class SubscriberSearch extends Subscriber
{

    public $site;
    public $dealer;
    public $service_id = null;
    public $type;
    public $from_date;
    public $to_date;

    public $serial;
    public $device_type;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'last_login_at', 'last_login_session', 'birthday', 'sex', 'client_type', 'using_promotion', 'auto_renew', 'verification_code'], 'integer'],
            [['msisdn', 'username','machine_name', 'email', 'full_name', 'avatar_url', 'skype_id', 'google_id', 'facebook_id', 'user_agent'], 'safe'],
            [['site_id', 'dealer_id', 'created_at', 'updated_at','register_at'], 'safe'],
            [['site', 'dealer','address','service_id','type','city','from_date','to_date','ip_address'], 'safe'],
            [['serial','device_type'],'safe']
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
        $query = Subscriber::find();

        $query->joinWith(['site' => function ($querySite) {
            $querySite->onCondition(['<>', 'site.status', Site::STATUS_REMOVE]);
        }, 'dealer' => function ($queryDl) {
            $queryDl->onCondition(['<>', 'dealer.status', Dealer::STATUS_DELETED]);
        }]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'updated_at' => SORT_DESC,
                ]
            ],
        ]);

        // Important: here is how we set up the sorting
        // The key is the attribute name on our "TourSearch" instance
        $dataProvider->sort->attributes['site'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['site.name' => SORT_ASC],
            'desc' => ['site.name' => SORT_DESC],
        ];
        // Lets do the same with country now
        $dataProvider->sort->attributes['dealer'] = [
            'asc' => ['dealer.name' => SORT_ASC],
            'desc' => ['dealer.name' => SORT_DESC],
        ];

        $this->load($params);
        // No search? Then return data Provider
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere([
            'subscriber.id' => $this->id,
            'subscriber.site_id' => $this->site_id,
            'subscriber.dealer_id' => $this->dealer_id,
            'subscriber.status' => $this->status,
            'subscriber.client_type' => $this->client_type,
        ]);

        if ($this->city) {
            $city = City::findOne(['name' => $this->city]);
            if ($city) {
                $query->andWhere(['province_code' => $city->code]);
            }
        }

        $query->andFilterWhere(['like', 'subscriber.msisdn', $this->msisdn])
            ->andFilterWhere(['like', 'subscriber.username', $this->username])
            ->andFilterWhere(['like', 'subscriber.email', $this->email])

            ->andFilterWhere(['like', 'subscriber.address', $this->address])

            ->andFilterWhere(['like', 'subscriber.ip_address', $this->ip_address]);


        if ($this->created_at) {
            $query->andFilterWhere(['>=', 'subscriber.created_at', strtotime($this->created_at)]);
        }
        if ($this->updated_at) {
            $query->andFilterWhere(['>=', 'subscriber.updated_at', strtotime($this->updated_at)]);
        }

        if($this->service_id) {
            $query->innerJoin('subscriber_service_asm', 'subscriber.id = subscriber_service_asm.subscriber_id')
                ->andWhere(['subscriber_service_asm.service_id' => $this->service_id])
                ->andWhere(['subscriber_service_asm.status'=>SubscriberServiceAsm::STATUS_ACTIVE])
                ->andWhere(['>=','subscriber_service_asm.expired_at',mktime()]);
        }

        $query ->andWhere(['=','subscriber.authen_type',Subscriber::AUTHEN_TYPE_ACCOUNT])
            ->andWhere(['is not','subscriber.username',null]);;

        $query->andOnCondition(['in', 'subscriber.status', [Subscriber::STATUS_ACTIVE, Subscriber::STATUS_INACTIVE, Subscriber::STATUS_DELETED]]);

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchExt($params)
    {
        $query = Subscriber::find();

        $query->joinWith(['site', 'dealer']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'updated_at' => SORT_DESC,
                ]
            ],
        ]);

        // Important: here is how we set up the sorting
        // The key is the attribute name on our "TourSearch" instance
        $dataProvider->sort->attributes['site'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['site.name' => SORT_ASC],
            'desc' => ['site.name' => SORT_DESC],
        ];
        // Lets do the same with country now
        $dataProvider->sort->attributes['dealer'] = [
            'asc' => ['dealer.name' => SORT_ASC],
            'desc' => ['dealer.name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'subscriber.id' => $this->id,
            'subscriber.status' => $this->status,
            'subscriber.client_type' => $this->client_type,
        ]);

//        if ($this->site_id) {
//            $query->joinWith('serviceProvider');
//            $query->andWhere(['like', 'site.name', $this->site_id]);
//        }


        if ($this->created_at) {
            $query->andFilterWhere(['>=', 'subscriber.created_at', strtotime($this->created_at)]);
        }
        if ($this->updated_at) {
            $query->andFilterWhere(['>=', 'subscriber.updated_at', strtotime($this->updated_at)]);
        }

        $query->andFilterWhere(['like', 'subscriber.msisdn', $this->msisdn])
            ->andFilterWhere(['like', 'site.name', $this->site])
            ->andFilterWhere(['like', 'dealer.name', $this->dealer]);

        return $dataProvider;
    }

    public function searchReportSubscriber($params){
        $query = Subscriber::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        if($this->type == ReportSubscriberForm::TYPE_EXPIRE){
            $query->innerJoin('subscriber_service_asm','subscriber.id=subscriber_service_asm.subscriber_id')
                ->addSelect(['subscriber.*',
                    'subscriber_service_asm.service_id as service_id',
                    'subscriber_service_asm.expired_at as time'])
                ->andWhere(['>=', 'subscriber_service_asm.expired_at', strtotime($this->from_date)])
                ->andWhere(['<=', 'subscriber_service_asm.expired_at', strtotime($this->to_date)])
                ->andWhere(['subscriber_service_asm.status'=> SubscriberServiceAsm::STATUS_ACTIVE]);
            if($this->site_id){
                $query->andWhere(['subscriber_service_asm.site_id'=>$this->site_id]);
            }
            if($this->service_id){
                $query->andWhere(['subscriber_service_asm.service_id'=>$this->service_id]);
            }
        }else{
            $query->innerJoin('subscriber_transaction','subscriber.id=subscriber_transaction.subscriber_id')
                ->addSelect(['subscriber_transaction.subscriber_id as subscriber_id',
                    'subscriber.full_name',
                    'subscriber.msisdn',
                    'subscriber.email',
                    'subscriber.address',
                    'subscriber.city',
                    'subscriber.username',
                    'subscriber.machine_name',
                    'subscriber_transaction.service_id as service_id',
                    'subscriber_transaction.transaction_time as time'])
                ->andWhere(['>=', 'subscriber_transaction.transaction_time', strtotime($this->from_date)])
                ->andWhere(['<=', 'subscriber_transaction.transaction_time', strtotime($this->to_date)])
                ->andWhere(['subscriber_transaction.status'=> SubscriberTransaction::STATUS_SUCCESS])
                ->andWhere(['subscriber_transaction.type'=>$this->type]);
            if($this->site_id){
                $query->andWhere(['subscriber_transaction.site_id'=>$this->site_id]);
            }
            if($this->service_id){
                $query->andWhere(['subscriber_transaction.service_id'=>$this->service_id]);
            }
        }
        if($this->city){
            $query->andWhere(['subscriber.city'=>$this->city]);
        }
        if($this->machine_name){
            $query->andWhere(['subscriber.machine_name'=>$this->machine_name]);
        }
        if($this->full_name){
            $query->andWhere(['subscriber.full_name'=>$this->full_name]);
        }
        if($this->username){
            $query->andWhere(['subscriber.username'=>$this->username]);
        }
        if($this->address){
            $query->andWhere(['subscriber.address'=>$this->address]);
        }
        if($this->msisdn){
            $query->andWhere(['subscriber.msisdn'=>$this->msisdn]);
        }
        if($this->email){
            $query->andWhere(['subscriber.email'=>$this->email]);
        }
        if($this->serial){
            $query->innerJoin('device','subscriber.machine_name=device.device_id')
            ->andWhere(['device.serial'=>$this->serial]);
        }
        if($this->device_type){
            $query->innerJoin('device','subscriber.machine_name=device.device_id')
            ->andWhere(['device.device_type'=>$this->device_type]);
        }
        return $dataProvider;
    }
    public function searchReportSubscriberExcel($params){
        $query = Subscriber::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        if($this->type == ReportSubscriberForm::TYPE_EXPIRE){
            $query->innerJoin('subscriber_service_asm','subscriber.id=subscriber_service_asm.subscriber_id')
                ->addSelect(['subscriber.*',
                    'subscriber_service_asm.service_id as service_id',
                    'subscriber_service_asm.expired_at as time'])
                ->andWhere(['>=', 'subscriber_service_asm.expired_at', strtotime($this->from_date)])
                ->andWhere(['<=', 'subscriber_service_asm.expired_at', strtotime($this->to_date)])
                ->andWhere(['subscriber_service_asm.status'=> SubscriberServiceAsm::STATUS_ACTIVE]);
            if($this->site_id){
                $query->andWhere(['subscriber_service_asm.site_id'=>$this->site_id]);
            }
            if($this->service_id){
                $query->andWhere(['subscriber_service_asm.service_id'=>$this->service_id]);
            }
        }else{
            $query->innerJoin('subscriber_transaction','subscriber.id=subscriber_transaction.subscriber_id')
                ->addSelect(['subscriber_transaction.subscriber_id as subscriber_id',
                    'subscriber.full_name',
                    'subscriber.msisdn',
                    'subscriber.email',
                    'subscriber.address',
                    'subscriber.city',
                    'subscriber.username',
                    'subscriber.machine_name',
                    'subscriber_transaction.service_id as service_id',
                    'subscriber_transaction.transaction_time as time'])
                ->andWhere(['>=', 'subscriber_transaction.transaction_time', strtotime($this->from_date)])
                ->andWhere(['<=', 'subscriber_transaction.transaction_time', strtotime($this->to_date)])
                ->andWhere(['subscriber_transaction.status'=> SubscriberTransaction::STATUS_SUCCESS])
                ->andWhere(['subscriber_transaction.type'=>$this->type]);
            if($this->site_id){
                $query->andWhere(['subscriber_transaction.site_id'=>$this->site_id]);
            }
            if($this->service_id){
                $query->andWhere(['subscriber_transaction.service_id'=>$this->service_id]);
            }
        }
        if($this->city){
            $query->andWhere(['subscriber.city'=>$this->city]);
        }
        if($this->machine_name){
            $query->andWhere(['subscriber.machine_name'=>$this->machine_name]);
        }
        if($this->full_name){
            $query->andWhere(['subscriber.full_name'=>$this->full_name]);
        }
        if($this->username){
            $query->andWhere(['subscriber.username'=>$this->username]);
        }
        if($this->address){
            $query->andWhere(['subscriber.address'=>$this->address]);
        }
        if($this->msisdn){
            $query->andWhere(['subscriber.msisdn'=>$this->msisdn]);
        }
        if($this->email){
            $query->andWhere(['subscriber.email'=>$this->email]);
        }
        if($this->serial){
            $query->innerJoin('device','subscriber.machine_name=device.device_id')
                ->andWhere(['device.serial'=>$this->serial]);
        }
        if($this->device_type){
            $query->innerJoin('device','subscriber.machine_name=device.device_id')
                ->andWhere(['device.device_type'=>$this->device_type]);
        }
        return $dataProvider;
    }
}
