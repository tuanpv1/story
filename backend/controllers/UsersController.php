<?php

namespace backend\controllers;

use backend\models\ViewUserForm;
use backend\models\PasswordForm;
use common\helpers\GetRamdom;
use common\helpers\SendEmail;
use common\models\AuthAssignment;
use common\models\AuthItem;
use Exception;
use kartik\widgets\ActiveForm;
use Yii;
use common\models\User;
use common\models\UserSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * UserController implements the CRUD actions for User model.
 */
class UsersController extends BaseBEController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return parent::behaviors();
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $params = Yii::$app->request->queryParams;
        $params['UserSearch']['type'] = User::TYPE_ADMIN;
        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $active = 1)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
            'active' => $active
        ]);
    }

    public function actionInfo()
    {
        $param = Yii::$app->request->queryParams;
        $id = isset($param['ViewUserForm']['username']) ? $param['ViewUserForm']['username'] : Yii::$app->user->identity->getId();
        $user = new ViewUserForm();
        $user->username = $id;
        return $this->render('info', [
            'user' => $user,
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();
        $model->type = User::TYPE_ADMIN;
        $model->setScenario('action-with-user');
        $sendEmail = new SendEmail();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
            $ramdom = new GetRamdom();
            $new_pass = $ramdom->get_rand_alphanumeric(8);
            $model->setPassword($new_pass);
            $model->generateAuthKey();
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$model->save()) {
                    Yii::info($model->getErrors());
                    Yii::$app->session->setFlash('error', Yii::t('app', 'Tạo tài khoản không thành công! Vui lòng thử lại.'));
                    return $this->render('create', [
                        'model' => $model,
                    ]);
                }
                $content= $model->getMessageUser($model->username,$new_pass);
                if ($sendEmail->sendEmail($model->email,$content,true)) {
                    $transaction->commit();
                    Yii::$app->session->setFlash('success', Yii::t('app', 'Tạo tài khoản thành công!'));
                    return $this->redirect(['index']);
                } else {
                    $transaction->rollBack();
                    Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Tạo tài khoản không thành công! Vui lòng thử lại.'));
                    return $this->render('create', [
                        'model' => $model,
                    ]);
                }
            } catch (Exception $e) {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error',$e->getMessage());
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->username =='admin'&& Yii::$app->user->identity->username != 'admin'){
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $model->setScenario('action-with-user');

        if ($model->load(Yii::$app->request->post())) {
            if($model->username == 'admin' && $model->status != User::STATUS_ACTIVE){
                Yii::$app->session->setFlash('error', Yii::t('app', 'Không được dừng hoạt động của Supper Admin!'));
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
            if($model->save()){
                Yii::$app->session->setFlash('success', Yii::t('app', 'Cập nhật tài khoản thành công!'));
                return $this->redirect(['view', 'id' => $model->id]);
            }else{
                Yii::$app->session->setFlash('error', Yii::t('app', 'Cập nhật tài khoản không thành công!'));
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne(['id'=>$id,'type'=>User::TYPE_ADMIN])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionResetPassword($id)
    {
        $sendEmail = new SendEmail();
        if ($sendEmail->sendPass($id)) {
            Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Mật khẩu mới đã được gửi vào email của bạn!'));
            return $this->redirect(Yii::$app->request->referrer);
        } else {
            Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Lỗi, Hiện chúng tôi không thể gửi email vui lòng liên hệ ban quản trị.'));
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    public function actionChangePassword(){
        $model = new PasswordForm();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {
            $user = User::findOne(Yii::$app->user->id);
            $user->setPassword($model->password_new);
            if($user->save()){
                Yii::$app->user->logout();
                Yii::$app->session->setFlash('success', Yii::t('app','Mật khẩu mới đã được thiết lập. Vui lòng đăng nhập lại'));
                return $this->redirect(['site/login']);
            }else{
                Yii::$app->session->setFlash('error', Yii::t('app','Tạo tài khoản không thành công! Vui lòng thử lại.'));
                Yii::info($user->getErrors());
            }
        }
        return $this->render('change-pass', [
            'model' => $model,
        ]);
    }

    public function actionAddAuthItem($id)
    {
        /* @var $model User */
        $model = User::findOne(['id' => $id]);

        Yii::$app->response->format = Response::FORMAT_JSON;

        $success = false;
        $message = Yii::t('app','User/nhóm quyền không tồn tại');

        if ($model) {
            $post = Yii::$app->request->post();

            if (isset($post['addItems'])) {
                $items = $post['addItems'];

                $count = 0;

                foreach ($items as $item) {
                    $role = AuthItem::findOne(['name' => $item]);
                    $mapping = new AuthAssignment();
                    $mapping->item_name = $item;
                    $mapping->user_id = $id;
                    if ($mapping->save()) {
                        $count ++;
                    }else{
                        Yii::info($mapping->getErrors());
                    }
                }


                if ($count >0) {
                    $success = true;
                    $message = Yii::t('app','Đã thêm').$count.Yii::t('app','nhóm quyền cho người dùng ').$model->username;

                }
            }
        }

        return [
            'success' => $success,
            'message' => $message
        ];
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function actionRevokeAuthItem()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $post = Yii::$app->request->post();

        $success = false;
        $message = Yii::t('app','Tham số không đúng');

        if (isset($post['user']) && isset($post['item'])) {
            $user = $post['user'];
            $item = $post['item'];

            $mapping = AuthAssignment::find()->andWhere(['user_id' => $user, 'item_name' => $item])->one();
            if ($mapping) {
                if ($mapping->delete()) {
                    $success = true;
                    $message = Yii::t('app','Đã xóa quyền ').$item.Yii::t('app','khỏi user ').$user.'!';
                } else {
                    $message = Yii::t('app','Lỗi hệ thống, vui lòng thử lại sau');
                }
            } else {
                $message = Yii::t('app','Quyền').$item.Yii::t('app','chưa được cấp cho user').$user.'!';
            }

        }

        return [
            'success' => $success,
            'message' => $message
        ];
    }
}
