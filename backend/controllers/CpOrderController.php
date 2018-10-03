<?php

namespace backend\controllers;

use common\models\Attribute;
use common\models\AttributeValue;
use common\models\CpOrder;
use common\models\CpOrderAsm;
use common\models\CpOrderSearch;
use Exception;
use kartik\widgets\ActiveForm;
use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * CpOrderController implements the CRUD actions for Attribute model.
 */
class CpOrderController extends BaseBEController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return parent::behaviors();
    }

    /**
     * Lists all CpOrder models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CpOrderSearch();
        $model_attribute = Attribute::find()->all();
        $params = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model_attribute'=>$model_attribute,
        ]);
    }

    /**
     * Displays a single CpOrder model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model_attribute = Attribute::find()->all();
        return $this->render('view', [
            'model' => $this->findModel($id),
            'model_attribute' => $model_attribute,
        ]);
    }


    /**
     * Creates a new CpOrder model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CpOrder();
        $model_attribute = Attribute::findAll(['status' => Attribute::STATUS_ACTIVE]);
        if(!$model_attribute){
            Yii::$app->session->setFlash('error', Yii::t('app', 'Vui lòng tạo thuộc tính khuyễn mãi trước'));
            return $this->redirect(['attribute/create']);
        }
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$model->save()) {
                    Yii::$app->session->setFlash('error', Yii::t('app', 'Tạo template đơn hàng không thành công! Vui lòng thử lại.'));
                    return $this->render('create', [
                        'model' => $model,
                        'model_attribute' => $model_attribute,
                    ]);
                }
                /** Gán giá trị cho các thuộc tính khuyến mại*/
                foreach ($model->attribute_value as $key => $value) {
                    $model_attribute_value = new AttributeValue();
                    $model_attribute_value->attribute_id = $key;
                    $model_attribute_value->value = $value;
                    $model_attribute_value->cp_order_id = $model->id;
                    $model_attribute_value->status = AttributeValue::STATUS_ACTIVE;
                    $model_attribute_value->type = AttributeValue::TYPE_CP_ORDER;
                    if(!$model_attribute_value->save()){
                        Yii::info($model_attribute_value->getErrors());
                        $transaction->rollBack();
                        Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Tạo template đơn hàng không thành công! Vui lòng thử lại.'));
                        return $this->render('create', [
                            'model' => $model,
                            'model_attribute' => $model_attribute,
                        ]);
                    }
                }
                $transaction->commit();
                Yii::$app->session->setFlash('success', Yii::t('app', 'Tạo template đơn hàng thành công!'));
                return $this->redirect(['view', 'id' => $model->id]);

            }catch (Exception $e) {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error',$e->getMessage());
                return $this->render('create', [
                    'model' => $model,
                    'model_attribute' => $model_attribute,
                ]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
                'model_attribute' => $model_attribute,
            ]);
        }
    }

    /**
     * Updates an existing CpOrder model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $value = CpOrderAsm::findAll(['cp_order_id' => $id]);

        if (count($value) > 0) {
            \Yii::$app->getSession()->setFlash('error', \Yii::t('app', 'Đơn hàng đang được sử dụng. Không thể sửa'));
            return $this->redirect(['index', 'id' => $model->id]);

        } else {
            /** lấy giá trị các thuộc tính khuyến mại lên form */
            $model_attribute = Attribute::find()->all();
            $attribute_value=[];
            if ($model_attribute) {
                /** @var \common\models\Attribute $item */
                foreach ($model_attribute as $item) {
                    $attribute_value[$item->id] = CpOrder::getAttributeValue($item->id,$id);
                }
            }
            $model->attribute_value = $attribute_value;
            if ($model->load(Yii::$app->request->post())) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if (!$model->save()) {
                        Yii::$app->session->setFlash('error', Yii::t('app', 'Cập nhật template đơn hàng không thành công! Vui lòng thử lại.'));
                        return $this->render('update', [
                            'model' => $model,
                            'model_attribute' => $model_attribute,
                        ]);
                    }
                    foreach ($model->attribute_value as $key => $value) {
                        $model_attribute_value = AttributeValue::findOne(['attribute_id'=>$key,'cp_order_id'=>$model->id,'type'=>AttributeValue::TYPE_CP_ORDER,'status'=>AttributeValue::STATUS_ACTIVE]);
                        /** nếu thuộc tính khuyến mại của đơn hàng đã có giá trị thì update */
                        if($model_attribute_value){
                            $model_attribute_value->value = $value;
                            if(!$model_attribute_value->save()){
                                Yii::info($model_attribute_value->getErrors());
                                $transaction->rollBack();
                                Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Cập nhật template đơn hàng không thành công! Vui lòng thử lại.'));
                                return $this->render('update', [
                                    'model' => $model,
                                    'model_attribute' => $model_attribute,
                                ]);
                            }
                            /** nếu thuộc tính khuyến mại của đơn hàng chưa có giá trị thì tạo mới */
                        }else{
                            $model_attribute_value_new =$model_attribute_value = new AttributeValue();
                            $model_attribute_value_new->attribute_id = $key;
                            $model_attribute_value_new->value = $value;
                            $model_attribute_value_new->cp_order_id = $model->id;
                            $model_attribute_value_new->status = AttributeValue::STATUS_ACTIVE;
                            $model_attribute_value_new->type = AttributeValue::TYPE_CP_ORDER;
                            if(!$model_attribute_value_new->save()){
                                Yii::info($model_attribute_value_new->getErrors());
                                $transaction->rollBack();
                                Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Tạo template đơn hàng không thành công! Vui lòng thử lại.'));
                                return $this->render('create', [
                                    'model' => $model,
                                    'model_attribute' => $model_attribute,
                                ]);
                            }
                        }
                    }
                    $transaction->commit();
                    Yii::$app->session->setFlash('success', Yii::t('app', 'Cập nhật template đơn hàng thành công.'));
                    return $this->redirect(['view', 'id' => $model->id]);

                }catch (Exception $e) {
                    $transaction->rollBack();
                    Yii::$app->getSession()->setFlash('error',$e->getMessage());
                    return $this->render('create', [
                        'model' => $model,
                        'model_attribute' => $model_attribute,
                    ]);
                }
            } else {
                return $this->render('update', [
                    'model' => $model,
                    'model_attribute' => $model_attribute,
                ]);
            }
        }
    }

    /**
     * Deletes an existing CpOrder model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $value = CpOrderAsm::findAll(['cp_order_id' => $id]);

        if (count($value) > 0) {
            \Yii::$app->getSession()->setFlash('error', \Yii::t('app', 'Đơn hàng đã được sử dụng. Không thể xóa'));
        } else {
            $transaction = Yii::$app->db->beginTransaction();
            try{
                $model_attribute_value = AttributeValue::findAll(['cp_order_id'=>$id]);
                $sumValue = count($model_attribute_value);
                $count=0;
                /** @var \common\models\AttributeValue $item */
                foreach ($model_attribute_value as $item) {
                    /** Xóa các thuộc tính khuyến mại của đơn hàng*/
                    if (!$item->findOne($item->id)->delete()) {
                        Yii::info($item->getErrors());
                        $transaction->rollBack();
                        Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Xóa template đơn hàng không thành công! Vui lòng thử lại.'));
                    }
                    $count++;
                }
                if($count==$sumValue){
                    /** Nếu xóa hết được thuộc tính của đơn hàng mới xóa đơn hàng*/
                    if(!$this->findModel($id)->delete()){
                        $transaction->rollBack();
                        Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Xóa template đơn hàng không thành công! Vui lòng thử lại.'));
                    }
                    $transaction->commit();
                    Yii::$app->session->setFlash('success', Yii::t('app', 'Xóa template đơn hàng thành công.'));
                }else{
                    $transaction->rollBack();
                    Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Xóa template đơn hàng không thành công! Vui lòng thử lại.'));
                }
            }catch (Exception $e) {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error',$e->getMessage());
            }
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the CpOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Attribute the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CpOrder::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
