<?php

namespace backend\controllers;

use common\models\AttributeSearch;
use common\models\Partner;
use common\models\PartnerAttributeAsm;
use common\models\PartnerAttributeAsmSearch;
use common\models\PartnerSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * PartnerController implements the CRUD actions for Partner model.
 */
class PartnerController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return parent::behaviors();
    }

    /**
     * Lists all Partner models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PartnerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Partner model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $searchModel = new PartnerAttributeAsmSearch();
        $params = Yii::$app->request->queryParams;
        $params['PartnerAttributeAsmSearch']['partner_id'] = $id;
        $dataProvider = $searchModel->search($params);
        return $this->render('view', [
            'model' => $this->findModel($id),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Partner model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Partner();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Partner model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Partner model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = Partner::findOne($id);
        if (!$model) {
            Yii::$app->session->set('warning', Yii::t('app', 'Không tồn lại Đối tác'));
            return $this->redirect(['index']);
        }
        if ($model->status == Partner::STATUS_DELETED) {
            Yii::$app->session->set('warning', Yii::t('app', 'Không tồn lại Đối tác'));
            return $this->redirect(['index']);
        }
        $model->status = Partner::STATUS_DELETED;
        if ($model->save()) {
            Yii::$app->session->set('success', Yii::t('app', 'Xóa đối tác thành công'));
        } else {
            Yii::$app->session->set('error', Yii::t('app', 'Xóa đối tác không thành công'));
            Yii::warning($model->getErrors());
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the Partner model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Partner the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Partner::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionUpdateStatus($id)
    {
        $model = PartnerAttributeAsm::findOne($id);
        if (isset($_POST['hasEditable'])) {
            $post = Yii::$app->request->post();
            if ($post['editableKey']) {
                $partnerAttributeAsm = PartnerAttributeAsm::findOne($post['editableKey']);
                $index = $post['editableIndex'];
                if ($partnerAttributeAsm || $model->id != $partnerAttributeAsm->id) {
                    $partnerAttributeAsm->load($post['PartnerAttributeAsm'][$index], '');
                    if ($partnerAttributeAsm->update()) {
                        echo \yii\helpers\Json::encode(['output' => '', 'message' => '']);
                    } else {
                        Yii::warning($partnerAttributeAsm->getErrors());
                        echo \yii\helpers\Json::encode(['output' => '', 'message' => \Yii::t('app', 'Dữ liệu không hợp lệ')]);
                    }
                } else {
                    echo \yii\helpers\Json::encode(['output' => '', 'message' => \Yii::t('app', 'Dữ liệu không tồn tại')]);
                }
            } else {
                echo \yii\helpers\Json::encode(['output' => '', 'message' => '']);
            }

            return;
        }
    }

    public function actionUpdateOrder($id)
    {
        $model = PartnerAttributeAsm::findOne($id);
        if (isset($_POST['hasEditable'])) {
            $post = Yii::$app->request->post();
            if ($post['editableKey']) {
                $partnerAttributeAsm = PartnerAttributeAsm::findOne($post['editableKey']);
                $index = $post['editableIndex'];
                if ($partnerAttributeAsm || $model->id != $partnerAttributeAsm->id) {
                    $partnerAttributeAsm->load($post['PartnerAttributeAsm'][$index], '');
                    if ($partnerAttributeAsm->update()) {
                        echo \yii\helpers\Json::encode(['output' => '', 'message' => '']);
                    } else {
                        Yii::warning($partnerAttributeAsm->getErrors());
                        $m = \Yii::t('app', 'Dữ liệu không hợp lệ ') . $partnerAttributeAsm->getFirstError('order');
                        echo \yii\helpers\Json::encode(['output' => '', 'message' => $m]);
                    }
                } else {
                    echo \yii\helpers\Json::encode(['output' => '', 'message' => \Yii::t('app', 'Dữ liệu không tồn tại')]);
                }
            } else {
                echo \yii\helpers\Json::encode(['output' => '', 'message' => '']);
            }

            return;
        }
    }

    public function actionAddAttribute($id_partner)
    {
        $searchModel = new AttributeSearch();
        $params = Yii::$app->request->queryParams;
        $array_attribute_ids = PartnerAttributeAsm::find()
            ->select('attribute_id')
            ->andWhere(['partner_id' => $id_partner])
            ->andWhere(['status' => PartnerAttributeAsm::STATUS_ACTIVE])
            ->all();
        $ids = [];
        if ($array_attribute_ids) {
            foreach ($array_attribute_ids as $array_attribute_id) {
                $ids[] = $array_attribute_id->attribute_id;
            }
        }
//        var_dump($ids);die();
        $dataProvider = $searchModel->searchPartner($params, $ids);

        return $this->render('_list_attribute', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'id_partner' => $id_partner
        ]);
    }

    public function actionAddAttributeToPartner()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $post = Yii::$app->request->post();
        if ($post['idPartner']) {
            $partner_id = $post['idPartner'];
            $ids = isset($post['ids']) ? $post['ids'] : [];
            // Find max order của đối tác
            $partner_last = PartnerAttributeAsm::find()
                ->andWhere(['partner_id' => $partner_id])
                ->orderBy(['order' => SORT_DESC])
                ->one();
            if ($partner_last) {
                $max = $partner_last->order;
            } else {
                $max = 0;
            }
            foreach ($ids as $id) {
                $max = $max + 1;
                $model = new PartnerAttributeAsm();
                $model->partner_id = $partner_id;
                $model->attribute_id = $id;
                $model->status = PartnerAttributeAsm::STATUS_ACTIVE;
                $model->order = $max;
                if (!$model->save()) {
                    return [
                        'success' => false,
                        'message' => Yii::t('app', 'Lỗi! Không thêm thành công!'),
                    ];
                }
            }
            return [
                'success' => true,
                'message' => Yii::t('app', 'Thêm thành công!')
            ];
        } else {
            return [
                'success' => false,
                'message' => Yii::t('app', 'Không tìm thấy Đối tác trên hệ thống'),
            ];
        }
    }

    public function actionDeleteAttribute($id)
    {
        $model = PartnerAttributeAsm::findOne($id);
        if (!$model) {
            Yii::$app->session->set('warning', Yii::t('app', 'Không tồn tại thuộc tính với đối tác'));
            return $this->redirect(['index']);
        }
        if ($model->delete()) {
            Yii::$app->session->set('success', Yii::t('app', 'Xóa thuộc tính thành công'));
        } else {
            Yii::$app->session->set('error', Yii::t('app', 'Xóa thuộc tính không thành công'));
            Yii::warning($model->getErrors());
        }
        return $this->redirect(['index']);
    }

}
