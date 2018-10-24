<?php

namespace common\models;

use api\helpers\APIHelper;
use api\helpers\Message;
use common\charging\helpers\ChargingGW;
use common\charging\models\ChargingConnection;
use common\charging\models\ChargingResult;
use common\helpers\CommonConst;
use common\helpers\CommonUtils;
use common\helpers\CUtils;
use common\helpers\FileUtils;
use common\helpers\ResMessage;
use common\helpers\SysCproviderService;
use common\helpers\VasProvisioning;
use DateInterval;
use DateTime;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Yii;
use yii\base\InvalidCallException;
use yii\base\InvalidValueException;
use yii\behaviors\TimestampBehavior;
use yii\console\Exception;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\IdentityInterface;
use yii\web\ServerErrorHttpException;

/**
 * This is the model class for table "{{%subscriber}}".
 *
 * @property integer $id
 * @property integer $whitelist
 * @property integer $authen_type
 * @property integer $channel
 * @property string $msisdn
 * @property string $username
 * @property string $machine_name
 * @property integer $balance
 * @property integer $status
 * @property string $email
 * @property string $address
 * @property string $city
 * @property string $full_name
 * @property string $auth_key
 * @property string $password_hash
 * @property integer $last_login_at
 * @property integer $last_login_session
 * @property integer $birthday
 * @property integer $sex
 * @property string $avatar_url
 * @property string $skype_id
 * @property string $google_id
 * @property string $facebook_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $register_at
 * @property integer activated_at
 * @property integer $client_type
 * @property integer $using_promotion
 * @property integer $auto_renew
 * @property integer $verification_code
 * @property string $user_agent
 * @property integer $expired_at
 * @property string $otp_code
 * @property string $ip_address
 * @property string $ip_to_location
 * @property string $province_code
 * @property integer $expired_code_time
 * @property integer $number_otp
 * @property integer $is_active
 * @property integer $type
 * @property integer $itvod_type
 * @property string $pass_code
 * @property integer $number_pass_code
 * @property integer $expired_pass_code
 */
class Subscriber extends \yii\db\ActiveRecord implements IdentityInterface
{
    public $access_token;

    const STATUS_ACTIVE = 10;
    const STATUS_INACTIVE = 1;
    const STATUS_MAINTAIN = 2;
    const STATUS_DELETED = 0;

    const SEX_NAM = 0;
    const SEX_NU = 1;

    const CHANNEL_TYPE_API = 1;
    const CHANNEL_TYPE_SYSTEM = 2;
    const CHANNEL_TYPE_CSKH = 3;
    const CHANNEL_TYPE_SMS = 4;
//    const CHANNEL_TYPE_WAP = 5;
    const CHANNEL_TYPE_MOBILEWEB = 6;
    const CHANNEL_TYPE_ANDROID = 7;
    const CHANNEL_TYPE_IOS = 8;
    const CHANNEL_TYPE_WEBSITE = 9;
    const CHANNEL_TYPE_ANDROID_MOBILE = 10;

    const RENEW_AUTO = 1;
    const RENEW_NOT_AUTO = 0;

    const AUTHEN_TYPE_ACCOUNT = 1;
    const AUTHEN_TYPE_MAC_ADDRESS = 2;

    //itvod
    const TYPE_FACEBOOK = 1;
    const TYPE_GOOGLE = 2;
    const TYPE_EMAIL = 3;

    const TYPE_OTP = 1;
    const TYPE_NOT_OTP = 2;

    const IS_ACTIVE = 1;
    const IS_NOT_ACTIVE = 0;

    /*
     * @var string password for register scenario
     */
    public $password;
    public $confirm_password;
    public $new_password;
    public $old_password;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%subscriber}}';
    }

    public static function getDb()
    {
        return Yii::$app->db;
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['site_id', 'authen_type', 'password_hash'], 'required'], // Bỏ required với msisdn
            [['username'], 'required', 'on' => 'create'], // cuongvm 20170523 Bỏ required với username khi create bằng api bởi machine_name
            [['username'], 'unique'],
            [['username', 'msisdn'], 'validateUnique', 'on' => 'create'], //** Enable cái này nếu cần thiết => $model->setScenario('create'); */
            [
                [
                    'site_id',
                    'dealer_id',
                    'authen_type',
                    'channel',
                    'status',
                    'last_login_at',
                    'last_login_session',
                    'birthday',
                    'sex',
                    'created_at',
                    'updated_at',
                    'client_type',
                    'using_promotion',
                    'auto_renew',
                    'expired_at',
                    'number_otp',
                    'whitelist',
                    'expired_code_time',
                    'register_at',
                    'is_active',
                    'type',
                    'itvod_type',
                    'number_pass_code',
                    'expired_pass_code'
                ],
                'integer',
            ],
