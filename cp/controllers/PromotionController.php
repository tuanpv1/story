<?php

namespace cp\controllers;

use common\models\Attribute;
use common\models\AttributeValue;
use common\models\Promotion;
use common\models\PromotionSearch;
use Exception;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * PromotionController implements the CRUD actions for Promotion model.
 */
class PromotionController extends BaseCPController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return parent::behaviors();
    }

    /**
     * Lists all Promotion models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PromotionSearch();
        $params = Yii::$app->request->queryParams;
        $params['PromotionSearch']['dealer_id'] = Yii::$app->user->identity->dealer_id;
        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Promotion model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Promotion model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Promotion();
        $model_attribute = Attribute::findAll(['status' => Attribute::STATUS_ACTIVE]);
        $model->dealer_id = Yii::$app->user->identity->dealer_id;

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                // biến check
                $check = 0;
                // Lưu vào bảng promotion trước
                if (!$model->save()) {
                    Yii::$app->session->setFlash('error', Yii::t('app', 'Tạo chương trình ưu đãi không thành công!'));
                    Yii::info($model->getErrors());
                    return $this->render('create', [
                        'model' => $model,
                        'model_attribute' => $model_attribute
                    ]);
                }
                // Lưu vào bảng attribute_value và trừ tài khoản ví ảo của đại lý
                if ($model->attribute_value) {
                    foreach ($model->attribute_value as $key => $value) {

                        $av = new AttributeValue();
                        $av->value = $value;
                        $av->attribute_id = $key;
                        $av->promotion_id = $model->id;
                        $av->type = AttributeValue::TYPE_PROMOTION;
                        $av->status = AttributeValue::STATUS_ACTIVE;
                        if (!$av->save()) {
                            Yii::$app->session->setFlash('error', Yii::t('app', 'Tạo chương trình ưu đãi không thành công!'));
                            Yii::info($av->getErrors());
                            return $this->render('create', [
                                'model' => $model,
                                'model_attribute' => $model_attribute
                            ]);
                        }

                        // Lấy thông tin ví ảo
                        $model_balance_cp = AttributeValue::find()
                            ->andWhere(['dealer_id' => $model->dealer_id])
                            ->andWhere(['attribute_id' => $key])
                            ->andWhere(['type' => AttributeValue::TYPE_BALANCE])
                            ->andWhere(['status' => AttributeValue::STATUS_ACTIVE])
                            ->one();
                        // trừ tài khoản
                        if ($model_balance_cp) {
                            $old_balance = $model_balance_cp->value;
                            $model_balance_cp->value = $old_balance - ($value * $model->total_promotion_code);
                            if (!$model_balance_cp->save()) {
                                Yii::$app->session->setFlash('error', Yii::t('app', 'Tạo chương trình ưu đãi không thành công!'));
                                Yii::info($model_balance_cp->getErrors());
                                return $this->render('create', [
                                    'model' => $model,
                                    'model_attribute' => $model_attribute
                                ]);
                            }
                        }
                    }
                }
                $transaction->commit();
                Yii::$app->session->setFlash('success', Yii::t('app', 'Tạo chương trình ưu đãi thành công!'));
                return $this->redirect(['view', 'id' => $model->id]);

            } catch (Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', Yii::t('app', 'Tạo chương trình ưu đãi không thành công!'));
                return $this->render('create', [
                    'model' => $model,
                    'model_attribute' => $model_attribute
                ]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'model_attribute' => $model_attribute,
        ]);
    }

    /**
     * Updates an existing Promotion model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        $model->dealer_id = Yii::$app->user->identity->dealer_id;
        $old_total_promotion_code = $model->total_promotion_code;

        // Kiểm tra đã gen code chưa nếu rồi thì không cho sửa
        if ($model->gen_code == Promotion::STATUS_GEN_CODED) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Chương trình ưu đã đã được gen code, không được cập nhật!'));
            return $this->redirect(['view', 'id' => $id]);
        }

        $model_attribute = Attribute::findAll(['status' => Attribute::STATUS_ACTIVE]);

        if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                // biến check
                $check = 0;
                // Lưu vào bảng promotion trước
                if (!$model->save()) {
                    Yii::$app->session->setFlash('error', Yii::t('app', 'Cập nhật chương trình ưu đãi không thành công!'));
                    Yii::info($model->getErrors());
                    return $this->render('create', [
                        'model' => $model,
                        'model_attribute' => $model_attribute
                    ]);
                }
                // Lưu vào bảng attribute_value và trừ tài khoản ví ảo của đại lý
                if ($model->attribute_value) {
                    foreach ($model->attribute_value as $key => $value) {
                        // Tìm xem có record trước đó chưa
                        $av = AttributeValue::findOne([
                            'attribute_id' => $key,
                            'promotion_id' => $model->id,
                            'type' => AttributeValue::TYPE_PROMOTION,
                            'status' => AttributeValue::STATUS_ACTIVE,
                        ]);
                        $ol_value = 0;
                        if ($av) {
                            $ol_value = $av->value;
                            $av->value = $value;
                        } else {
                            // chưa có thì tạo mới
                            $av = new AttributeValue();
                            $av->value = $value;
                            $av->attribute_id = $key;
                            $av->promotion_id = $model->id;
                            $av->type = AttributeValue::TYPE_PROMOTION;
                            $av->status = AttributeValue::STATUS_ACTIVE;
                        }

                        if (!$av->save()) {
                            Yii::$app->session->setFlash('error', Yii::t('app', 'Cập nhật chương trình ưu đãi không thành công!'));
                            Yii::info($av->getErrors());
                            return $this->render('create', [
                                'model' => $model,
                                'model_attribute' => $model_attribute
                            ]);
                        }

                        // Lấy thông tin ví ảo
                        $model_balance_cp = AttributeValue::find()
                            ->andWhere(['dealer_id' => $model->dealer_id])
                            ->andWhere(['attribute_id' => $key])
                            ->andWhere(['type' => AttributeValue::TYPE_BALANCE])
                            ->andWhere(['status' => AttributeValue::STATUS_ACTIVE])
                            ->one();
                        // trừ tài khoản khi cập nhật sẽ khác
                        $old_balance = $model_balance_cp->value;
                        $roll_back = $ol_value * $old_total_promotion_code;
                        $model_balance_cp->value = $old_balance - ($value * $model->total_promotion_code) + $roll_back;
                        if (!$model_balance_cp->save()) {
                            Yii::$app->session->setFlash('error', Yii::t('app', 'Cập nhật chương trình ưu đãi không thành công!'));
                            Yii::info($av->getErrors());
                            return $this->render('create', [
                                'model' => $model,
                                'model_attribute' => $model_attribute
                            ]);
                        }
                    }
                }
                $transaction->commit();
                Yii::$app->session->setFlash('success', Yii::t('app', 'Cập nhật chương trình ưu đãi thành công!'));
                return $this->redirect(['view', 'id' => $model->id]);

            } catch (Exception $e) {
                $transaction->rollBack();
                Yii::trace($e->getMessage());
                Yii::$app->session->setFlash('error', Yii::t('app', 'Cập nhật chương trình ưu đãi không thành công!'));
                return $this->render('create', [
                    'model' => $model,
                    'model_attribute' => $model_attribute
                ]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'model_attribute' => $model_attribute,
        ]);
    }

    /**
     * Deletes an existing Promotion model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = Promotion::findOne($id);
        $title = $model->name;
        if ($model->status != Promotion::STATUS_INACTIVE || $model->gen_code != Promotion::STATUS_GEN_CODE) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Không được xóa chương trình ưu đãi đang ở trạng thái hoạt động hoặc đã được gen mã!'));
            return $this->redirect(['view', 'id' => $id]);
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // Cộng lại ví ảo
            $av = AttributeValue::findAll([
                'promotion_id' => $id,
                'type' => AttributeValue::TYPE_PROMOTION,
                'status' => AttributeValue::STATUS_ACTIVE,
            ]);
            Yii::info($av);
            if ($av) {
                /** @var AttributeValue $item */
                foreach ($av as $item) {
                    // Tìm attribute của CP
                    $avcp = AttributeValue::findOne([
                        'attribute_id' => $item->attribute_id,
                        'dealer_id' => $model->dealer_id,
                        'type' => AttributeValue::TYPE_BALANCE,
                        'status' => AttributeValue::STATUS_ACTIVE
                    ]);
                    if ($avcp) {
                        // Update lai vi
                        $avcp->value = ($avcp->value + ($item->value * $model->total_promotion_code));
                        if (!$avcp->update()) {
                            Yii::warning($avcp->getErrors());
                        }
                        // Xóa attribute value
                        if (!$item->delete()) {
                            Yii::warning($item->getErrors());
                        }
                    } else {
                        Yii::info('Khong tim thay vi CP');
                    }
                }
            }

            if (!$model->delete()) {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Xóa chương trình ưu đãi không thành công!'));
                return $this->redirect(['view', 'id' => $id]);
            }

            // Xóa trong bảng attribute_value
            $transaction->commit();
            Yii::$app->session->setFlash('success', Yii::t('app', 'Đã xóa chương trình ưu đãi ' . $title));
        } catch (Exception $e) {
            $transaction->rollBack();
            Yii::trace($e);
            Yii::$app->session->setFlash('error', Yii::t('app', 'Xóa chương trình ưu đãi không thành công!'));
            return $this->redirect(['view', 'id' => $id]);
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Promotion model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Promotion the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Promotion::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionGetView()
    {
        $param = Yii::$app->request->post();
        if ($param && !empty($param['Promotion']['name'])) {
            return $this->redirect(['view', 'id' => $param['Promotion']['name']]);
        }
    }
}
