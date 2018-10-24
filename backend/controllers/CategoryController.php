<?php

namespace backend\controllers;

use common\components\ActionLogTracking;
use common\helpers\CVietnameseTools;
use common\models\Category;
use common\models\CategorySiteAsm;
use common\models\UserActivity;
use kartik\form\ActiveForm;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class CategoryController extends BaseBEController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post','get'],
                ],
            ],
            [
                'class'              => ActionLogTracking::className(),
                'user'               => Yii::$app->user,
                'model_type_default' => UserActivity::ACTION_TARGET_TYPE_CAT,
                'post_action'        => [
                    ['action' => 'create', 'accept_ajax' => false],
                    ['action' => 'update', 'accept_ajax' => false],
                    ['action' => 'delete', 'accept_ajax' => true],
                    ['action' => 'move-down', 'accept_ajax' => true],
                    ['action' => 'move-up', 'accept_ajax' => true],
                ],
                // 'only' => ['create', 'update', 'delete', 'move-down', 'move-up'],
            ],
        ]);
    }

    /**
     * Lists all Category models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        if (isset($_POST['hasEditable'])) {
            // read your posted model attributes
            $post = Yii::$app->request->post();
            if ($post['editableKey']) {
                // read or convert your posted information
                $cat   = Category::findOne($post['editableKey']);
                $index = $post['editableIndex'];
                if ($cat) {
                    $cat->load($post['Category'][$index], '');
                    if ($cat->update()) {
                        echo \yii\helpers\Json::encode(['output' => '', 'message' => '']);
                    } else {
                        echo \yii\helpers\Json::encode(['output' => '', 'message' => \Yii::t('app', 'Dữ liệu không hợp lệ')]);
                    }
                } else {
                    echo \yii\helpers\Json::encode(['output' => '', 'message' => \Yii::t('app', 'Danh mục không tồn tại')]);
                }
            } // else if nothing to do always return an empty JSON encoded output
            else {
                echo \yii\helpers\Json::encode(['output' => '', 'message' => '']);
            }

            return;
        }
        $params = Yii::$app->request->get();
        $searchModel = new \common\models\CategorySearch();
        if (isset($params['CategorySearch']) && $params['CategorySearch']['status'] !== null && $params['CategorySearch']['status'] !== '') {
            $dataProvider  = $searchModel->search($params);
        } else {
            $categories   = $searchModel->getAllCategories(null, true);
            $dataProvider = new ArrayDataProvider([
                'key'        => 'id',
                'allModels'  => $categories,
                'pagination' => [
                    'pageSize' => 20,
                ],
            ]);
        }

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
        ]);
    }

    /**
     * Displays a single Category model.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Category model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Category();
        $model->setScenario('admin_create_update');
        $post = Yii::$app->request->post();
        if (Yii::$app->request->isAjax && isset($post['ajax']) && $model->load($post)) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {
            $image                   = UploadedFile::getInstance($model, 'images');
            if ($image) {
                $file_name = Yii::$app->user->id . '.' . uniqid() . time() . '.' . $image->extension;
                $tmp       = Yii::getAlias('@backend') . '/web/' . Yii::getAlias('@cat_image') . '/';
                if (!file_exists($tmp)) {
                    mkdir($tmp, 0777, true);
                }
                if ($image->saveAs($tmp . $file_name)) {
                    $model->images = $file_name;
                }
            }
            $model->ascii_name  = CVietnameseTools::makeSearchableStr($model->display_name);
            $model->child_count = 0;

            if ($model->save()) {
                if ($model->parent_id != null) {
                    $modelParent = $model->parent;
                    ++$modelParent->child_count;
                    // $model->order_number = $modelParent->child_count;
                    $model->level = $modelParent->level + 1;
                    $model->path  = $modelParent->path . '/' . $model->id;
                    $modelParent->save();
                } else {
                    $model->path        = $model->id;
                    $model->level       = 0;
                    $model->child_count = 0;
                    $maxOrder           = Category::find()
                        ->select(['max(order_number) as `order`'])
                        ->where('level=0')
                        ->scalar();
                }

                $model->order_number = $model->id;
                $model->save();
                Yii::info($model->getErrors());

                \Yii::$app->getSession()->setFlash('success', \Yii::t('app', 'Thêm mới thành công'));

                return $this->redirect(['index']);
            } else {
                // Yii::info($model->getErrors());
                 Yii::$app->getSession()->setFlash('error', 'Lỗi lưu danh mục');
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Category model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario('admin_create_update');
        $post = Yii::$app->request->post();
        if (Yii::$app->request->isAjax && isset($post['ajax']) && $model->load($post)) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        $oldParentId = $model->parent_id;
        $oldParent   = $model->parent;
        $oldImg      = $model->images;
        if ($model->load(Yii::$app->request->post())) {
            $image                   = UploadedFile::getInstance($model, 'images');
            if ($image) {
                $file_name = Yii::$app->user->id . '.' . uniqid() . time() . '.' . $image->extension;
                $tmp       = Yii::getAlias('@backend') . '/web/' . Yii::getAlias('@cat_image') . '/';
                if (!file_exists($tmp)) {
                    mkdir($tmp, 0777, true);
                }

                if ($image->saveAs($tmp . $file_name)) {
                    $model->images = $file_name;
                }
            } else {
                $model->images = $oldImg;
            }

            $model->ascii_name = CVietnameseTools::makeSearchableStr($model->display_name);
            if ($model->save()) {
                if ($oldParentId != $model->parent_id) {
                    if ($model->parent_id != null) {
                        if ($oldParent != null) {
                            --$oldParent->child_count;
                            $catSameParent = Category::find()->where('parent_id = :p_parent_id', [':p_parent_id' => $oldParent->id])
                                ->andWhere('order_number > :p_order_number', [':p_order_number' => $model->order_number])->all();
                            $oldParent->save();
                        } else {
                            $catSameParent = Category::find()->where('parent_id is null')
                                ->andWhere('order_number > :p_order_number', [':p_order_number' => $model->order_number])->all();
                        }
                        foreach ($catSameParent as $catSame) {
                            /* @var $catSame Category */
                            $catSame->order_number -= 1;
                            $catSame->update();
                        }
                        /** @var  $currentParent Category */
                        $currentParent = Category::findOne($model->parent_id);
                        ++$currentParent->child_count;
                        $currentParent->save();

                        $model->path         = $currentParent->path . '/' . $model->id;
                        $model->order_number = $currentParent->child_count;
                        $model->level        = $currentParent->level + 1;
                    } else {
                        $model->path         = $model->id;
                        $model->level        = 0;
                        $maxOrder            = Category::find()->select(['max(order_number) as `order`'])->where('level=0')->andWhere(['site_id' => Yii::$app->user->id])->scalar();
                        $model->order_number = $maxOrder + 1;
                    }
                    $model->save();
                }
                \Yii::$app->getSession()->setFlash('success', \Yii::t('app', 'Cập nhật thành công'));

                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Category model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $cat         = $this->findModel($id);
        $cat->status = Category::STATUS_DELETED;
        $cat->save(false);
        $modelParent = $cat->parent;
        if($modelParent){
            --$modelParent->child_count;
            $modelParent->save();
        }
        \Yii::$app->getSession()->setFlash('success', \Yii::t('app', 'Xóa thành công'));
        return $this->redirect(['index']);
    }

    /**
     * Finds the Category model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param int $id
     *
     * @return Category the loaded model
     *
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Category::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionMoveDown()
    {
        $id = Yii::$app->request->get('id');

        return $this->orderCat($id, 'down');
    }

    public function actionMoveUp()
    {
        $id = Yii::$app->request->get('id');

        return $this->orderCat($id, 'up');
    }

    private function orderCat($id, $action = 'up')
    {
        $sort  = ['up' => SORT_ASC, 'down' => SORT_DESC][$action];
        $state = ['up' => '>', 'down' => '<'][$action];
        $cat1  = Category::findOne($id);

        $orderForNextCategories = $cat1->order_number;

        if ($cat1->parent_id === null) {
            $cat2 = Category::find()
                ->andWhere("category.order_number $state :order", [':order' => $cat1->order_number])
                ->andWhere('parent_id IS NULL')
                ->orderBy(['category.order_number' => $sort])->one();
        } else {
            $cat2 = Category::find()
                ->andWhere("category.order_number $state :order", [':order' => $cat1->order_number])
                ->andWhere(['parent_id' => $cat1->parent_id])
                ->orderBy(['category.order_number' => $sort])->one();
        }

        if ($cat2 !== null) {
            $cat1->order_number = $cat2->order_number;
            $cat2->order_number = $orderForNextCategories;

            return $cat1->update() && $cat2->update();
        }

        return false;
    }

    public function actionResetOrder($secret = 'vivas@124')
    {
        $categories = Category::findAll(['show_on_portal' => 1]);

        foreach ($categories as $k => $cat) {
            Category::updateAll(['order_number' => $k + 1], 'id = ' . $cat->id);
        }

        return $this->redirect(['index']);
    }
}
