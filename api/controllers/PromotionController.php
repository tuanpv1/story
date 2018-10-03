<?php

namespace api\controllers;

use common\helpers\Encrypt;
use common\models\Dealer;
use common\models\Partner;
use common\models\PromotionCode;
use Yii;

/**
 * Created by PhpStorm.
 * User: TuanPham
 * Date: 3/8/2017
 * Time: 5:15 PM
 */
class PromotionController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        return $behaviors;
    }

    public function actionCheck()
    {
        $receiver = Yii::$app->request->post('receiver'); // Người dùng có thể là sdt, username, ....
        $receiver_info = Yii::$app->request->post('receiver_info'); // Chuỗi json thông tin người dùng
        $promotion_code = Yii::$app->request->post('promotion_code'); // Đẩy lên dạng đã được mã hóa
        $signature = Yii::$app->request->post('signature'); // Chữ kí
        $partner_id = Yii::$app->request->post('partner_id'); // Mã đối tác không phải CP
        $transaction_partner_id = Yii::$app->request->post('transaction_id'); // Mã giao dịch để đối soát

        PromotionCode::validateApi($receiver, $promotion_code, $signature, $receiver_info, $transaction_partner_id, $partner_id);

        $partner = Partner::findOne(['id' => $partner_id, 'status' => Partner::STATUS_ACTIVE]);
        if (!$partner) {
            return ['success' => false, 'error_code' => PromotionCode::ERROR_MISS_PARTNER_ID];
        }

        // Lấy key mã hóa theo partner
        $key = $partner->secret_key;
        $code_encrypt = Encrypt::encryptCode($promotion_code, $key);
        Yii::info('ma khuyen ma ma hoa dung: ' . $code_encrypt);
        $code_decrypt = strtolower(Encrypt::decryptCode($promotion_code, $key));
        Yii::info('ma khuyen ma giai ma: ' . $code_decrypt);
        // Xác thực thông tin
        $check_signature = md5($receiver . $code_decrypt . $partner_id . $transaction_partner_id);
        Yii::info('Ma md5 dung: ' . $check_signature);
        if ($check_signature !== $signature) {
            return ['success' => false, 'error_code' => PromotionCode::ERROR_INVALID_SIGNATURE];
        }

        // Kiểm tra mã code
        return PromotionCode::checkPromotionCode($receiver, $receiver_info, $partner_id, $transaction_partner_id, $code_decrypt);
    }

    public function actionUsePromotionCode()
    {
        $receiver = Yii::$app->request->post('receiver'); // Người dùng có thể là sdt, username, ....
        $receiver_info = Yii::$app->request->post('receiver_info'); // Chuỗi json thông tin người dùng
        $promotion_code = Yii::$app->request->post('promotion_code'); // Đẩy lên dạng đã được mã hóa
        $signature = Yii::$app->request->post('signature'); // Chữ kí
        $partner_id = Yii::$app->request->post('partner_id'); // Mã đối tác không phải CP
        $transaction_partner_id = Yii::$app->request->post('transaction_id'); // Mã giao dịch để đối soát

        PromotionCode::validateApi($receiver, $promotion_code, $signature, $receiver_info, $transaction_partner_id, $partner_id);

        $partner = Partner::findOne(['id' => $partner_id, 'status' => Partner::STATUS_ACTIVE]);
        if (!$partner) {
            return ['success' => false, 'error_code' => PromotionCode::ERROR_MISS_TRANSACTION_ID];
        }

        // Lấy key mã hóa theo partner
        $key = $partner->secret_key;
        $code_decrypt = strtolower(Encrypt::decryptCode($promotion_code, $key));
        Yii::info('ma khuyen ma giai ma: ' . $code_decrypt);
        // Xác thực thông tin
        $check_signature = md5($receiver . $code_decrypt . $partner_id . $transaction_partner_id);
        Yii::info('Ma md5 dung: ' . $check_signature);
        if ($check_signature !== $signature) {
            return ['success' => false, 'error_code' => PromotionCode::ERROR_INVALID_SIGNATURE];
        }

        // Thêm thông tin phone vào promotion_code
        return PromotionCode::addPhoneToCode($receiver, $receiver_info, $partner_id, $transaction_partner_id, $code_decrypt);
    }

    public function actionRollBack()
    {
        $receiver = Yii::$app->request->post('receiver'); // Người dùng có thể là sdt, username, ....
        $receiver_info = Yii::$app->request->post('receiver_info'); // Chuỗi json thông tin người dùng
        $promotion_code = Yii::$app->request->post('promotion_code'); // Đẩy lên dạng đã được mã hóa
        $signature = Yii::$app->request->post('signature'); // Chữ kí
        $partner_id = Yii::$app->request->post('partner_id'); // Mã đối tác không phải CP
        $transaction_partner_id = Yii::$app->request->post('transaction_id'); // Mã giao dịch để đối soát

        PromotionCode::validateApi($receiver, $promotion_code, $signature, $receiver_info, $transaction_partner_id, $partner_id);

        $partner = Partner::findOne(['id' => $partner_id, 'status' => Partner::STATUS_ACTIVE]);
        if (!$partner) {
            return ['success' => false, 'error_code' => PromotionCode::ERROR_MISS_TRANSACTION_ID];
        }

        // Lấy key mã hóa theo partner
        $key = $partner->secret_key;
        $code_decrypt = strtolower(Encrypt::decryptCode($promotion_code, $key));
        // Xác thực thông tin
        $check_signature = md5($receiver . $code_decrypt . $partner_id . $transaction_partner_id);
        if ($check_signature !== $signature) {
            return ['success' => false, 'error_code' => PromotionCode::ERROR_INVALID_SIGNATURE];
        }

        return PromotionCode::rollBack($receiver, $receiver_info, $partner_id, $transaction_partner_id, $code_decrypt);
    }

    public function actionGetListCp()
    {
        $data = Dealer::find()
            ->select('name_code,full_name')
            ->asArray()
            ->all();
        return [
            'success' => true,
            'data' => $data
        ];

    }

}