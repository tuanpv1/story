<?php

namespace backend\controllers;

use backend\models\PasswordResetRequestForm;
use backend\models\ResetPasswordForm;
use common\auth\filters\Yii2Auth;
use common\helpers\CheckLogin;
use dektrium\user\models\User;
use Yii;
use yii\base\InvalidParamException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\models\LoginForm;
use yii\web\BadRequestHttpException;

/**
 * Site controller
 */
class SiteController extends BaseBEController
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
                        'actions' => [
                            'login',
                            'request-password-reset',
                            'reset-password',
                            'error'
                        ],
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

    public
    function actionRequestPasswordReset()
    {
        $this->layout = 'main-login';
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Mật khẩu mới đã được gửi vào email của bạn! Vui lòng check mail'));
                return $this->redirect(['site/login']);
            } else {
                Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Lỗi, Hiện chúng tôi không thể gửi email vui lòng liên hệ ban quản trị.'));
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    public
    function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->getSession()->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
}
