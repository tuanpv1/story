<?php
namespace cp\controllers;

use common\auth\filters\Yii2Auth;
use common\models\LoginForm;
use common\models\LogLanguage;
use common\models\Multilanguage;
use Yii;
use yii\web\Controller;

/**
 * Site controller
 */
class BaseCPController extends Controller
{
 

    public $audit_id = null;

    public function behaviors()
    {
        return [
//            'auth' => [
//                'class' => Yii2Auth::className(),
//                'autoAllow' => false,
//                'authManager' => 'authManager',
//            ],
        ];
    }

    public function init(){
    }
}
