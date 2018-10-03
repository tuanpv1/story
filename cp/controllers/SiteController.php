<?php
namespace cp\controllers;

use backend\models\PasswordForm;
use backend\models\PasswordResetRequestForm;
use common\auth\filters\Yii2Auth;
use common\helpers\CheckLogin;
use common\models\User;
use kartik\widgets\ActiveForm;
use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use cp\models\LoginForm;
use yii\web\Response;

/**
 * Site controller
 */
class SiteController extends BaseCPController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
//            'auth' => [
//                'class' => Yii2Auth::className(),
//                'autoAllow' => false,
//                'authManager' => 'authManager',
//            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error','request-password-reset','change-password'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'errorHandler' => [
                'errorAction' => 'error/index',
            ],
        ];
    }

    public function actionIndex()
    {
        $model = User::findOne(['id' => Yii::$app->user->identity->getId()]);
        if ($model) {
            return $this->render('index',
                [
                    'model' => $model,
                ]);
        }else{
            return $this->redirect(['site/login']);
        }
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        $loginError = new CheckLogin();
        $count = $loginError->getCountError();
        if (!$loginError->showError($count)) {
            Yii::$app->getSession()->setFlash('error', \Yii::t('app', 'Bạn đã đăng nhập quá số lần quy định vui lòng đăng nhập lại sau ' . Yii::$app->params['timeOutLogin'] . ' phút'));
            return $this->render('login', [
                'model' => $model,
            ]);
        }
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $count++;
            $loginError->setCountError($count);
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionRequestPasswordReset()
    {
        $this->layout = 'main-login';
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Mật khẩu mới đã được gửi vào email của bạn!'));
                return $this->redirect(['site/login']);
            } else {
                Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Lỗi, Hiện chúng tôi không thể gửi email vui lòng liên hệ ban quản trị.'));
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
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
}
