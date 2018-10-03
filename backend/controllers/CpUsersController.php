<?php

namespace backend\controllers;

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
use common\models\Promotion;
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
class CpUsersController extends BaseBEController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return parent::behaviors();

    }

    /**
     * Lists all Dealer models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DealerSearch();
        $params = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
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
     * Creates a new Dealer model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Dealer();
        $model->setScenario('create-dealer');
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        $model_attribute = Attribute::findAll(['status' => Attribute::STATUS_ACTIVE]);
//        echo"<pre>"; print_r($model_attribute);die();
        if ($model->load(Yii::$app->request->post())) {
            $check = User::findOne(['username'=>$model->username]);
            if($check){
                Yii::$app->session->setFlash('error', Yii::t('app', 'Tên truy cập đã tồn tại! Vui lòng nhập lại.'));
                return $this->render('create', [
                    'model' => $model,
                    'model_attribute' => $model_attribute
                ]);
            }
            $transaction = Yii::$app->db->beginTransaction();
            try {
                //Tạo đại lý
                if (!$model->save()) {
                    Yii::info(($model->getErrors()));
                }
                //Tạo tài khoản
                $user = new User();
                $user->username = $model->username;
                $user->full_name = $model->full_name;
                $user->phone = $model->phone_number;
                $user->type = User::TYPE_CP;
                $user->status = User::STATUS_ACTIVE;
                $user->email = $model->email;
                $ramdom = new GetRamdom();
                $new_pass = $ramdom->get_rand_alphanumeric(8);
                $user->setPassword($new_pass);
                $user->generateAuthKey();
                $user->dealer_id = $model->id;
                if (!$user->save()) {
                    Yii::info(($user->getErrors()));
                }

                // Thêm vào bảng động tạo giá trị ví ảo đầu tiên
                if($model_attribute){
                    foreach ($model_attribute as $item) {
                        $model_attribute_value = new AttributeValue();
                        $model_attribute_value->attribute_id = $item->id;
                        $model_attribute_value->value = 0;
                        $model_attribute_value->dealer_id = $model->id;
                        $model_attribute_value->status = AttributeValue::STATUS_ACTIVE;
                        $model_attribute_value->type = AttributeValue::TYPE_BALANCE;
                        if (!$model_attribute_value->save(false)) {
                            Yii::info(($model_attribute_value->getErrors()));
                        }
                    }
                }

//                // gửi mail thông tin tài khoản
                $sendEmail = new SendEmail();
                $sendEmail->sendAccountCp($user->id, $new_pass);

                $transaction->commit();
                Yii::$app->session->setFlash('success', Yii::t('app', 'Tạo đại lý thành công!'));
                return $this->redirect(['index']);
            } catch (Exception $e) {
                $transaction->rollBack();
                Yii::info($e->getMessage());
                Yii::$app->session->setFlash('error', Yii::t('app', 'Tạo đại lý không thành công!'));
                return $this->render('create', [
                    'model' => $model,
                    'model_attribute' => $model_attribute
                ]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
                'model_attribute' => $model_attribute
            ]);
        }
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
        /** @var Dealer $model */
        $model = $this->findModel($id);
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        $model->username = $model->getUserName($id);
        /** @var User $model_user */
        $model_user = User::findOne(['dealer_id' => $id]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if($model_user){
                $model_user->status = $model->status;
                $model_user->email = $model->email;
                $model_user->phone = $model->phone_number;
                if($model_user->save()){
                    Yii::$app->session->setFlash('success','Cập nhật thông tin đại lý thành công');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Dealer model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model_order = CpOrderAsm::findOne(['dealer_id' => $id]);
        $model_promotion = Promotion::findOne(['dealer_id'=>$id]);
        if($model_promotion || $model_order){
            Yii::$app->session->setFlash('error', Yii::t('app', 'Không được xóa đại lý đã có chương trình khuyễn mãi!'));
            return $this->redirect(['view','id' => $id]);
        }
        $model->status = Dealer::STATUS_DELETED;
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$model->save(false)) {
                Yii::info(($model->getErrors()));
            }
            $listUser = User::findAll(['dealer_id'=>$id]);
            foreach($listUser as $item){
                $item->status = User::STATUS_DELETED;
                if(!$item->save()){
                    Yii::info(($item->getErrors()));
                }
            }
            $transaction->commit();
            Yii::$app->session->setFlash('success', Yii::t('app', 'Xóa đại lý thành công!'));
            return $this->redirect(['index']);
        } catch (Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', Yii::t('app', 'Xóa đại lý không thành công!'));
            return $this->redirect(['index']);
        }
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

    public function actionResetPassword($id)
    {
        $sendEmail = new SendEmail();
        /**  @var $model \common\models\User */
        $model = User::findOne(['dealer_id'=>$id]);
        if($model->status != User::STATUS_ACTIVE){
            Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Không được cấp lại mật khẩu cho tài khoản đang ở trạng thái không kích hoạt.'));
            return $this->redirect(Yii::$app->request->referrer);
        }
        if ($sendEmail->sendPass($model->id)) {
            Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Mật khẩu mới đã được gửi vào email của đại lý'));
            return $this->redirect(Yii::$app->request->referrer);
        } else {
            Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Lỗi, Hiện chúng tôi không thể gửi email vui lòng liên hệ ban quản trị.'));
            return $this->redirect(Yii::$app->request->referrer);
        }
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