//            [
//                'username',
//                'match', 'pattern' => '/^[\*a-zA-Z0-9]{1,20}$/',
//                'message' => Yii::t('app', 'Thông tin không hợp lệ, tên tài khoản - Tối đa 20 ký tự (bao gồm chữ cái và số) không bao gồm ký tự đặc biệt '),
//                'on' => 'create'
//            ],
            [['msisdn', 'ip_address', 'pass_code'], 'string', 'max' => 45],
            [['verification_code', 'otp_code', 'auth_key'], 'string', 'max' => 32],
            [['username', 'machine_name', 'email'], 'string', 'max' => 100],
            [['full_name', 'password'], 'string', 'max' => 200],
            [['password_hash', 'address', 'city'], 'string', 'max' => 255],
            [['avatar_url', 'skype_id', 'google_id', 'facebook_id', 'province_code', 'ip_to_location'], 'string', 'max' => 255],
            [['user_agent'], 'string', 'max' => 512],
            ['password', 'string', 'min' => 8, 'tooShort' => Yii::t('app', 'Mật khẩu không hợp lệ. Mật khẩu ít nhất 8 ký tự')],
            ['confirm_password', 'string', 'min' => 8, 'tooShort' => Yii::t('app', 'Xác nhận mật khẩu không hợp lệ, ít nhất 8 ký tự')],
            ['new_password', 'string', 'min' => 8, 'tooShort' => Yii::t('app', 'Mật khẩu không hợp lệ, ít nhất 8 ký tự')],
            [['confirm_password', 'password', 'dealer_id'], 'required', 'on' => 'create'],
            [
                ['confirm_password'],
                'compare',
                'compareAttribute' => 'password',
                'message' => Yii::t('app', 'Xác nhận mật khẩu không đúng.'),
                'on' => 'create',
            ],
            [
                ['confirm_password'],
                'compare',
                'compareAttribute' => 'new_password',
                'message' => Yii::t('app', 'Xác nhận mật khẩu chưa đúng.'),
                'on' => 'change-password',
            ],
            [['new_password'], 'required', 'on' => 'change-password'],
            [['old_password', 'new_password', 'confirm_password'], 'required', 'on' => 'change-password'],
            [['email'], 'email', 'message' => Yii::t('app', 'Email không đúng định dạng')],
            [['balance'], 'integer', 'min' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'site_id' => Yii::t('app', 'Nhà cung cấp'),
            'dealer_id' => Yii::t('app', 'Đại lý'),
            'authen_type' => Yii::t('app', 'Loại xác thực'),
            'channel' => Yii::t('app', 'Kênh đăng ký'),
            'msisdn' => Yii::t('app', 'Số điện thoại'),
            'username' => Yii::t('app', 'Tên tài khoản'),
            'machine_name' => Yii::t('app', 'Địa chỉ Mac'),
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'status' => Yii::t('app', 'Trạng thái'),
            'email' => Yii::t('app', 'Email'),
            'full_name' => Yii::t('app', 'Họ và tên'),
            'password' => Yii::t('app', 'Mật khẩu'),
            'confirm_password' => Yii::t('app', 'Mật khẩu xác nhận'),
            'last_login_at' => Yii::t('app', 'Last Login At'),
            'last_login_session' => Yii::t('app', 'Last Login Session'),
            'birthday' => Yii::t('app', 'Ngày tháng năm sinh'),
            'sex' => Yii::t('app', 'Giới tính'),
            'avatar_url' => Yii::t('app', 'Avatar Url'),
            'skype_id' => Yii::t('app', 'Skype ID'),
            'google_id' => Yii::t('app', 'Google ID'),
            'facebook_id' => Yii::t('app', 'Facebook ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'client_type' => Yii::t('app', 'Client Type'),
            'using_promotion' => Yii::t('app', 'Using Promotion'),
            'auto_renew' => Yii::t('app', 'Auto Renew'),
            'verification_code' => Yii::t('app', 'Verification Code'),
            'user_agent' => Yii::t('app', 'User Agent'),
            'balance' => Yii::t('app', 'Tài khoản ví'),
            'address' => Yii::t('app', 'Địa chỉ'),
            'city' => Yii::t('app', 'Tỉnh/ Thành phố'),
            'whitelist' => Yii::t('app', 'whitelist'),
            'otp_code' => Yii::t('app', 'Opt Code'),
            'ip_address' => Yii::t('app', 'Địa chỉ IP')
        ];
    }

    public function validateUnique($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $subscriber = Subscriber::findOne(['username' => $this->username, 'status' => [Subscriber::STATUS_ACTIVE, Subscriber::STATUS_INACTIVE]]);
            if ($subscriber) {
                $this->addError($attribute, Yii::t('app', 'Tên tài khoản đã tồn tại. Vui lòng chọn tên khác!'));
            }
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContentViewLogs()
    {
        return $this->hasMany(ContentViewLog::className(), ['subscriber_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriberActivities()
    {
        return $this->hasMany(SubscriberActivity::className(), ['subscriber_id' => 'id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriberFavorites()
    {
        return $this->hasMany(SubscriberFavorite::className(), ['subscriber_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriberFeedbacks()
    {
        return $this->hasMany(SubscriberFeedback::className(), ['subscriber_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriberTokens()
    {
        return $this->hasMany(SubscriberToken::className(), ['subscriber_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriberTransactions()
    {
        return $this->hasMany(SubscriberTransaction::className(), ['subscriber_id' => 'id']);
    }
    
    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * @param $username
     * @param $site_id
     * @param bool|true $status
     * @return null|static
     */
    public static function findByUsername($username, $site_id, $status = true)
    {
        if (!$status) {
            return Subscriber::findOne(['username' => $username, 'site_id' => $site_id]);
        }
        return Subscriber::findOne(['username' => $username, 'site_id' => $site_id, 'status' => Subscriber::STATUS_ACTIVE]);
    }

    /**
     * @param $password
     * @return bool
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * @param $username
     * @param $password
     * @param $authen_type
     * @param null $mac_address
     * @return array
     */
    public static function register($username, $password, $msisdn, $city = null, $status = Subscriber::STATUS_ACTIVE, $authen_type, $site_id, $channel = Subscriber::CHANNEL_TYPE_ANDROID, $mac_address = null, $address = '', $email = '', $fullname = '')
    {
        $res = [];
        /** Chuyển sang chữ thường */
        $username = strtolower($username);
        $mac_address = strtolower($mac_address);

        $subscriber = new Subscriber();
        $subscriber->username = $username;
        $subscriber->machine_name = ($authen_type == Subscriber::AUTHEN_TYPE_MAC_ADDRESS) ? $mac_address : null;
        $subscriber->status = $status;
        $subscriber->msisdn = $msisdn;
        $subscriber->city = $city;
        $subscriber->email = $email;
        $subscriber->address = $address;
        $subscriber->full_name = $fullname;
        $subscriber->channel = (int)$channel;
        $subscriber->authen_type = $authen_type;
        $subscriber->password = ($authen_type == Subscriber::AUTHEN_TYPE_MAC_ADDRESS) ? CUtils::randomString(8) : $password;
        if ($authen_type == Subscriber::AUTHEN_TYPE_ACCOUNT) {
            $subscriber->register_at = time();
        }
        $subscriber->setPassword($password);
        $subscriber->generateAuthKey();
        /** Validate và save, nếu có lỗi thì return message_error */
        if (!$subscriber->validate()) {
            $message = $subscriber->getFirstMessageError();
            $res['status'] = false;
            $res['message'] = $message;
            return $res;
        }
        if (!$subscriber->save()) {
            $res['status'] = false;
            $res['message'] = Message::getFailMessage();
            return $res;
        }

//        $item = $subscriber->getAttributes(['id', 'username','full_name', 'msisdn', 'status', 'site_id', 'created_at', 'updated_at'], ['password_hash', 'authen_type']);
        $res['status'] = true;
        $res['message'] = Message::getRegisterSuccessMessage();
        $res['subscriber'] = $subscriber;
        return $res;
    }

    private function getFirstMessageError()
    {
        $error = $this->firstErrors;
        $message = "";
        foreach ($error as $key => $value) {
            $message .= $value;
            break;
        }
        return $message;
    }

    /**
     * Finds an identity by the given ID.
     * @param string|integer $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        // TODO: Implement findIdentity() method.
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        /* @var SubscriberToken $subscriber_token */
        /* @var Subscriber $subscriber */
        $subscriber_token = SubscriberToken::findByAccessToken($token);

        if ($subscriber_token) {
            $subscriber = $subscriber_token->getSubscriber()->one();
            if ($subscriber) {
                $subscriber->access_token = $token;
            }

            return $subscriber;
        }

        return null;
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|integer an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        // TODO: Implement getId() method.
        return $this->getPrimaryKey();
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        // TODO: Implement getAuthKey() method.
        return $this->auth_key;
    }

    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return boolean whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        // TODO: Implement validateAuthKey() method.
        return $this->getAuthKey() === $authKey;
    }

    /**
     * @return array
     */
    public static function listStatus()
    {
        $lst = [
            self::STATUS_ACTIVE => Yii::t('app', 'Hoạt động'),
            self::STATUS_INACTIVE => Yii::t('app', 'Tạm khóa'),
            self::STATUS_DELETED => \Yii::t('app', 'Đã xóa'),
            self::STATUS_MAINTAIN => \Yii::t('app', 'Bảo hành'),
        ];
        return $lst;
    }

    public static function listCity($site_id)
    {
        $city = [];
        $listCity = City::find()->all();
        foreach ($listCity as $item) {
            /** @var $item City */
            $city[$item->name] = $item->name;
        }
        return $city;
    }

    /**
     * @return int
     */
    public function getStatusName()
    {
        $lst = self::listStatus();
        if (array_key_exists($this->status, $lst)) {
            return $lst[$this->status];
        }
        return $this->status;
    }

    /**
     * @return array
     */
    public static function listSex()
    {
        $lst = [
            self::SEX_NAM => Yii::t('app', 'Nam'),
            self::SEX_NU => Yii::t('app', 'Nữ'),
        ];
        return $lst;
    }

    /**
     * @return int
     */
    public function getSexName()
    {
        $lst = self::listSex();
        if (array_key_exists($this->sex, $lst)) {
            return $lst[$this->sex];
        }
        return $this->sex;
    }

    public function getDisplayName()
    {
        if ($this->full_name != null && $this->full_name != '') {
            return $this->full_name;
        }

        return $this->username;
    }

    /**
     * @param $msisdn
     * @param $site_id
     * @param bool $create
     * @return Subscriber|null|static
     */
    public static function findByMsisdn($msisdn, $site_id, $create = false)
    {
//        $msisdn = CUtils::validateMobile($msisdn);
        $subscriber = Subscriber::findOne([
            'msisdn' => $msisdn,
            'status' => self::STATUS_ACTIVE,
            'site_id' => $site_id,
        ]);
        if (!$create) {
            return $subscriber;
        } else {
            if ($subscriber) {
                return $subscriber;
            } else {
                $subscriber = new Subscriber();

                $subscriber->msisdn = $msisdn;
                $subscriber->username = $msisdn;
                $subscriber->site_id = $site_id;
                $subscriber->status = Subscriber::STATUS_ACTIVE;

                if ($subscriber->save()) {
                    return $subscriber;
                } else {
                    Yii::trace($subscriber->errors);
                }
            }
            return null;
        }
    }

    /**
     * @param $transType
     * @param $channelType
     * @param $description
     * @param null $service
     * @param null $content
     * @param int $status
     * @param int $cost
     * @param string $currency
     * @param int $balance
     * @param null $service_provider
     * @param string $error_code
     * @return SubscriberTransaction
     */
    public function newTransaction(
        $transType,
        $channelType,
        $description,
        $service = null,
        $content = null,
        $status = SubscriberTransaction::STATUS_FAIL,
        $cost = 0,
        $currency = 'VND',
        $balance = 0,
//        $service_provider = null,
        $error_code = '',
        $card_code = null,
        $card_serial = null,
        $voucher_id = null,
        $order_id = null,
        $balance_before_charge = 0
    )
    {
        $tr = new SubscriberTransaction();
        $tr->subscriber_id = $this->id;
        $tr->msisdn = $this->msisdn;
        $tr->type = $transType;
        $tr->channel = $channelType;
        $tr->description = $description;
        $tr->order_id = $order_id;

        /** @var $content Content */
        if ($content) {
            $tr->content_id = $content->id;
        }
//        if ($service_provider) {
//            $tr->site_id = $service_provider->id;
//        }
        $tr->created_at = time();
        $tr->status = $status;
        $tr->transaction_time = time();
        $tr->error_code = $error_code;
        if ($tr->save()) {
            return $tr;
        } else {
            Yii::error($tr->getErrors());
            return null;
        }

    }

    /**
     * @param $id
     * @param $site_id
     * @throws BadRequestHttpException
     * @throws InternalErrorException
     * @throws ServerErrorHttpException
     * @throws \yii\db\Exception
     */
    public
    function favorite($id, $site_id)
    {
        $subscriber_favorite = SubscriberFavorite::findOne([
            'subscriber_id' => $this->id,
            'content_id' => $id,
            'site_id' => $site_id,
        ]);

        if (!$subscriber_favorite) {
            /* @var Content $content */
            $content = Content::findOne(['id' => $id, 'site_id' => $site_id]);
            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            try {
                if ($content) {
                    $subscriber_favorite = new SubscriberFavorite();
                    $subscriber_favorite->content_id = $id;
                    $subscriber_favorite->subscriber_id = $this->id;
                    $subscriber_favorite->site_id = $site_id;
                    $subscriber_favorite->created_at = time();
                    $subscriber_favorite->updated_at = time();
                    if ($subscriber_favorite->save()) {
                        $content->favorite_count++;
                        if ($content->save()) {
                            $transaction->commit();
                            return Message::getFavoriteSuccessMessage();
                        }
                    }
                } else {
                    throw new InternalErrorException(Message::getActionFailMessage());
                }
            } catch (Exception $e) {
                $transaction->rollback();
                throw new ServerErrorHttpException(Message::getActionFailMessage());
            }
        } else {
            throw new BadRequestHttpException(Message::getFavoriteExitsMessage());
        }
    }

    public
    function unfavorite($id, $site_id)
    {
        $subscriber_favorite = SubscriberFavorite::findOne([
            'subscriber_id' => $this->id,
            'content_id' => $id,
            'site_id' => $site_id,
        ]);

        if ($subscriber_favorite) {
            /* @var Content $content */
            $content = Content::findOne(['id' => $id, 'site_id' => $site_id]);
            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            try {
                if ($content) {
                    if ($subscriber_favorite->delete()) {
                        $content->favorite_count--;
                        if ($content->save()) {
                            $transaction->commit();
                            return Message::getUnFavoriteSuccessMessage();
                        }
                    }
                } else {
                    throw new InternalErrorException(Message::getActionFailMessage());
                }
            } catch (Exception $e) {
                $transaction->rollback();
                throw new ServerErrorHttpException(Message::getActionFailMessage());
            }
        } else {
            throw new BadRequestHttpException(Message::getUnFavoriteExitsMessage());
        }
    }

    public
    function favorites($site_id)
    {
        $query = \api\models\SubscriberFavorite::find()
            ->andWhere(['site_id' => $site_id])
            ->andWhere(['subscriber_id' => $this->id]);

        $provider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'updated_at' => SORT_DESC,
                ],
            ],
            'pagination' => [
                'defaultPageSize' => 10,
            ],
        ]);
        return $provider;
    }

    public
    function comment($title, $content_comment, $content_id, $site_id)
    {
        if ($content_comment == "") {
            throw new BadRequestHttpException(Message::getNoCommentMessage());
        }
        /* @var Content $content */
        $content = Content::findOne(['id' => $content_id]);
        if ($content) {
            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            try {
                $comment = new \common\models\SubscriberFeedback();
                $comment->content = $content_comment;
                $comment->title = $title;
                $comment->create_date = time();
                $comment->subscriber_id = $this->id;
                $comment->site_id = $site_id;
                $comment->content_id = $content_id;
                $comment->status = SubscriberFeedback::STATUS_ACTIVE;
                if ($comment->save()) {
                    $content->comment_count++;
                    if ($content->save()) {
                        $transaction->commit();
                        return Message::getSuccessMessage();
                    }
                }
            } catch (Exception $e) {
                $transaction->rollback();
                throw new ServerErrorHttpException(Message::getActionFailMessage());
            }

        } else {
            throw new InternalErrorException(Message::getActionFailMessage());
        }

    }

    public
    function comments($site_id, $content_id)
    {
        $query = \api\models\SubscriberFeedback::find()
            ->andWhere(['site_id' => $site_id])
            ->andWhere(['content_id' => $content_id]);

        $provider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'create_date' => SORT_DESC,
                ],
            ],
            'pagination' => [
                'defaultPageSize' => 10,
            ],
        ]);
        return $provider;
    }

    public
    static function getMsisdn()
    {
        $headers = [];
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
        } elseif (function_exists('http_get_request_headers')) {
            $headers = http_get_request_headers();
        } else {
            foreach ($_SERVER as $name => $value) {
                if (strncmp($name, 'HTTP_', 5) === 0) {
                    $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                    $headers[$name] = $value;
                }
            }
        }
        $lcHeaders = [];
        foreach ($headers as $name => $value) {
            $lcHeaders[strtolower($name)] = $value;
        }

        $headers = $lcHeaders;
        $clientIp = $_SERVER['REMOTE_ADDR'];
        $msisdn = isset($headers['msisdn']) ? $headers['msisdn'] : "";
        $xIpAddress = isset($headers['x-ipaddress']) ? $headers['x-ipaddress'] : "";
        $xForwardedFor = isset($headers['x-forwarded-for']) ? $headers['x-forwarded-for'] : "";
        $userIp = isset($headers['user-ip']) ? $headers['user-ip'] : "";
        $xWapMsisdn = isset($headers['x-wap-msisdn']) ? $headers['x-wap-msisdn'] : "";

//        $clientIp = "113.186.0.123";
        /*if ($ip_validation) {
        $valid = preg_match('/10\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $clientIp);
        $valid |= preg_match('/113\.185\.\d{1,3}\.\d{1,3}/', $clientIp);
        $valid |= preg_match('/172\.16\.30\.\d{1,3}/', $clientIp);
        if (!$valid) {
        echo "IP invalid";
        return "";
        }
        else {
        echo "IP valid";
        }
        }*/

        if ($msisdn) {
            return $msisdn;
        }

        if ($xWapMsisdn) {
            return $xWapMsisdn;
        }

        return "";
    }

    public
    static function getSubscriberInfo($msisdn)
    {

        $arr = array();
        $i = 0;
        try {
            if (empty($msisdn) || $msisdn == '' || !is_integer($msisdn)) {
                return ['message' => \Yii::t('app', 'Khong ton tai nguoi dung')];
            } else {
                $query = Subscriber::find()
                    ->select('*')
                    ->from('subscriber')
                    ->andWhere(['msisdn' => $msisdn])
                    ->asArray()
                    ->all();
                foreach ($query as $val) {
                    return $val;
                }
            }

        } catch (\yii\db\Exception $ex) {
            return false;
        }
    }

    public
    function getListTransactions($from, $to, $page_size = 10, $page_index = 1)
    {
        $offset = ($page_index - 1) * $page_size;
        if ($offset < 0) {
            $offset = 0;
        }
        $total_pages = 0;
        $total = SubscriberTransaction::find()->andWhere(['>', 'created_at', $from])
            ->andWhere(['<', 'created_at', $to])
            ->andWhere(['subscriber_id' => $this->id, 'site_id' => $this->site_id, 'status' => SubscriberTransaction::STATUS_SUCCESS])
            ->andWhere(['is not', 'service_id', null])
            ->all();
        $total_pages = intval(count($total) / $page_size);
        $transactions = SubscriberTransaction::find()->andWhere(['>', 'created_at', $from])
            ->andWhere(['<', 'created_at', $to])
            ->andWhere(['subscriber_id' => $this->id, 'site_id' => $this->site_id, 'status' => SubscriberTransaction::STATUS_SUCCESS])
            ->andWhere(['is not', 'service_id', null])
            ->orderBy('id desc')
            ->limit($page_size)
            ->offset($offset)->all();
        return [
            'total_pages' => $total_pages,
            'transactions' => $transactions,
        ];
    }

    /**
     * @param $token
     * @return null|static
     */
    public
    static function findCredentialByToken($token)
    {
        return self::findOne(['token' => $token, 'status' => static::STATUS_ACTIVE]);
    }

    /**
     * @param $action
     * @param $channelType
     * @param $description
     * @param null $service Service
     * @param null $content Content
     * @param $status
     * @param int $cost
     * @param string $telco_code
     *
     * @return SubscriberTransaction
     */
    public
    function newActivity(
        $action,
        $channelType,
        $description,
        $status = Sub::STATUS_FAIL,
        $service_provider = null
    )
    {
        $tr = new SubscriberActivity();
        $tr->subscriber_id = $this->id;
        $tr->site_id = $this->site_id;
        $tr->msisdn = $this->msisdn;
        $tr->action = $action;
        $tr->channel = $channelType;
        $tr->description = $description;

        if ($service_provider) {
            $tr->site_id = $service_provider->id;
        }
        $tr->created_at = time();
        $tr->status = $status;
        $tr->created_at = time();
        $tr->save(false);
        return $tr;
    }

    public
    static function sendMail($to_mail, $subject, $content, $file_attach)
    {
        try {
            $mailer = Yii::$app->mailer;
            $mail = $mailer->compose()
                ->setFrom($mailer->transport->getUsername())
                ->setTo($to_mail)
                ->setSubject($subject)
                ->setHtmlBody($content);
            if (!empty($file_attach)) {
                $mail->attach($file_attach);
            }
            return $mail->send();
        } catch (\Exception $e) {
            Yii::error($e);
        }

    }

// hàm tạo mã hóa
    public
    static function phoneEncrypt($input, $key_seed)
    {
        $input = trim($input);
        $block = mcrypt_get_block_size('tripledes', 'ecb');
        $len = strlen($input);
        $padding = $block - ($len % $block);
        $input .= str_repeat(chr($padding), $padding);

        // generate a 24 byte key from the md5 of the seed
        $key = substr(md5($key_seed), 0, 24);
        $iv_size = mcrypt_get_iv_size(MCRYPT_TRIPLEDES, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        // encrypt
        $encrypted_data = mcrypt_encrypt(MCRYPT_TRIPLEDES, $key, $input,
            MCRYPT_MODE_ECB, $iv);
        // clean up output and return base64 encoded
        return base64_encode($encrypted_data);
    }

    public
    static function phoneDecrypt($input, $key_seed)
    {
        $input = base64_decode($input);
        $key = substr(md5($key_seed), 0, 24);
        $text = mcrypt_decrypt(MCRYPT_TRIPLEDES, $key, $input, MCRYPT_MODE_ECB, '12345678');

        $block = mcrypt_get_block_size('tripledes', 'ecb');
        $packing = ord($text{strlen($text) - 1});
        if ($packing and ($packing < $block)) {
            for ($P = strlen($text) - 1; $P >= strlen($text) - $packing; $P--) {
                if (ord($text{$P}) != $packing) {
                    $packing = 0;
                }
            }
        }
        $text = substr($text, 0, strlen($text) - $packing);
        return $text;
    }

    public static function getMAC($subscriber_id)
    {
        $channel = Subscriber::findOne($subscriber_id);
        if ($channel) {
            return $channel->machine_name;
        } else {
            return '';
        }
    }

    public static function addNewSubscriber($mac_address, $site_id, $channel)
    {
        /** @var Subscriber $subscriber */
        $subscriber = Subscriber::find()
            ->andWhere(['username' => $mac_address])
            ->andWhere(['site_id' => $site_id])
            ->andWhere(['status' => Subscriber::STATUS_ACTIVE])
            ->orderBy(['authen_type' => SORT_ASC, 'status' => SORT_DESC])
            ->one();
        if (!$subscriber) {
            $subscriber = new Subscriber();
            $subscriber->site_id = $site_id;
            $subscriber->channel = $channel;
            $subscriber->username = $mac_address;
            $subscriber->machine_name = $mac_address;
            $subscriber->authen_type = Subscriber::AUTHEN_TYPE_ACCOUNT;
            $subscriber->status = Subscriber::STATUS_ACTIVE;
            $subscriber->register_at = time();
            $subscriber->save(false);
            return $subscriber;
        }
        return null;
    }

    public static function registerNew($username, $password = "123456", $msisdn, $city = null, $status = Subscriber::STATUS_ACTIVE, $authen_type, $site_id, $channel = Subscriber::CHANNEL_TYPE_ANDROID, $mac_address = null, $address = '', $email = '', $fullname = '')
    {
        $res = [];
        /** Chuyển sang chữ thường */
        $username = strtolower($username);
        $mac_address = strtolower($mac_address);

        $subscriber = new Subscriber();
        $subscriber->username = $username;
        $subscriber->machine_name = $mac_address;
        $subscriber->status = $status;
        $subscriber->msisdn = $msisdn;
        $subscriber->city = $city;
        $subscriber->site_id = $site_id;
        $subscriber->email = $email;
        $subscriber->address = $address;
        $subscriber->full_name = $fullname;
        $subscriber->channel = (int)$channel;
        $subscriber->is_active = Subscriber::IS_NOT_ACTIVE;
        $subscriber->authen_type = $authen_type;
        $subscriber->password = ($authen_type == Subscriber::AUTHEN_TYPE_MAC_ADDRESS) ? CUtils::randomString(8) : CUtils::randomString(8);
        if ($authen_type == Subscriber::AUTHEN_TYPE_ACCOUNT) {
            $subscriber->register_at = time();
        }

        if (in_array(Yii::$app->request->getUserIP(), Yii::$app->params['factory_ip'])) {
            $subscriber->type = Subscriber::TYPE_NSX;
        } else {
            $subscriber->type = Subscriber::TYPE_USER;
        }

        $subscriber->setPassword($password);
        $subscriber->generateAuthKey();
        /** Validate và save, nếu có lỗi thì return message_error */
        if (!$subscriber->validate()) {
            $message = $subscriber->getFirstMessageError();
            $res['status'] = false;
            $res['message'] = $message;
            return $res;
        }
        if (!$subscriber->save()) {
            $res['status'] = false;
            $res['message'] = Message::getFailMessage();
            return $res;
        }
        /** TODO tạo bảng quan hệ Subscriber với Device mỗi khi tạo account */
        if ($mac_address) {
            /** @var  $device Device */
            $device = Device::findByMac($mac_address, $site_id);
            if ($device) {
                // if($authen_type == Subscriber::AUTHEN_TYPE_MAC_ADDRESS){
                /** MAC first_login, last_login **/
                $device->first_login = time();
//                    $device->last_login = time();
                $device->save();
                // }

//                SubscriberDeviceAsm::createSubscriberDeviceAsm($subscriber->id, $device->id);
            }
        }
    }

    public static function createSubscriber($channel = Subscriber::CHANNEL_TYPE_ANDROID, $email = '', $name = '', $id = '', $site_id, $type = Subscriber::TYPE_EMAIL, $password = '')
    {
        $subscriber = new Subscriber();
        $subscriber->site_id = $site_id;
        if ($type == Subscriber::TYPE_EMAIL) {
            $subscriber->itvod_type = Subscriber::TYPE_EMAIL;
        } else if ($type == Subscriber::TYPE_FACEBOOK) {
            $subscriber->itvod_type = Subscriber::TYPE_FACEBOOK;
        } else {
            $subscriber->itvod_type = Subscriber::TYPE_GOOGLE;
        }
        $subscriber->channel = $channel;
        if ($email) {
            $subscriber->username = $email;
        } else {
            $subscriber->username = $id;
        }
        $subscriber->authen_type = Subscriber::AUTHEN_TYPE_ACCOUNT;
        $subscriber->status = Subscriber::STATUS_ACTIVE;
        $subscriber->email = $email;
        $subscriber->full_name = $name;
        if ($type == Subscriber::TYPE_FACEBOOK) {
            $subscriber->facebook_id = $id;
            $password = CUtils::randomString(8);
        } elseif ($type == Subscriber::TYPE_GOOGLE) {
            $subscriber->google_id = $id;
            $password = CUtils::randomString(8);
        }
        $subscriber->register_at = time();
        $subscriber->setPassword($password);
        $subscriber->generateAuthKey();
        $subscriber->created_at = time();
        $subscriber->updated_at = time();
        $subscriber->auto_renew = 1;
        if ($subscriber->save()) {
            return $subscriber;
        } else {
            Yii::trace($subscriber->errors);
        }
        return false;
    }

    public function setStatusCode($code)
    {
        Yii::$app->response->setStatusCode($code);
    }

    public function getProvinceName()
    {
        $city = City::findOne(['code' => $this->province_code]);
        $lang = Yii::$app->language;


        if ($city) {
            if ($lang == "vi") {
                return $city->name;
            } else {
                return $city->ascii_name;
            }
        }

        return "";

    }

	public static function updateIP($subscriber_id, $ip_address)
    {
        $subscriber = Subscriber::findOne($subscriber_id);
        if ($subscriber->ip_address != $ip_address) {
            $subscriber->ip_address = $ip_address;
            if ($subscriber->save()) return true;
        }
        return false;
    }
}
