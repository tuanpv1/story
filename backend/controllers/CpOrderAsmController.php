<?php

namespace backend\controllers;

use common\models\Attribute;
use common\models\AttributeSearch;
use common\models\AttributeValue;
use common\models\CpOrderAsm;
use common\models\CpOrderAsmSearch;
use Exception;
use kartik\widgets\ActiveForm;
use Yii;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * CpOrderAsmController implements the CRUD actions for Attribute model.
 */
class CpOrderAsmController extends BaseBEController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return parent::behaviors();
    }

    /**
     * Lists all CpOrderAsm models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CpOrderAsmSearch();
        $params = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($params);
        $model_attribute = Attribute::find()->all();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model_attribute'=>$model_attribute,
        ]);
    }


    /**
     * Displays a single CpOrderAsm model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model_attribute = Attribute::find()->all();
        return $this->render('view', [
            'model' => $this->findModel($id),
            'model_attribute'=>$model_attribute,
        ]);
    }


    /**
     * Creates a new CpOrderAsm model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate(){
        $model = new CpOrderAsm();
        $model_attribute = Attribute::find()->all();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
            $model->transaction_time=time();
            $transaction = Yii::$app->db->beginTransaction();
            try{
                if (!$model->save()) {
                    Yii::$app->session->setFlash('error', Yii::t('app', 'Tạo đơn hàng cho đại lý không thành công! Vui lòng thử lại.'));
                    return $this->render('create', [
                        'model' => $model,
                        'model_attribute'=>$model_attribute,
                    ]);
                }
                /** Nếu trạng thái là hoạt động thì mới cộng các giá trị vào ví*/
                if($model->status==CpOrderAsm::STATUS_ACTIVE){
                    $attribute_value = AttributeValue::find()
                        ->where(['cp_order_id'=>$model->cp_order_id])
                        ->andWhere(['type'=>AttributeValue::TYPE_CP_ORDER])
                        ->andWhere(['status'=>AttributeValue::STATUS_ACTIVE])
                        ->all();
//                    echo "<pre>";print_r($attribute_value);die();
                    /** @var \common\models\AttributeValue $item */
                    foreach ($attribute_value as $item) {
                        /** @var \common\models\AttributeValue $attribute_cp */
                        $attribute_cp = AttributeValue::findOne(['attribute_id'=>$item->attribute_id,'dealer_id'=>$model->dealer_id,'type'=>AttributeValue::TYPE_BALANCE,'status'=>AttributeValue::STATUS_ACTIVE]);
                        /** Nếu ví đã có thuộc tính khuyến mại thì cộng thêm giá trị của đơn hàng vào ví*/
                        if($attribute_cp ){
                            $attribute_cp->value = $attribute_cp->value + $item->value;
                            if(!$attribute_cp->save()){
                                Yii::info($attribute_cp->getErrors());
                                $transaction->rollBack();
                                Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Tạo đơn hàng cho đại lý không thành công! Vui lòng thử lại.'));
                                return $this->render('create', [
                                    'model' => $model,
                                    'model_attribute'=>$model_attribute,
                                ]);
                            }
                        }else{
                            /** Nếu ví chưa có thuộc tính khuyến mại thì tạo thêm vào ví*/
                            $model_attribute_value = new AttributeValue();
                            $model_attribute_value->attribute_id = $item->attribute_id;
                            $model_attribute_value->value = $item->value;
                            $model_attribute_value->dealer_id = $model->dealer_id;
                            $model_attribute_value->status = AttributeValue::STATUS_ACTIVE;
                            $model_attribute_value->type = AttributeValue::TYPE_BALANCE;
                            if(!$model_attribute_value->save()){
                                Yii::info($model_attribute_value->getErrors());
                                $transaction->rollBack();
                                Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Tạo đơn hàng cho đại lý không thành công! Vui lòng thử lại.'));
                                return $this->render('create', [
                                    'model' => $model,
                                    'model_attribute'=>$model_attribute,
                                ]);
                            }
                        }
                    }
                }
                $transaction->commit();
                Yii::$app->session->setFlash('success', Yii::t('app', 'Gán template đơn hàng thành công!'));
                return $this->redirect(['index']);
            }catch (Exception $e) {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error',$e->getMessage());
                return $this->render('create', [
                    'model' => $model,
                    'model_attribute'=>$model_attribute,
                ]);
            }
        }else {
            return $this->render('create', [
                'model' => $model,
                'model_attribute'=>$model_attribute,
            ]);
        }
    }

    /**
     * Updates an existing CpOrderAsm model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $status = $model->status;
        $model_attribute = Attribute::find()->all();
        $list_value=[];
        $attribute_value = AttributeValue::find()
            ->where(['cp_order_id'=>$model->cp_order_id])
            ->andWhere(['type'=>AttributeValue::TYPE_CP_ORDER])
            ->andWhere(['status'=>AttributeValue::STATUS_ACTIVE])
            ->all();
        /** @var \common\models\AttributeValue $item */
        foreach ($attribute_value as $item) {
            $list_value[$item->attribute_id]=$item->value;
        }
        $model->attribute_value = $list_value;
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) ) {
            $transaction = Yii::$app->db->beginTransaction();
            try{
                if (!$model->save()) {
                    Yii::$app->session->setFlash('error', Yii::t('app', 'Cập nhật đơn hàng cho đại lý không thành công! Vui lòng thử lại.'));
                    return $this->render('update', [
                        'model' => $model,
                        'model_attribute'=>$model_attribute,
                    ]);
                }
                if($model->status!=$status){

                    /** Nếu trạng thái là hoạt động thì mới cộng các giá trị vào ví*/
                    if($model->status==CpOrderAsm::STATUS_ACTIVE){
                        /** @var \common\models\AttributeValue $item */
                        foreach ($attribute_value as $item) {
                            /** @var \common\models\AttributeValue $attribute_cp */
                            $attribute_cp = AttributeValue::findOne(['attribute_id'=>$item->attribute_id,'dealer_id'=>$model->dealer_id,'type'=>AttributeValue::TYPE_BALANCE,'status'=>AttributeValue::STATUS_ACTIVE]);
                            /** Nếu ví đã có thuộc tính khuyến mại thì cộng thêm giá trị của đơn hàng vào ví*/
                            if($attribute_cp ){
                                $attribute_cp->value = $attribute_cp->value + $item->value;
                                if(!$attribute_cp->save()){
                                    Yii::info($attribute_cp->getErrors());
                                    $transaction->rollBack();
                                    Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Tạo đơn hàng cho đại lý không thành công! Vui lòng thử lại.'));
                                    return $this->render('create', [
                                        'model' => $model,
                                        'model_attribute'=>$model_attribute,
                                    ]);
                                }
                            }else{
                                /** Nếu ví chưa có thuộc tính khuyến mại thì tạo thêm vào ví*/
                                $model_attribute_value = new AttributeValue();
                                $model_attribute_value->attribute_id = $item->attribute_id;
                                $model_attribute_value->value = $item->value;
                                $model_attribute_value->dealer_id = $model->dealer_id;
                                $model_attribute_value->status = AttributeValue::STATUS_ACTIVE;
                                $model_attribute_value->type = AttributeValue::TYPE_BALANCE;
                                if(!$model_attribute_value->save()){
                                    Yii::info($model_attribute_value->getErrors());
                                    $transaction->rollBack();
                                    Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Tạo đơn hàng cho đại lý không thành công! Vui lòng thử lại.'));
                                    return $this->render('create', [
                                        'model' => $model,
                                        'model_attribute'=>$model_attribute,
                                    ]);
                                }
                            }
                        }
                    }else{
                        /** @var \common\models\AttributeValue $item */
                        foreach ($attribute_value as $item) {
                            /** @var \common\models\AttributeValue $attribute_cp */
                            $attribute_cp = AttributeValue::findOne(['attribute_id'=>$item->attribute_id,'dealer_id'=>$model->dealer_id,'type'=>AttributeValue::TYPE_BALANCE,'status'=>AttributeValue::STATUS_ACTIVE]);
                            /** Nếu ví có thuộc tính khuyến mại thì mới trừ đi giá trị của đơn hàng */
                            if($attribute_cp ){
                                $attribute_cp->value = $attribute_cp->value - $item->value;
                                if(!$attribute_cp->save()){
                                    Yii::info($attribute_cp->getErrors());
                                    $transaction->rollBack();
                                    Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Cập nhật đơn hàng cho đại lý không thành công! Vui lòng thử lại.'));
                                    return $this->render('update', [
                                        'model' => $model,
                                        'model_attribute'=>$model_attribute,
                                    ]);
                                }
                            }
                        }
                    }
                }
                $transaction->commit();
                Yii::$app->session->setFlash('success', Yii::t('app', 'Cập nhật đơn hàng cho đại lý thành công.'));
                return $this->redirect(['index']);
            }catch (Exception $e) {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error',$e->getMessage());
                return $this->render('update', [
                    'model' => $model,
                    'model_attribute'=>$model_attribute,
                ]);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
                'model_attribute'=>$model_attribute,
            ]);
        }
    }


    /**
     * Finds the CpOrderAsm model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Attribute the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CpOrderAsm::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    public function actionFindCpOrder() {
        $out = [];
        if (isset($_POST['selectOrder'])) {
            $order_id = $_POST['selectOrder'];
            if ($order_id != null) {
                $listValue  = AttributeValue::find()->where(['cp_order_id'=>$order_id])->all();
                if(count($listValue)<=0){
                    echo Json::encode(['output'=>'']);
                }
                foreach($listValue as  $item){
                    /** @var \common\models\AttributeValue $item */
                    $dataList['id'] = $item->attribute_id;
                    $dataList['value'] = $item->value;
                    $out[] = $dataList;
                }
                Yii::info($out);
                echo Json::encode(['output'=>$out]);
                return;
            }
        }
        echo Json::encode(['output'=>'']);
    }

}
