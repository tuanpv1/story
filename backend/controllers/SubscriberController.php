<?php

namespace backend\controllers;

use api\helpers\Message;
use common\components\ActionLogTracking;
use common\components\ActionSPFilter;
use common\components\SPOwnerFilter;
use common\helpers\CommonConst;
use common\helpers\ResMessage;
use common\models\ContentViewLogSearch;
use common\models\Device;
use common\models\DeviceSearchToAssign;
use common\models\LogSubscriberSwap;
use common\models\LogSubscriberSwapSearch;
use common\models\Service;
use common\models\ServiceGroupAsm;
use common\models\ServiceSearch;
use common\models\SmsMessage;
use common\models\SmsMessageSearch;
use common\models\SmsSupport;
use common\models\SmsSupportSearch;
use common\models\SmsUserAsm;
use common\models\Subscriber;
use common\models\SubscriberActivitySearch;
use common\models\SubscriberDeviceAsm;
use common\models\SubscriberSearch;
use common\models\SubscriberServiceAsm;
use common\models\SubscriberServiceAsmSearch;
use common\models\SubscriberToken;
use common\models\SubscriberTokenSearch;
use common\models\SubscriberTransaction;
use common\models\SubscriberTransactionSearch;
use common\models\User;
use common\models\UserActivity;
use DateTime;
use Exception;
use moonland\phpexcel\Excel;
use PHPExcel;
use PHPExcel_IOFactory;
use sp\models\SendEmailInternalForm;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * SubscriberController implements the CRUD actions for Subscriber model.
 */
class SubscriberController extends BaseBEController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            [
                'class'              => ActionLogTracking::className(),
                'user'               => Yii::$app->user,
                'model_type_default' => UserActivity::ACTION_TARGET_TYPE_SUBSCRIBER,
                'post_action'        => [
                    ['action' => 'create', 'accept_ajax' => false],
                    ['action' => 'update', 'accept_ajax' => false],
                    ['action' => 'delete', 'accept_ajax' => false],
                ],
                // 'only' => ['create', 'update', 'delete', 'cancel-service-package', 'purchase-service-package']
            ],
        ]);
    }

    /**
     * Lists all Subscriber models.
     * @return mixed
     */
    public function actionIndex()
    {
        $param       = Yii::$app->request->queryParams;
        $searchModel = new SubscriberSearch();
        $param['SubscriberSearch']['site_id'] = $this->sp_user->site_id;
        $dataProvider                         = $searchModel->search($param);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'site_id'=> $this->sp_user->site_id,
            'site'=> $this->sp_user->site_id
        ]);
    }

    /**
     * Displays a single Subscriber model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id, $active = 1)
    {
        $param = Yii::$app->request->queryParams;

        $subscriber = $this->findModel($id);

        // Danh sách gói cuóc
        $myService                                            = new SubscriberServiceAsmSearch();
        $param['SubscriberServiceAsmSearch']['subscriber_id'] = $id;
        $lstMyService                                         = $myService->search($param,true);

        //Lịch sử giao dịch
        $transaction                                           = new SubscriberTransactionSearch();
        $param['SubscriberTransactionSearch']['subscriber_id'] = $id;
        if (isset($param['from_date_trans'])) {
            $param['SubscriberTransactionSearch']['from_date'] = $param['from_date_trans'];
            Yii::info($param['SubscriberTransactionSearch']['from_date']);
        }else{
            $param['from_date_trans']=null;
        }
        if (isset($param['to_date_trans'])) {
            $param['SubscriberTransactionSearch']['to_date'] = $param['to_date_trans'];
            Yii::info($param['SubscriberTransactionSearch']['to_date']);
        }else{
            $param['to_date_trans']=null;
        }
        $lstTransaction = $transaction->searchCr33($param);

        //Lịch sử tương tác
        $activities                                         = new SubscriberActivitySearch();
        $param['SubscriberActivitySearch']['subscriber_id'] = $id;
        if (isset($param['from_date_acts'])) {
            $param['SubscriberActivitySearch']['from_date'] = $param['from_date_acts'];
        }else{
            $param['from_date_acts']=null;
        }
        if (isset($param['to_date_acts'])) {
            $param['SubscriberActivitySearch']['to_date'] = $param['to_date_acts'];
        }else{
            $param['to_date_acts']=null;
        }
        $lstActivities = $activities->search($param);

        //Lịch sử MO/MT
        $smSms                                      = new SmsMessageSearch();
        $param['SmsMessageSearch']['subscriber_id'] = $id;
        if (isset($param['from_date_sms'])) {
            $param['SmsMessageSearch']['from_date'] = $param['from_date_sms'];
        }else{
            $param['from_date_sms']=null;
        }
        if (isset($param['to_date_sms'])) {
            $param['SmsMessageSearch']['to_date'] = $param['to_date_sms'];
        }else{
            $param['to_date_sms']=null;
        }
        $lstSms = $smSms->search($param);

        //Đăng kí gói cước
        $serviceSearch                     = new ServiceSearch();
        $param['ServiceSearch']['site_id'] = $this->sp_user->site_id;
        $lstService                        = $serviceSearch->searchEx($param);

        //Lịch sử viewlog
//        $viewLogs                                       = new ContentViewLogSearch();
//        $param['ContentViewLogSearch']['subscriber_id'] = $id;
//        $param['ContentViewLogSearch']['site_id']       = $this->sp_user->site_id;
//        $param['ContentViewLog']['dealer_id']           = $this->sp_user->dealer_id;
//        if (isset($param['from_date_view'])) {
//            $param['ContentViewLogSearch']['from_date'] = $param['from_date_view'];
//
//        }
//        if (isset($param['to_date_view'])) {
//            $param['ContentViewLogSearch']['to_date'] = $param['to_date_view'];
//
//        }
//        $lstViewLogs = $viewLogs->search($param);

        // Thiet bi cua thue bao
        $assignedDevices = new ArrayDataProvider([
            'allModels' => Device::findBySubscriber($id),
        ]);

        // Tim kiem thiet bi de gan
        $searchDevices = new DeviceSearchToAssign();

        $param['DeviceSearchToAssign']['site_id']   = $this->sp_user->site_id;
        $param['DeviceSearchToAssign']['dealer_id'] = $subscriber->dealer_id;
        if (isset($param['device_id'])) {
            $param['DeviceSearchToAssign']['device_id'] = $param['device_id'];
        }
        $lstDevices = $searchDevices->searchToAssignForSubscriber($param);

        return $this->render('view', [
            'model'            => $subscriber,
            'active'           => $active,
            'serviceSearch'    => $serviceSearch,
            'lstService'       => $lstService,
            'lstMyService'     => $lstMyService,
            'devices'          => $assignedDevices,
            'searchDevices'    => $searchDevices,
            'lstDevices'       => $lstDevices,
            'lstTransaction'   => $lstTransaction,
            'transaction' => $transaction,
            'lstSms'           => $lstSms,
            'searchSms'        => $smSms,
//            'lstViewLogs'      => $lstViewLogs,
            'lstActivities'    => $lstActivities,
            'searchActivities' => $activities,
            'from_date_trans'=>$param['from_date_trans'],
            'to_date_trans'=>$param['to_date_trans'],
            'from_date_acts'=>$param['from_date_acts'],
            'to_date_acts'=>$param['to_date_acts'],
            'from_date_sms'=>$param['from_date_sms'],
            'to_date_sms'=>$param['to_date_sms'],
        ]);
    }

    /**
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionCancelServicePackage()
    {
        //        Yii::$app->response->format = Response::FORMAT_JSON;

        $data          = Yii::$app->request->post();
        $ssaId         = $data['subscriber_service_asm_id'];
        $subscriber_id = $data['subscriber_id'];

        $ssa = SubscriberServiceAsm::findOne(['id' => $ssaId, 'status' => SubscriberServiceAsm::STATUS_ACTIVE]);

        if (!$ssa) {
//            return [
            //                'success' => false,
            //                'message' => 'Không tìm thấy gói cước hoặc gói cước đã được hủy.',
            //            ];
            Yii::$app->getSession()->setFlash('error', Yii::t("app", "Không tìm thấy gói cước hoặc gói cước đã được hủy."));
            return $this->redirect(['subscriber/view', 'id' => $subscriber_id, 'active' => 4]);
        }

        /** @var Service $service */
        $service    = Service::findOne(['id' => $ssa->service_id, 'status' => Service::STATUS_ACTIVE]);
        $subscriber = $this->findModel($ssa->subscriber_id);
        $response   = $subscriber->cancelServicePackage($service,
            SubscriberTransaction::CHANNEL_TYPE_CSKH,
            SubscriberTransaction::TYPE_CANCEL, false, null, $ssaId);

        $success = $response['error'] == CommonConst::API_ERROR_NO_ERROR;
