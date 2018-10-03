<?php

namespace cp\controllers;

use common\helpers\Encrypt;
use common\helpers\GetRamdom;
use common\models\Dealer;
use common\models\PromotionCode;
use common\models\PromotionCodeSearch;
use common\models\User;
use cp\models\ViewPromotionForm;
use Exception;
use PHPExcel;
use PHPExcel_IOFactory;
use Yii;
use common\models\Promotion;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PromotionCodeController implements the CRUD actions for Promotion model.
 */
class PromotionCodeController extends BaseCPController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return parent::behaviors();
    }

    /**
     * Lists all PromotionCode models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PromotionCodeSearch();
        $params = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($params, Yii::$app->user->identity->dealer_id);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * View a new Promotion model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionView($id = null)
    {
        $dealer_id = Yii::$app->user->identity->dealer_id;
        if (empty($id)) {
            $model = Promotion::find()->where(['dealer_id' => $dealer_id])
                ->andWhere(['status' => Promotion::STATUS_ACTIVE])
                ->andWhere(['>', 'expired_time', time()])
                ->andWhere(['gen_code' => Promotion::STATUS_GEN_CODE])->one();
            if (!$model) {
                Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Không có chương trình ưu đãi nào để gen mã! Vui lòng kiểm tra lại các chương trình ưu đãi trước khi gen mã ưu đãi.'));
                return $this->redirect(['promotion/index']);
            }
            $id = $model->id;
        }
        $model_promotion = $this->findModel($id);
        if ($model_promotion->gen_code == Promotion::STATUS_GEN_CODED) {
            Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Chương trình ưu đãi đã được gen mã! Vui lòng kiểm tra lại!'));
            return $this->redirect(['promotion/index']);
        }
        return $this->render('view', [
            'dealer_id' => $dealer_id,
            'model' => $model_promotion,
        ]);
    }


    protected function findModel($id)
    {
        if (($model = Promotion::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function findPromotion($cp_id)
    {
        /** @var Promotion $model */
        $model = Promotion::find()->where(['dealer_id' => $cp_id])
            ->andWhere(['status' => Promotion::STATUS_ACTIVE])
            ->andWhere(['>', 'expired_time', time()])
            ->andWhere(['gen_code' => Promotion::STATUS_GEN_CODE])
            ->orderBy(['expired_time' => SORT_ASC])->one();
        if ($model) {
            return $model->id;
        } else {
            return null;
        }
    }

    public function actionGenCode($id)
    {
        $name_code = Dealer::findOne(Yii::$app->user->identity->dealer_id)->name_code;
        $promotion = Promotion::findOne($id);
        if ($promotion->gen_code == Promotion::STATUS_GEN_CODED || $promotion->status != Promotion::STATUS_ACTIVE ) {
            Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Chương trình ưu đãi đã được gen mã hoặc chưa kích hoạt! Vui lòng kiểm tra lại!'));
            return $this->redirect(Yii::$app->request->referrer ?: ['index']);
        }
        $n = 1;
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $objPHPExcel = new PHPExcel();
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
            $objSheet = $objPHPExcel->getActiveSheet();
            $objSheet->getCell('A1')->setValue(Yii::t("app", "Danh sách mã được gen từ chương trình " . $promotion->name));
            $objSheet->mergeCells('A1:E1');
            $objSheet->getCell('A2')->setValue(Yii::t("app", "STT"));
            $objSheet->getCell('B2')->setValue(Yii::t("app", "Mã ưu đãi"));
            $objSheet->getCell('C2')->setValue(Yii::t("app", "Chương trình ưu đãi"));
            $objSheet->getCell('D2')->setValue(Yii::t("app", "Đại lý"));
            $objSheet->getCell('E2')->setValue(Yii::t("app", "Thời gian tạo"));
            $rowOrderError = 3;
            while ($n <= $promotion->total_promotion_code) {
                $code_part1 = $name_code;
                $ramdom = new GetRamdom();
                $code_part2 = $ramdom->get_rand_numbers(10);

                $code = strtolower(trim($code_part1)) . $code_part2;
                // Mã hóa code
                $code_save = Encrypt::encryptCode($code, Yii::$app->params['key_encrypt']);
                // Kiểm tra đã có mã tồn tại chưa
                $model_check = PromotionCode::find()->andWhere(['code' => $code_save])
                    ->andWhere(['>=', 'expired_at', time()])
                    ->andWhere(['status' => PromotionCode::STATUS_NOT_USED])
                    ->one();
                if (!$model_check) {
                    // Lưu lại
                    $promotionCode = new PromotionCode();
                    $promotionCode->code = $code_save;
                    $promotionCode->expired_at = PromotionCode::EXPIRED_FOREVER;
                    $promotionCode->status = PromotionCode::STATUS_NOT_USED;
                    $promotionCode->promotion_id = $id;
                    if ($promotionCode->save()) {
                        // Đẩy vào excel
                        $report = [];
                        $report[PromotionCode::EXCEL_ROW1] = $n;
                        $report[PromotionCode::EXCEL_ROW2] = $code;
                        $report[PromotionCode::EXCEL_ROW3] = $promotion->name;
                        $report[PromotionCode::EXCEL_ROW4] = Yii::$app->user->identity->full_name;
                        $report[PromotionCode::EXCEL_ROW5] = date('d-m-Y H:i:s', $promotionCode->created_at);
                        $errorsArr[0] = $report;
                        foreach ($errorsArr as $order => $errors) {
                            $objSheet->getCell($promotionCode->getCell(PromotionCode::EXCEL_ROW1, $rowOrderError))->setValue($order);
                            foreach ($errors as $attr => $error) {
                                $objSheet->getCell($promotionCode->getCell($attr, $rowOrderError))->setValue($error);
                            }
                        }
                        $rowOrderError++;
                        $n++; // Nghiêm cấm xóa
                    }
                }
            }

            $objSheet->getColumnDimension('A')->setAutoSize(true);
            $objSheet->getColumnDimension('B')->setAutoSize(true);
            $objSheet->getColumnDimension('C')->setAutoSize(true);
            $objSheet->getColumnDimension('D')->setAutoSize(true);
            $objSheet->getColumnDimension('E')->setAutoSize(true);

            $file_name = basename(date('dmYHis') . 'code') . '.xlsx';
            $url = Yii::getAlias('@file_export') . "/" . $file_name;
            $objWriter->save($url);

            // Cập nhật lại trạng thái đã gen_code
            $promotion->gen_code = Promotion::STATUS_GEN_CODED;
            $promotion->file = $file_name;
            $promotion->save();
            $transaction->commit();

            Yii::$app->session->setFlash('success', Yii::t('app', 'Gen mã thành công từ chương trình ưu đãi ' . $promotion->name));
            Yii::$app->session->set('file', $promotion->file);
            return $this->redirect(['index']);

        } catch (Exception $e) {
            Yii::trace($e->getMessage());
            Yii::$app->session->setFlash('error', Yii::t('app', 'Lỗi không gen mã thành công, Vui lòng thử lại sau!'));
            $transaction->rollBack();
            return $this->redirect(['view', 'id' => $id]);
        }
    }


    public function actionGetView()
    {
        $param = Yii::$app->request->post();
        if ($param && !empty($param['Promotion']['name'])) {
            return $this->redirect(['view', 'id' => $param['Promotion']['name']]);
        }
    }
}
