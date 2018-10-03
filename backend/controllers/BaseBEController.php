<?php
namespace backend\controllers;

use common\auth\filters\Yii2Auth;
use common\models\LoginForm;
use common\models\LogLanguage;
use common\models\Multilanguage;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Site controller
 */
class BaseBEController extends Controller
{
 

    public $audit_id = null;

    public function behaviors()
    {
        return [
            'auth' => [
                'class' => Yii2Auth::className(),
                'autoAllow' => false,
                'authManager' => 'authManager',
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function init(){
    }
}
