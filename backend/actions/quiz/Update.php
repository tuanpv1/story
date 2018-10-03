<?php
namespace backend\actions\quiz;
use common\models\Quiz;
use common\models\Topic;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * Created by PhpStorm.
 * User: Linh
 * Date: 21/06/2016
 * Time: 4:18 PM
 */
class Update extends Action
{
    public $view ;
    public function run($id)
    {
        /** @var Quiz $model */
        $model = Quiz::findOne($id);
        if(!$model){
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        if ($model->load(\Yii::$app->request->post())&& $model->initPreCreateModel() && $model->save()) {
            $model->assignGrade();
            return $this->controller->redirect(['view', 'id' => $model->id]);
        }

        $topicArr = ArrayHelper::map(Topic::find()->asArray()->all(), 'id', 'name');
        $model->topic = $model->getTopicAssigned();
        return $this->controller->render($this->view ? $this->view : $this->id, [
                'model' =>$model,
                'topicArr' =>$topicArr
        ]);
    }
}