//        return [
        //            'success' => $success,
        //            'message' => $response['message']
        //        ];
        Yii::$app->getSession()->setFlash($success ? 'success' : 'error', $success ? Yii::t("app", "Hủy gói cước thành công.") : $response['message']);
        return $this->redirect(['subscriber/view', 'id' => $subscriber_id, 'active' => 4]);
    }

    /**
     * @return \yii\web\Response
     */
    public function actionPurchaseServicePackages()
    {
//        Yii::$app->response->format = Response::FORMAT_JSON;

        $data          = Yii::$app->request->post();
        $subscriber_id = $data['subscriber_id'];
        $service_ids   = $data['service_ids'];

        if (!$subscriber_id || !$service_ids || count($service_ids) == 0) {
            Yii::$app->getSession()->setFlash('error', Yii::t("app", "Thông tin không hợp lệ. Vui lòng thử lại."));
            return $this->redirect(['subscriber/view', 'id' => $subscriber_id, 'active' => 4]);
        }

        /* @var $subscriber Subscriber */
        $subscriber = Subscriber::findOne(['id' => $subscriber_id]);
        if ($subscriber->status != Subscriber::STATUS_ACTIVE) {
            Yii::$app->getSession()->setFlash('error', Yii::t("app", "Chỉ có thể đăng ký gói cước cho thuê bao ở trạng thái Đang hoạt động."));
            return $this->redirect(['subscriber/view', 'id' => $subscriber_id, 'active' => 4]);
        }

//        $countServices = count($service_ids);
//        for ($i = 0; $i < $countServices - 1; $i++) {
//            for ($j = $i + 1; $j < $countServices; $j++) {
//                $service1 = Service::findOne(['id' => $service_ids[$i], 'status' => Service::STATUS_ACTIVE]);
//                $service2 = Service::findOne(['id' => $service_ids[$j], 'status' => Service::STATUS_ACTIVE]);
//                /**
//                 * Kiem tra goi cuoc mua co trung voi goi cuoc trong cung group hay ko (group: vtv -> goi ngay,goi tuan,goi thang)
//                 * Trong mot group thi chi dc mua 1 goi cuoc trong group do
//                 */
//                $groups1 = $service1->serviceGroupAsms;
//                $groups2 = $service2->serviceGroupAsms;
//
//                foreach ($groups1 as $group1) {
//                    /** @var $group1 ServiceGroupAsm */
//                    foreach ($groups2 as $group2) {
//                        /** @var $group2 ServiceGroupAsm */
//
//                        if ($group1->service_group_id == $group2->service_group_id) {
//                            $name1     = $service1->display_name;
//                            $name2     = $service2->display_name;
//                            $groupName = $group1->serviceGroup->display_name;
//                            Yii::$app->getSession()->setFlash('error', Yii::t("app", "Không thể đăng ký cùng lúc 2 gói cước ") . $name1, ", " . $name2 . Yii::t("app", " cùng thuộc nhóm") . $groupName);
//                            return $this->redirect(['subscriber/view', 'id' => $subscriber_id, 'active' => 4]);
//                        }
//                    }
//                }
//            }
//        }

        /** @var Service $service */
        foreach ($service_ids as $service_id) {
            $service  = Service::findOne(['id' => $service_id, 'status' => Service::STATUS_ACTIVE]);
            $response = $subscriber->purchaseServicePackage(SubscriberTransaction::CHANNEL_TYPE_CSKH,
                $service,
                SubscriberTransaction::TYPE_REGISTER, false
            );
            $success = $response['error'] == CommonConst::API_ERROR_NO_ERROR;
            if (!$success) {
                Yii::$app->getSession()->setFlash('error', $response['message']);
                return $this->redirect(['subscriber/view', 'id' => $subscriber_id, 'active' => 4]);
            }
        }

        $success = $response['error'] == CommonConst::API_ERROR_NO_ERROR;

        Yii::$app->getSession()->setFlash($success ? 'success' : 'error', $success ? Yii::t("app", "Đăng ký gói cước thành công.") : $response['message']);
        return $this->redirect(['subscriber/view', 'id' => $subscriber_id, 'active' => 4]);
    }

    /**
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionExtendServicePackage()
    {
        //        Yii::$app->response->format = Response::FORMAT_JSON;

        $data          = Yii::$app->request->post();
        $ssaId         = $data['subscriber_service_asm_id'];
        $subscriber_id = $data['subscriber_id'];

        $ssa = SubscriberServiceAsm::findOne($ssaId);

        if (!$ssa) {
//            return [
            //                'success' => false,
            //                'message' => 'Không tìm thấy gói cước để gia hạn.',
            //            ];
            Yii::$app->getSession()->setFlash('error', Yii::t("app", "Không tìm thấy gói cước để gia hạn."));
            return $this->redirect(['subscriber/view', 'id' => $subscriber_id, 'active' => 4]);
        }

        /** @var Service $service */
        $service = Service::findOne(['id' => $ssa->service_id, 'status' => Service::STATUS_ACTIVE]);
        if (!$service) {
//            return [
            //                'success' => false,
            //                'message' => 'Không tìm thấy gói cước.',
            //            ];
            Yii::$app->getSession()->setFlash('error', Yii::t("app", "Không tìm thấy gói cước."));
            return $this->redirect(['subscriber/view', 'id' => $subscriber_id, 'active' => 4]);
        }
        $subscriber = $this->findModel($ssa->subscriber_id);
        if (!$subscriber || $subscriber->status != Subscriber::STATUS_ACTIVE) {
//            return [
            //                'success' => false,
            //                'message' => 'Không tìm thấy thuê bao hoặc thuê bao không còn hoạt động.',
            //            ];
            Yii::$app->getSession()->setFlash('error', Yii::t("app", "Không tìm thấy thuê bao hoặc thuê bao không còn hoạt động."));
            return $this->redirect(['subscriber/view', 'id' => $subscriber_id, 'active' => 4]);
        }
        $response = $subscriber->purchaseServicePackage(SubscriberTransaction::CHANNEL_TYPE_CSKH,
            $service,
            SubscriberTransaction::TYPE_RENEW
        );

        $success = $response['error'] == CommonConst::API_ERROR_NO_ERROR;
