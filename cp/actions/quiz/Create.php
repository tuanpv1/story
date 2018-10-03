<?php
namespace backend\actions\quiz;
use common\models\Lesson;
use common\models\Quiz;
use common\models\Topic;
use yii\base\Action;
use yii\helpers\ArrayHelper;

/**
 * Created by PhpStorm.
 * User: Linh
 * Date: 21/06/2016
 * Time: 4:18 PM
 */
class Create extends Action
{
    public $view ;
    public function run()
    {
        $model = new Quiz();

        if ($model->load(\Yii::$app->request->post())&& $model->initPreCreateModel() && $model->save()) {
            $model->assignTopic();
            $model->assignGrade();
            return $this->controller->redirect(['view', 'id' => $model->id]);
        }

        $topicArr = ArrayHelper::map(Topic::find()->asArray()->all(), 'id', 'name');
        return $this->controller->render($this->view ? $this->view : $this->id, [
                'model' =>$model,
                'topicArr' =>$topicArr,
        ]);
    }
}