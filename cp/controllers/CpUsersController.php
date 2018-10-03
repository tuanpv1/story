<?php

namespace cp\controllers;

use backend\models\ViewUserForm;
use backend\models\PasswordForm;
use common\helpers\GetRamdom;
use common\helpers\SendEmail;
use common\models\Attribute;
use common\models\AttributeValue;
use common\models\CpOrder;
use common\models\CpOrderAsm;
use common\models\CpOrderSearch;
use common\models\Dealer;
use common\models\DealerSearch;
use Exception;
use kartik\widgets\ActiveForm;
use Yii;
use common\models\User;
use common\models\UserSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * CpUsersController implements the CRUD actions for Dealer model.
 */
class CpUsersController extends BaseCPController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return parent::behaviors();
    }



    /**
     * Displays a single Dealer model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $searchModel = new DealerSearch();
        $params = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($params);
        $model_attribute = Attribute::find()->all();

        return $this->render('view', [
            'model' => $this->findModel($id),
            'dataProvider' => $dataProvider,
            'model_attribute' => $model_attribute,
        ]);
    }

    /**
     * Updates an existing Dealer model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->username = $model->getUserName($id);
        if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$model->save(false)) {
                    Yii::info(($model->getErrors()));
                }
                $listUser = User::findAll(['dealer_id'=>$id]);
                foreach($listUser as $item){
                    $item->email = $model->email;
                    $item->phone = $model->phone_number;
                    if(!$item->save()){
                        Yii::info(($item->getErrors()));
                    }
                }
                $transaction->commit();
                Yii::$app->session->setFlash('success', Yii::t('app', 'Cập nhật thông tin đại lý thành công!'));
                return $this->redirect(['view', 'id' => $model->id]);
            } catch (Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', Yii::t('app', 'Cập nhật thông tin đại lý không thành công!'));
                return $this->redirect(['index']);
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }


    /**
     * Finds the Dealer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Dealer::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionChangePassword()
    {
        $model = new PasswordForm();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {
            $user = User::findOne(Yii::$app->user->id);
            $user->setPassword($model->password_new);
            if ($user->save()) {
                Yii::$app->user->logout();
                Yii::$app->session->setFlash('success', Yii::t('app', 'Mật khẩu mới đã được thiết lập. Vui lòng đăng nhập lại'));
                return $this->redirect(['site/login']);
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Tạo tài khoản không thành công! Vui lòng thử lại.'));
                Yii::info($user->getErrors());
            }
        }
        return $this->render('change-pass', [
            'model' => $model,
        ]);
    }

}
