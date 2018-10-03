<?php

namespace cp\controllers;

use common\models\Attribute;
use common\models\AttributeSearch;
use common\models\AttributeValue;
use common\models\CpOrderAsm;
use common\models\CpOrderAsmSearch;
use common\models\User;
use Exception;
use kartik\widgets\ActiveForm;
use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * CpOrderAsmController implements the CRUD actions for CpOrderAsm model.
 */
class CpOrderAsmController extends BaseCPController
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
        $dataProvider = $searchModel->searchOrderByCP($params,Yii::$app->user->identity->dealer_id);
        $model_attribute = Attribute::find()->all();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model_attribute'=>$model_attribute,
        ]);
    }
}
