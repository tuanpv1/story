<?php

namespace backend\controllers;

use app\models\ContentCategoryAsm;
use common\helpers\CVietnameseTools;
use common\models\Content;
use common\models\ContentSearch;
use Yii;
use yii\bootstrap\ActiveForm;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * ContentController implements the CRUD actions for Content model.
 */
class ContentController extends BaseBEController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ]);
    }

    /**
     * Lists all Content models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ContentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Content model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id, $active = 1)
    {
        $model = $this->findModel($id);
        $searchEpisodeModel = new ContentSearch();
        $episodeModel = new Content();
        $episodeModel->parent_id = $model->id;
        $episodeModel->type = $model->type;
        $params = Yii::$app->request->queryParams;
        $episodeProvider = $searchEpisodeModel->filterEpisode($params, $model->created_user_id, $id);

        return $this->render('view', [
            'model' => $model,
            'active' => $active,
            'searchEpisodeModel' => $searchEpisodeModel,
            'episodeModel' => $episodeModel,
            'episodeProvider' => $episodeProvider,
        ]);
    }

    /**
     * Creates a new Content model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($parent_id = null)
    {
        $model = new Content();
        $model->setScenario('createContent');
        if ($parent_id) {
            $model->parent_id = $parent_id;
        }

        $model->created_user_id = Yii::$app->user->id;
//        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
//            Yii::$app->response->format = Response::FORMAT_JSON;
//            return ActiveForm::validate($model);
//        }

        if ($model->load(Yii::$app->request->post())) {
            $image = UploadedFile::getInstance($model, 'image');
            if ($image) {
                $file_name = Yii::$app->user->id . '.' . uniqid() . time() . '.' . $image->extension;
                $tmp = Yii::getAlias('@backend') . '/web/' . Yii::getAlias('@content_images') . '/';
                if (!file_exists($tmp)) {
                    mkdir($tmp, 0777, true);
                }
                if ($image->saveAs($tmp . $file_name)) {
                    $model->image = $file_name;
                }
            }
            $model->ascii_name = CVietnameseTools::makeSearchableStr($model->display_name);
            $model->code = time() . rand(0, 999);
            if ($model->save()) {
                // lưu danh mục
                $modelCategory = new ContentCategoryAsm();
                $modelCategory->content_id = $model->id;
                $modelCategory->category_id = $model->list_cat_id;
                $modelCategory->save();
                // update lại số tập và thứ tự của tập
                if ($model->parent_id) {
                    $model->updateEpisodeOrder();
                }
                Yii::$app->session->setFlash('success', 'Tạo mới nội dung thành công');
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::error($model->getErrors());
                Yii::$app->session->setFlash('error', Yii::t('app', 'Lỗi! tạo nội dung không thành công'));
                return $this->render('create', ['model' => $model]);
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * Updates an existing Content model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $oldImg = $model->image;
        if(!$model->code){
            $model->code = time() . rand(0, 999);
        }
        if(!$model->created_user_id){
            $model->created_user_id = Yii::$app->user->id;
        }
        $model->getCat();
        if ($model->load(Yii::$app->request->post())) {
            $image = UploadedFile::getInstance($model, 'images');
            if ($image) {
                $file_name = Yii::$app->user->id . '.' . uniqid() . time() . '.' . $image->extension;
                $tmp = Yii::getAlias('@backend') . '/web/' . Yii::getAlias('@content_images') . '/';
                if (!file_exists($tmp)) {
                    mkdir($tmp, 0777, true);
                }

                if ($image->saveAs($tmp . $file_name)) {
                    $model->image = $file_name;
                }
            } else {
                $model->image = $oldImg;
            }
            $model->ascii_name = CVietnameseTools::makeSearchableStr($model->display_name);
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Cập nhật nội dung thành công');
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Lỗi! ' . $model->getErrors()));
                return $this->render('update', ['model' => $model]);
            }
        }

        return $this->render('update', ['model' => $model]);
    }

    /**
     * Deletes an existing Content model.
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
     * Finds the Content model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Content the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Content::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionUpdateOrder($id)
    {
        $model = $this->findModel($id);
        if (isset($_POST['hasEditable'])) {
            // read your posted model attributes
            $post = Yii::$app->request->post();
            if ($post['editableKey']) {
                // read or convert your posted information
                $content = Content::findOne($post['editableKey']);
                $index = $post['editableIndex'];
                if ($content || $model->id != $content->id) {
                    $content->load($post['Content'][$index], '');
                    if ($content->update()) {
                        echo \yii\helpers\Json::encode(['output' => '', 'message' => '']);
                    } else {
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
}