//        return [
        //            'success' => $success,
        //            'message' => $success ? 'Gia hạn gói cước thành công' : $response['message']
        //        ];
        Yii::$app->getSession()->setFlash($success ? 'success' : 'error', $success ? Yii::t("app", "Gia hạn gói cước thành công.") : $response['message']);
        return $this->redirect(['subscriber/view', 'id' => $subscriber_id, 'active' => 4]);
    }

    /**
     * Creates a new Subscriber model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Subscriber();
        $model->setScenario('create');
        $model->site_id = $this->sp_user->site_id;

        if ($model->load(Yii::$app->request->post())) {
            $model->authen_type = Subscriber::AUTHEN_TYPE_ACCOUNT;
            $model->client_type = Subscriber::CHANNEL_TYPE_SYSTEM;
            $res                = $model->saveProperties();
            if ($res['status']) {
                Yii::$app->getSession()->setFlash('success', Yii::t("app", "Thêm mới thành công!"));
                return $this->redirect(['view', 'id' => $res['subscriber']->id]);
            } else {
                Yii::$app->getSession()->setFlash('error', $res['message']);
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Subscriber model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            // Khong cho phep sua username
            $oldUserName     = $this->findModel($id)->username;
            $model->username = $oldUserName;

            $res = $model->saveProperties();
            if ($res['status']) {
                Yii::$app->getSession()->setFlash('success', Yii::t("app", "Cập nhật thành công!"));
                return $this->redirect(['view', 'id' => $res['subscriber']->id]);
            } else {
                Yii::$app->getSession()->setFlash('error', $res['message']);
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Subscriber model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->status != Subscriber::STATUS_INACTIVE) {
            Yii::$app->getSession()->setFlash('error', Yii::t("app", "Bạn chỉ được phép xóa các thuê bao có trạng thái 'Tạm khóa'"));
            return $this->redirect(Yii::$app->request->referrer);
        }

        $model->status = Subscriber::STATUS_DELETED;

        if (!$model->save()) {
            Yii::error($model->errors);
            Yii::$app->getSession()->setFlash('error', Yii::t("app", "Xóa thất bại. Vui lòng thử lại."));
        } else {
            Yii::$app->getSession()->setFlash('success', Yii::t("app", "Xóa thành công."));
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the Subscriber model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Subscriber the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Subscriber::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t("app", "Trang bạn yêu cầu không tồn tại."));
        }
    }

    public function actionResendMt($sms_id, $subscriber_id)
    {
//        Yii::$app->response->format = Response::FORMAT_JSON;
        //        $post = Yii::$app->request->post();

//        $success = false;
        //        $message = "Tham số không đúng";

        if ($sms_id && $subscriber_id) {

            $subscriber = Subscriber::findOne(['id' => $subscriber_id]);
            /** @var SmsMessage $sms */
            $sms = SmsMessage::findOne(['id' => $sms_id]);
            if ($sms && $subscriber) {
                /** @var SmsMessage $result */
                $result = ResMessage::resend($subscriber, $sms->message);
                if ($result) {
                    if (substr(trim($result->mt_status), 0, 1) == '0') {
                        Yii::$app->getSession()->setFlash('success', Yii::t("app", "Gửi lại MT thành công"));
                    } else {
                        Yii::$app->getSession()->setFlash('error', Yii::t("app", "Lỗi hệ thống, vui lòng thử lại sau. Mã lỗi: ") . $result->mt_status);
                    }
                } else {
                    Yii::$app->getSession()->setFlash('error', Yii::t("app", "Lỗi hệ thống, vui lòng thử lại sau"));
                }
            } else {
                Yii::$app->getSession()->setFlash('error', Yii::t("app", "Không tồn tại MT này"));
            }
        } else {
            Yii::$app->getSession()->setFlash('error', Yii::t("app", "Tham số không đúng"));
        }

        $this->redirect(\Yii::$app->urlManager->createUrl(["subscriber/view", 'id' => $subscriber_id, 'active' => 3]));
    }

    public function actionAssignDevicesForSubscriber()
    {

        Yii::$app->response->format = Response::FORMAT_JSON;

        $data          = Yii::$app->request->post();
        $subscriber_id = $data['subscriber_id'];
        $device_ids    = $data['device_ids'];
        $check         = SubscriberDeviceAsm::findOne(['subscriber_id' => $subscriber_id, 'status' => SubscriberDeviceAsm::STATUS_ACTIVE]);
        if ($check) {
            Yii::$app->getSession()->setFlash('error', Yii::t("app", "Thuê bao vẫn đang được gán cho một thiết bị "));
            return $this->redirect(['subscriber/view', 'id' => $subscriber_id, 'active' => 5]);
        }
//        foreach ($device_ids as $device_id) {
        //            $subscriber = SubscriberDeviceAsm::findOne(['subscriber_id' => $subscriber_id, 'device_id' => $device_id, 'status' => SubscriberDeviceAsm::STATUS_ACTIVE]);
        //            if ($subscriber) {
        //                $device = Device::findOne($device_id);
        //                Yii::$app->getSession()->setFlash('error', Yii::t("app", "Thiết bị ") . $device->device_id . Yii::t("app", " hiện tại vẫn đang được gán cho thuê bao này."));
        //
        //            }
        //        }

        $success = false;
        foreach ($device_ids as $device_id) {
            $model = new SubscriberDeviceAsm();

            $model->subscriber_id = $subscriber_id;
            $model->device_id     = $device_id;
            $model->status        = SubscriberDeviceAsm::STATUS_ACTIVE;

            if ($model->save()) {
                $success = true;
//                $subscriber = $model->subscriber;
                //                if ($subscriber->status != Subscriber::STATUS_ACTIVE) {
                //                    $subscriber->status = Subscriber::STATUS_ACTIVE;
                //                    if (!$subscriber->save(true, ['status'])) {
                //                        $success = false;
                //                    }
                //                }
                $device = $model->device;
                if ($device->status != Device::STATUS_ACTIVE) {
                    $device->status = Subscriber::STATUS_ACTIVE;
                    if (!$device->save(true, ['status'])) {
                        $success = false;
                    }
                }
                $subscriber = $model->subscriber;
                if ($subscriber->machine_name != $device->device_id) {
                    $subscriber->machine_name = $device->device_id;
                    if (!$subscriber->save(true, ['machine_name'])) {
                        $success = false;
                    }
                }

            }
        }
        if ($success) {
            Yii::$app->getSession()->setFlash('success', Yii::t("app", "Gán thiết bị thành công"));
            return $this->redirect(['subscriber/view', 'id' => $subscriber_id, 'active' => 5]);
        } else {
            Yii::$app->getSession()->setFlash('error', Yii::t("app", "Lỗi không xác định."));
            return $this->redirect(['subscriber/view', 'id' => $subscriber_id, 'active' => 5]);
        }
    }

    public function actionUnassignDeviceForSubscriber()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $data          = Yii::$app->request->post();
        $subscriber_id = $data['subscriber_id'];
        $device_id     = $data['device_id'];

        $subscriberDeviceAsm = SubscriberDeviceAsm::findOne(['subscriber_id' => $subscriber_id, 'device_id' => $device_id, 'status' => SubscriberDeviceAsm::STATUS_ACTIVE]);

        if (!$subscriberDeviceAsm) {
            return [
                'success' => false,
                'message' => Yii::t("app", "Thiết bị chưa được gán cho thuê bao."),
            ];
        }

        $subscriberDeviceAsm->status = SubscriberDeviceAsm::STATUS_REMOVED;

        if ($subscriberDeviceAsm->save()) {

//            $subscriber_token = SubscriberToken::find()->andWhere([''])

            $subscriber               = Subscriber::findOne($subscriber_id);
            $subscriber->authen_type  = Subscriber::AUTHEN_TYPE_MAC_ADDRESS;
            $subscriber->status       = Subscriber::STATUS_MAINTAIN;
            $subscriber->machine_name = '';
            if ($subscriber->save()) {
                return [
                    'success' => true,
                    'message' => Yii::t("app", "Bỏ gán thiết bị thành công."),
                ];
            }
        }
        return [
            'success' => false,
            'message' => Yii::t("app", "Lỗi không xác định."),
        ];
    }

    public function actionSendEmailInternal()
    {
        $model = new SendEmailInternalForm();
        if ($model->load(Yii::$app->request->post())) {
            $userArr = [];
            // Xử lí với file_user
            $file_user = UploadedFile::getInstance($model, 'file_user');
            if (!$file_user) {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Thông tin không hợp lệ, danh sách gửi không được bỏ trống -  xin vui lòng nhập lại'));
                return $this->render('send-email-internal', [
                    'model' => $model,
                ]);
            }

            $file_name        = uniqid() . time() . '.' . $file_user->extension;
            $target_file_user = Yii::getAlias('@file_customer') . '/' . $file_name;

            // lưu vào bảng
            $smsSuport = new SmsSupport();
            if ($file_user->saveAs($target_file_user)) {
                $smsSuport->file_user = $file_name;
            }
            $smsSuport->type    = SmsSupport::TYPE_CSKH_INTERNAL;
            $smsSuport->title   = $model->title;
            $smsSuport->content = $model->content;
            $smsSuport->status  = SmsSupport::STATUS_ACTIVE;
            $smsSuport->save(false);

            $data = Excel::import($target_file_user, ['setFirstRecordAsKeys' => false, 'setIndexSheetByName' => true, 'getOnlySheet' => 'Sheet1']);
            if (count($data) <= 0) {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Thông tin không hợp lệ, danh sách gửi không đúng format -  xin vui lòng nhập lại'));
                return $this->render('send-email-internal', [
                    'model' => $model,
                ]);
            }
//            echo "<pre>";print_r($data);die();
            if ($data[1]['A'] != 'Username' || $data[1]['B'] != 'Email') {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Thông tin không hợp lệ, danh sách gửi không đúng format -  xin vui lòng nhập lại'));
                return $this->render('send-email-internal', [
                    'model' => $model,
                ]);
            }

            // xuat excel
            $objPHPExcel = new PHPExcel;
            $objWriter   = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
            $objSheet    = $objPHPExcel->getActiveSheet();
            $objSheet->getCell('A1')->setValue(Yii::t("app", "Báo cáo trạng thái gửi Email nội bộ"));

            $objSheet->getCell('A2')->setValue(Yii::t("app", "Tên tài khoản"));
            $objSheet->getCell('B2')->setValue(Yii::t("app", "Địa chỉ email"));
            $objSheet->getCell('C2')->setValue(Yii::t("app", "Trạng thái"));
            $rowOrderError = 3;
            $b             = 0;
            foreach ($data as $item) {
                if ($b != 0) {
                    $report   = [];
                    $username = $item['A'];
                    $to_mail  = $item['B'];
                    if (!empty($username)) {
                        $check = Subscriber::find()
                            ->andWhere(['username' => $username])
                            ->andWhere(['status' => User::STATUS_ACTIVE])
                            ->one();
                        if (empty($check)) {
                            $report[Subscriber::EXCEL_ROW3] = Yii::t("app", "Thất bại! Tên tài khoản $username không tồn tại.");
                        } else {
                            if (in_array($username, $userArr)) {
                                $report[Subscriber::EXCEL_ROW3] = Yii::t("app", "Tên tài khoản bị trùng");
                            } else {
                                // lưu kết nối sms với user
                                $smsUserAsm                 = new SmsUserAsm();
                                $smsUserAsm->sms_support_id = $smsSuport->id;
                                $smsUserAsm->user_id        = $check->id;
                                $smsUserAsm->is_read        = SmsUserAsm::NOT_READ;
                                $smsUserAsm->status         = SmsUserAsm::STATUS_ACTIVE;
                                $smsUserAsm->date_send      = time();
                                $smsUserAsm->save(false);
                                $report[Subscriber::EXCEL_ROW3] = Yii::t("app", "Thành công!");
                                $userArr[]                      = $username;
                            }
                        }
                    } else {
                        $report[Subscriber::EXCEL_ROW3] = Yii::t("app", "Thất bại! Tên tài khoản trống");
                    }
                    $report[Subscriber::EXCEL_ROW1] = $username;
                    $report[Subscriber::EXCEL_ROW2] = $to_mail;

                    $errorsArr[0] = $report;
                    foreach ($errorsArr as $order => $errors) {
                        $objSheet->getCell($this->getCell(Subscriber::EXCEL_ROW1, $rowOrderError))->setValue($order);
                        foreach ($errors as $attr => $error) {
                            $objSheet->getCell($this->getCell($attr, $rowOrderError))->setValue($error);
                        }
                    }
                    $rowOrderError++;
                }
                $b++;
            }
            $objSheet->getColumnDimension('A')->setAutoSize(true);
            $objSheet->getColumnDimension('B')->setAutoSize(true);
            $objSheet->getColumnDimension('C')->setAutoSize(true);

            $error_file_name = basename($file_name) . '_err.' . $file_user->extension;

            $objWriter->save(Yii::getAlias('@file_customer') . "/" . $error_file_name);
            $smsSuportUpdate           = SmsSupport::findOne($smsSuport->id);
            $smsSuportUpdate->file_log = $error_file_name;
            $smsSuportUpdate->update(false);
            Yii::$app->session->setFlash('success', Yii::t('app', 'Thực hiện gửi mail hoàn tất. Để xem chi tiết trạng thái gửi mail từng khách hàng, vui lòng xem trong file log chi tiết.'));
            return $this->redirect(['send-email-internal']);
        }
        return $this->render('send-email-internal', [
            'model' => $model,
        ]);
    }

    public function actionEmailInternal()
    {
        $param          = Yii::$app->request->queryParams;
        $model          = new SendEmailInternalForm();
        $internalSearch = new SmsSupportSearch();
        if (isset($param['SendEmailInternalForm']['from_date']) && isset($param['SendEmailInternalForm']['to_date'])) {
            $started  = strtotime(DateTime::createFromFormat("d/m/Y", $param['SendEmailInternalForm']['from_date'])->setTime(0, 0)->format('Y-m-d H:i:s'));
            $finished = strtotime(DateTime::createFromFormat("d/m/Y", $param['SendEmailInternalForm']['to_date'])->setTime(23, 59, 59)->format('Y-m-d H:i:s'));
            if ($finished < $started) {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Ngày kết thúc tìm kiếm không được nhỏ hơn ngày bắt đầu tìm kiếm'));
            }
            $model->from_date = $param['SendEmailInternalForm']['from_date'];
            $model->to_date   = $param['SendEmailInternalForm']['to_date'];
        }
        $param['SendEmailInternalForm']['type'] = SmsSupport::TYPE_CSKH_INTERNAL;
        $dataProviderInternal                   = $internalSearch->search($param);
        return $this->render('index-internal', [
            'model'        => $model,
            'searchModel'  => $internalSearch,
            'dataProvider' => $dataProviderInternal,
        ]);
    }

    private function getCell($attr, $rowIdx)
    {
        switch ($attr) {
            case Subscriber::EXCEL_ROW1:
                return "A$rowIdx";
            case Subscriber::EXCEL_ROW2:
                return "B$rowIdx";
            case Subscriber::EXCEL_ROW3:
                return "C$rowIdx";
        }
        return '';
    }

    public function actionEmailExternal()
    {
        $param       = Yii::$app->request->queryParams;
        $model       = new SendEmailInternalForm();
        $searchModel = new SmsSupportSearch();

        if (isset($param['SendEmailInternalForm']['from_date']) && isset($param['SendEmailInternalForm']['to_date'])) {
            $started  = strtotime(DateTime::createFromFormat("d/m/Y", $param['SendEmailInternalForm']['from_date'])->setTime(0, 0)->format('Y-m-d H:i:s'));
            $finished = strtotime(DateTime::createFromFormat("d/m/Y", $param['SendEmailInternalForm']['to_date'])->setTime(23, 59, 59)->format('Y-m-d H:i:s'));
            if ($finished < $started) {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Ngày kết thúc tìm kiếm không được nhỏ hơn ngày bắt đầu tìm kiếm'));
            }
            $model->from_date = $param['SendEmailInternalForm']['from_date'];
            $model->to_date   = $param['SendEmailInternalForm']['to_date'];
        }
        $param['SendEmailInternalForm']['type'] = SmsSupport::TYPE_CSKH_EXTERNAL;
        $dataProvider                           = $searchModel->search($param);
        return $this->render('index-external', [
            'model'        => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionSendEmailExternal()
    {
        $model       = new SendEmailInternalForm();
        $ATTACH_FILE = "";
        if ($model->load(Yii::$app->request->post())) {
            //Xử lí với file ảnh, nếu có đính kèm ảnh thì mới gán attach
            $image = UploadedFile::getInstance($model, 'image');
            if ($image) {
                $file_name    = uniqid() . time() . '.' . $image->extension;
                $target_image = Yii::getAlias('@webroot') . DIRECTORY_SEPARATOR . Yii::getAlias('@file_customer') . DIRECTORY_SEPARATOR . $file_name;
                if ($image->saveAs($target_image)) {
                    $model->image = $file_name;
                    $ATTACH_FILE  = $target_image;
                }
            }
            // Xử lí với file_user
            $file_user = UploadedFile::getInstance($model, 'file_user');
            if (!$file_user) {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Thông tin không hợp lệ, danh sách gửi không được bỏ trống -  xin vui lòng nhập lại'));
                return $this->render('send-email-external', [
                    'model' => $model,
                ]);
            }

            $file_name        = uniqid() . time() . '.' . $file_user->extension;
            $target_file_user = Yii::getAlias('@webroot') . DIRECTORY_SEPARATOR . Yii::getAlias('@file_customer') . DIRECTORY_SEPARATOR . $file_name;
            if ($file_user->saveAs($target_file_user)) {
                $model->file_user = $file_name;
            }

            $data = Excel::import($target_file_user, ['setFirstRecordAsKeys' => false, 'setIndexSheetByName' => true, 'getOnlySheet' => 'Sheet1']);
            if (count($data) <= 0) {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Thông tin không hợp lệ, danh sách gửi không đúng format -  xin vui lòng nhập lại'));
                return $this->render('send-email-external', [
                    'model' => $model,
                ]);
            }
            //set filename
            $FILE_LOG_NAME = date('Ymd_His', time()) . '.' . $file_user->extension;
            // check format
            if ($data[1]["A"] != "Username" || $data[1]["B"] != "Email") {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Thông tin không hợp lệ, danh sách gửi không đúng format -  xin vui lòng nhập lại'));
                return $this->render('send-email-external', [
                    'model' => $model,
                ]);
            }
            // Remove first row
            array_shift($data);
            // xuat excel
            $objPHPExcel = new PHPExcel();
            $objWriter   = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
            $objSheet    = $objPHPExcel->getActiveSheet();
            $objSheet->getCell('A1')->setValue(Yii::t("app", "Báo cáo trạng thái gửi Email CSKH"));
            $objSheet->getCell('A2')->setValue(Yii::t("app", "Tên tài khoản"));
            $objSheet->getCell('B2')->setValue(Yii::t("app", "Địa chỉ email"));
            $objSheet->getCell('C2')->setValue(Yii::t("app", "Trạng thái"));
            $rowOrderError = 3;
            foreach ($data as $item) {
                $report = [];
                //sendMail
                $username = $item['A'];
                $to_mail  = $item['B'];
                if (!filter_var($to_mail, FILTER_VALIDATE_EMAIL)) {
                    // không phải format email
                    $report[Subscriber::EXCEL_ROW3] = Yii::t("app", "Thất bại! Email không đúng định dạng email@domain.extension");
                    //add value to excel
                    $report[Subscriber::EXCEL_ROW1] = $username;
                    $report[Subscriber::EXCEL_ROW2] = $to_mail;
                    $errorsArr[0]                   = $report;
                    foreach ($errorsArr as $order => $errors) {
                        $objSheet->getCell($this->getCell(Subscriber::EXCEL_ROW1, $rowOrderError))->setValue($order);
                        foreach ($errors as $attr => $error) {
                            $objSheet->getCell($this->getCell($attr, $rowOrderError))->setValue($error);
                        }
                    }
                    $rowOrderError++;
                    continue;
                }
                $sendMail = Subscriber::sendMail($to_mail, $model->title, $model->content, $ATTACH_FILE);
                //saveLog
                if ($sendMail) {
                    $report[Subscriber::EXCEL_ROW3] = Yii::t("app", "Thành công!");
                } else {
                    $report[Subscriber::EXCEL_ROW3] = Yii::t("app", "Thất bại! Lỗi webmail");
                }
                //add value to excel
                $report[Subscriber::EXCEL_ROW1] = $username;
                $report[Subscriber::EXCEL_ROW2] = $to_mail;
                $errorsArr[0]                   = $report;
                foreach ($errorsArr as $order => $errors) {
                    $objSheet->getCell($this->getCell(Subscriber::EXCEL_ROW1, $rowOrderError))->setValue($order);
                    foreach ($errors as $attr => $error) {
                        $objSheet->getCell($this->getCell($attr, $rowOrderError))->setValue($error);
                    }
                }
                $rowOrderError++;
            }
            //save DB
            SmsSupport::createSmsSupport($model->title, $model->content, $model->image, $model->file_user, $FILE_LOG_NAME);
            //save file log
            $objSheet->getColumnDimension('A')->setAutoSize(true);
            $objSheet->getColumnDimension('B')->setAutoSize(true);
            $objSheet->getColumnDimension('C')->setAutoSize(true);

            $objWriter->save(Yii::getAlias('@file_customer') . "/" . $FILE_LOG_NAME);

            Yii::$app->session->setFlash('success', Yii::t('app', 'Thực hiện gửi mail hoàn tất. Để xem chi tiết trạng thái gửi mail từng khách hàng, vui lòng xem trong file log chi tiết.'));
            return $this->redirect(['send-email-external']);

        }

        return $this->render('send-email-external', [
            'model' => $model,
        ]);
    }

    public function actionViewEmailExternal($id)
    {
        $model = SmsSupport::findOne($id);
        return $this->render('view_email_external', [
            'model' => $model,
        ]
        );
    }

    public function actionViewEmailInternal($id)
    {
        $model = SmsSupport::findOne($id);
        return $this->render('view_email_internal', [
            'model' => $model,
        ]
        );
    }

//    public function actionUploadFile()
    //    {
    //        Yii::$app->response->format = Response::FORMAT_JSON;
    //
    //        $files = null;
    ////        $type = Yii::$app->request->post('type');
    //        if(!isset($_FILES['file_upload'])){
    //            return ['success' => false];
    //        }
    //        $files = $_FILES['file_upload'];
    //        $size = $files['size'];
    //
    //        $ext = explode('.', basename($files['name']));
    //        $file_name = uniqid() . time() . '.' . array_pop($ext);
    //
    //        $target = Yii::getAlias('@webroot') . DIRECTORY_SEPARATOR . Yii::getAlias('@file_customer') . DIRECTORY_SEPARATOR . $file_name;
    //
    //        if (move_uploaded_file($files['tmp_name'], $target)) {
    //            $success = true;
    //        }else {
    //            $success = false;
    //        }
    //        Yii::info("##### cuongvm: source:".$files['tmp_name']);
    //        Yii::info("##### cuongvm: des:".$target);
    //
    //        $output = ['success' => $success, 'file_name' => $file_name, 'target'=>$target, 'size' =>$size];
    //
    //        return $output;
    //    }
    //
    //
    //

    /**
     * [actionWhitelist description]
     * @return any
     */
    public function actionWhitelist()
    {
        $params = Yii::$app->request->post();
        if (Yii::$app->request->isPost) {
            if (isset($params['whitelist'])) {
                \Yii::$app->db->createCommand("UPDATE subscriber SET whitelist=:not_whitelist WHERE whitelist=:is_whitelist")
                    ->bindValue(':is_whitelist', Subscriber::IS_WHITELIST)
                    ->bindValue(':not_whitelist', Subscriber::NOT_WHITELIST)
                    ->execute();

                foreach ($params['whitelist'] as $id) {
                    $subscriber            = Subscriber::findOne($id);
                    $subscriber->whitelist = Subscriber::IS_WHITELIST;
                    $subscriber->update();
                }

                Yii::$app->getSession()->setFlash('success', Yii::t("app", "Cập nhật whitelist thành công!"));
            } else {
                \Yii::$app->db->createCommand("UPDATE subscriber SET whitelist=:not_whitelist WHERE whitelist=:is_whitelist")
                    ->bindValue(':is_whitelist', Subscriber::IS_WHITELIST)
                    ->bindValue(':not_whitelist', Subscriber::NOT_WHITELIST)
                    ->execute();
            }
        }

        $whitelist = Subscriber::find()
            ->where(['status' => Subscriber::STATUS_ACTIVE])
            ->andWhere(['whitelist' => Subscriber::IS_WHITELIST])
            ->andWhere(['site_id' => $this->sp_user->site_id])
            ->asArray()
            ->all();

        return $this->render('whitelist', [
            'whitelist' => json_encode($whitelist),
        ]);
    }

    public function actionFindSubscriber($q)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out                         = ['results' => ['id' => '', 'display_name' => '']];

        if (!is_null($q)) {
            $data = Subscriber::find()
                ->andFilterWhere(['LIKE', 'username', $q])
                ->andFilterWhere([
                    'status'  => Subscriber::STATUS_ACTIVE,
                    'site_id' => $this->sp_user->site_id,
                ])->asArray()->all();

            $out['results'] = $data;
        }

        return $out;
    }

    public function actionResetDevice()
    {

        $searchModel = new SubscriberDeviceAsm();
        $params      = Yii::$app->request->queryParams;
        $searchModel->load($params);

        $query = $searchModel->find();
        $query->innerJoin('subscriber', 'subscriber.id = subscriber_device_asm.subscriber_id');
        $query->innerJoin('device', 'device.id = subscriber_device_asm.device_id');

        $query->andFilterWhere(['like', 'subscriber.username', $searchModel->subscriber_id]);
        $query->andFilterWhere(['like', 'device.device_id', $searchModel->device_id]);
        if ($searchModel->created_at) {
            $query->andFilterWhere(['>=', 'subscriber_device_asm.created_at', strtotime($searchModel->created_at)]);
            $query->andFilterWhere(['<=', 'subscriber_device_asm.created_at', strtotime($searchModel->created_at) + 86400]);
        }

        $query->andFilterWhere(['=', 'subscriber.site_id', $this->sp_user->site_id]);
        $query->andFilterWhere(['=', 'subscriber_device_asm.status', SubscriberDeviceAsm::STATUS_ACTIVE]);

        $query->groupBy('subscriber.id');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('reset-device', [
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
        ]);
    }

    public function actionRenewSub($sub_id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $params                      = Yii::$app->request->post();

        $sub = Subscriber::findOne($sub_id);

        $sub->username    = $params['subscriber_id'];
        $sub->authen_type = Subscriber::AUTHEN_TYPE_ACCOUNT;

        if ($sub->save(false)) {
            return ['output' => '', 'message' => ''];
        }

    }


    /**
     * Lists all SubscriberToken models.
     * @return mixed
     */
    public function actionSession()
    {
        $param = Yii::$app->request->queryParams;
        $searchModel = new SubscriberTokenSearch();
        $param['SubscriberTokenSearch']['site_id'] = $this->sp_user->site_id;
        $dataProvider = $searchModel->search($param);

        return $this->render('session', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'site_id'=> $this->sp_user->site_id,
        ]);
    }

    public function actionDeleteSession($id)
    {
        $model = SubscriberToken::findOne($id);
        $model->status = SubscriberToken::STATUS_DELETE;

        if (!$model->save()) {
            Yii::error($model->errors);
            Yii::$app->getSession()->setFlash('error', Yii::t("app", "Xóa thất bại! Vui lòng thử lại."));
        } else {
            Yii::$app->getSession()->setFlash('success', Yii::t("app", "Xóa thành công!"));
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Lists all SubscriberToken models.
     * @return mixed
     */
    public function actionListSession($subscriber_id,$username)
    {
        $param = Yii::$app->request->queryParams;
        $searchModel = new SubscriberTokenSearch();
        $param['SubscriberTokenSearch']['subscriber_id'] = $subscriber_id;
        $dataProvider = $searchModel->searchDetail($param);

        return $this->render('listsession', [
            'dataProvider' => $dataProvider,
            'username'=>$username
        ]);
    }

    public function actionSwapDevice($sub_id)
    {
        $model = SubscriberDeviceAsm::findOne(['subscriber_id' => $sub_id]);
        $model->setScenario('swapDevice');
        if (Yii::$app->request->post()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $params = Yii::$app->request->post()['SubscriberDeviceAsm'];
                $oldMachineId = $model->device_id; // id thiết bị MAC cũ
                $newMachineName = $params['new_device_id']; // địa chỉ MAC mới
                $model->new_device_id = $newMachineName;

                $device = Device::findByMac($newMachineName,$this->sp_user->site_id,Device::STATUS_ACTIVE); // thiết bị MAC mới
                if(!$device){ // kiểm tra có thiết bị MAC mới trên hệ thống không
                    Yii::$app->getSession()->setFlash('error', Yii::t("app", "Không tồn tại địa chỉ MAC mới trên hệ thống. Vui lòng thử lại."));
                    return $this->render('swap-device',  ['model' => $model]);
                }
                if($device->id == $oldMachineId){ // kiểm trả MAC mới có trùng MAC cũ không
                    Yii::$app->getSession()->setFlash('error', Yii::t("app", 'Địa chỉ MAC mới không được trùng Địa chỉ MAC cũ'));
                    return $this->render('swap-device', [ 'model' => $model,]);
                }

                $subscriber_new = Subscriber::findOne(['machine_name'=>$newMachineName,'site_id'=>$this->sp_user->site_id,'is_active'=>Subscriber::IS_ACTIVE,'status'=>Subscriber::STATUS_ACTIVE]); // thuê bao chứa MAC mới
                if($subscriber_new){ // kiểm tra MAC mới có đang gắn với thuê bao đã kích hoạt
                    Yii::$app->getSession()->setFlash('error', Yii::t("app", 'Địa chỉ MAC của thiết bị đang được sử dụng.'));
                    return $this->render('swap-device', [ 'model' => $model]);
                }

                $newMachineId = $device->id; // id thiết bị MAC mới
                $subscriber_device = SubscriberDeviceAsm::findOne(['subscriber_id' => $newMachineId]); // liên kết giữa thuê bao với thiết bị mới
                if($subscriber_device){ // kiểm tra xem có tồn tại liên kết với thiết bị mới không
                    $subscriber_device->status = SubscriberDeviceAsm::STATUS_REMOVED; // xóa liên kết thiết bị mới
                    if(!$subscriber_device->save()){
                        Yii::$app->getSession()->setFlash('error', Yii::t("app", "Không xóa được liên kết thiết bị của thiết bị mới. Vui lòng thử lại."));
                        return $this->render('swap-device', [ 'model' => $model]);
                    }
                }

                $model->device_id = $newMachineId; // tạo liên kết giữa thuê bao với thiết bị mới
                if (!$model->save(false)) {
                    $transaction->rollBack();
                    Yii::$app->getSession()->setFlash('error', Yii::t("app", "Không tạo được liên kết giữa thuê bao với thiết bị mới. Vui lòng thử lại."));
                    return $this->render('swap-device', [ 'model' => $model]);
                }

                $subscriber_swap = Subscriber::find() // thuê bao gắn với thiết bị mac mới
                    ->where(['machine_name'=>$newMachineName])
                    ->all();
                if($subscriber_swap){
                    /** @var Subscriber $item */
                    foreach($subscriber_swap as $item){// chuyển hết trạng thái các thuê bao chứa MAC mới thành xóa
                        $item->username = null;
                        $item->machine_name = null;
                        $item->status = Subscriber::STATUS_DELETED;
                        if(!$item->save()){
                            $transaction->rollBack();
                            Yii::$app->getSession()->setFlash('error', Yii::t("app", "Không xóa được thuê bao có địa chỉ MAC mới được swap ."));
                            return $this->render('swap-device', [ 'model' => $model]);
                        }else{
                            SubscriberToken::deleteAll(['subscriber_id'=>$item->id]); // xóa token cua thuê bao gắn với MAC mới
                        }
                    }
                }

                $subscriber = Subscriber::findOne($sub_id);
                $subscriber->username = $newMachineName;
                $subscriber->machine_name = $newMachineName;

                if (!$subscriber->save(false)) {// chuyển username + machinne_name của thuê bảo thành thiết bị mới
                    $transaction->rollBack();
                    Yii::$app->getSession()->setFlash('error', Yii::t("app", "Không đổi được username + machine_name trong subscriber.Vui lòng thử lại."));
                    return $this->render('swap-device', [ 'model' => $model]);
                }

                if (!empty($oldMachineId)) { // lưu log swap thiết bị
                    $log = new LogSubscriberSwap();
                    $log->subscriber_id = $sub_id;
                    $log->device_id_old = $oldMachineId;
                    $log->device_id_new = $newMachineId;
                    $log->number_change = $log->number_change == 0 ? 1 : $log->number_change++;
                    $log->description = $params['description'];
                    $log->status = LogSubscriberSwap::STATUS_ACTIVE;
                    $log->actor_id = $this->sp_user->id;
                    if ($log->save()) {
                        SubscriberToken::deleteAll(['subscriber_id'=>$sub_id]); // xóa token thuê bao cũ
                        $transaction->commit();
                        Yii::$app->getSession()->setFlash('success', Yii::t("app", "Thay đổi thông tin gán thiết bị thành công!"));
                        return $this->redirect(['subscriber/view', 'id' => $sub_id]);
                    } else {
                        $transaction->rollBack();
                        Yii::$app->getSession()->setFlash('error', Yii::t("app", "Không ghi được log device swap" . json_encode($log->getErrors()).'. Vui lòng thử lại.'));
                        return $this->render('swap-device', [ 'model' => $model]);
                    }
                }
            } catch (Exception $e) {
                Yii::error($e);
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', Yii::t('app', 'Thay đổi thông tin gán thiết bị không thành công. Vui lòng thử lại.'));
                return $this->render('swap-device', [ 'model' => $model]);
            }
        }
        return $this->render('swap-device', [ 'model' => $model]);
    }

    public function actionFindDevice($q,$id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out                         = ['results' => ['id' => '', 'display_name' => '']];

        if (!is_null($q)) {
            $data = Device::find() ->innerJoin('subscriber','subscriber.machine_name = device.device_id')
                ->where(['subscriber.is_active'=>null])
                ->orWhere(['subscriber.is_active'=>0])
                ->andWhere(['device.site_id'=> $this->sp_user->site_id])
                ->andWhere([ 'device.status'=> Device::STATUS_ACTIVE])
                ->andWhere(['<>','device.id',$id])
                ->andWhere(['LIKE', 'device.device_id', $q])
                ->limit(10)->asArray()->all();

            $out['results'] = $data;
        }

        return $out;
    }

    public  function actionViewLogSwapDevice(){
        $param       = Yii::$app->request->queryParams;
        $searchModel = new LogSubscriberSwapSearch();

        $site_id = $this->sp_user->site_id;
        $dataProvider                         = $searchModel->search($param,$site_id);

        return $this->render('log-device', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionViewTransaction($id){
        $model = SubscriberTransaction::findOne($id);
        return $this->render('view-transaction',['model'=>$model]);
    }
}
