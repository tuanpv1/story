<?php

namespace common\models;

use app\models\ContentCategoryAsm;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Url;

/**
 * This is the model class for table "content".
 *
 * @property int $id
 * @property string $display_name
 * @property string $ascii_name
 * @property string $image
 * @property string $author
 * @property string $short_description
 * @property string $description
 * @property int $total_like
 * @property int $total_view
 * @property int $approved_at
 * @property int $created_at
 * @property int $updated_at
 * @property string $tags
 * @property int $created_user_id
 * @property int $is_series
 * @property int $status
 * @property int $episode_order
 * @property int $total_episode
 * @property int $parent_id
 * @property string $admin_note
 * @property string $country
 * @property int $rating
 * @property int $rating_count
 * @property int $total_dislike
 * @property int $total_favorite
 * @property int $type
 * @property int $honor
 * @property string $code
 * @property string $language
 *
 * @property SubscriberFavorite[] $subscriberFavorites
 * @property SubscriberTransaction[] $subscriberTransactions
 */
class Content extends \yii\db\ActiveRecord
{
    public $list_cat_id;

    const STATUS_ACTIVE = 10; // Đã duyệt
    const STATUS_INACTIVE = 0; // khóa
    const STATUS_REJECTED = 1; // Từ chối
    const STATUS_DELETE = 2; // Xóa
    const STATUS_PENDING = 3; // CHỜ DUYỆT
    const STATUS_INVISIBLE = 4; // ẨN
    const STATUS_DRAFT = 5; // Nháp

    const Type_NOTHING = 0;
    const Type_FEATURED = 1;
    const Type_HOT = 2;
    const Type_ESPECIAL = 3;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'content';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description'], 'string'],
            [['approved_at', 'created_at', 'updated_at', 'created_user_id', 'is_series', 'status', 'episode_order', 'parent_id', 'type', 'honor'], 'integer'],
            [['display_name', 'ascii_name', 'image', 'author', 'country'], 'string', 'max' => 255],
            [['short_description'], 'string', 'max' => 2000],
            [['tags'], 'string', 'max' => 500],
            [['admin_note'], 'string', 'max' => 4000],
            [['code'], 'string', 'max' => 20],
            [['language'], 'string', 'max' => 10],
            [['display_name', 'author', 'created_user_id', 'list_cat_id', 'short_description', 'description'], 'required'],
            [['image'], 'required','on' => 'createContent'],
            [['total_view', 'total_like', 'rating', 'rating_count', 'total_dislike', 'total_favorite', 'total_episode'], 'integer'],
            [['total_view', 'total_like', 'rating', 'rating_count', 'total_dislike', 'total_favorite', 'total_episode'], 'default', 'value' => 0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'display_name' => Yii::t('app', 'Tên'),
            'ascii_name' => 'Ascii Name',
            'image' => Yii::t('app', 'Hình ảnh'),
            'author' => 'Author',
            'short_description' => Yii::t('app', 'Mô tả'),
            'description' => Yii::t('app', 'Nội dung'),
            'total_like' => Yii::t('app', 'Total Like'),
            'total_view' => Yii::t('app', 'Total View'),
            'list_cat_id' => Yii::t('app', 'Danh mục'),
            'approved_at' => 'Approved At',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'tags' => 'Tags',
            'created_user_id' => Yii::t('app', 'Người tạo'),
            'is_series' => Yii::t('app', 'Thể loại bộ'),
            'status' => Yii::t('app', 'Trạng thái'),
            'episode_order' => Yii::t('app', 'Thứ tự tập'),
            'total_episode' => Yii::t('app', 'Tổng số tập'),
            'parent_id' => 'Parent ID',
            'admin_note' => 'Admin Note',
            'country' => 'Country',
            'rating' => 'Rating',
            'rating_count' => 'Rating Count',
            'total_dislike' => 'Total Dislike',
            'total_favorite' => 'Total Favorite',
            'type' => 'Type',
            'honor' => 'Honor',
            'code' => 'Code',
            'language' => 'Language',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriberFavorites()
    {
        return $this->hasMany(SubscriberFavorite::className(), ['content_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriberTransactions()
    {
        return $this->hasMany(SubscriberTransaction::className(), ['content_id' => 'id']);
    }

    public static function getListStatus($type = 'all')
    {
        return [
            'all' => [
                self::STATUS_ACTIVE => Yii::t('app', 'Đã duyệt'),
                self::STATUS_INVISIBLE => Yii::t('app', 'Ẩn'),
                self::STATUS_DRAFT => Yii::t('app', 'Nháp'),
                self::STATUS_DELETE => Yii::t('app', 'Xóa'),
                self::STATUS_INACTIVE => Yii::t('app', 'Khóa'),
                self::STATUS_PENDING => Yii::t('app', 'Chờ duyệt'),
                self::STATUS_REJECTED => Yii::t('app', 'Từ chối'),
            ],
            'filter' => [
                self::STATUS_ACTIVE => Yii::t('app', 'Đã duyệt'),
                self::STATUS_INVISIBLE => Yii::t('app', 'Ẩn'),
                self::STATUS_DRAFT => Yii::t('app', 'Nháp'),
            ],
        ][$type];
    }

    public static function getListType()
    {
        $lst = [
            self::Type_NOTHING => Yii::t('app', 'All'),
            self::Type_FEATURED => Yii::t('app', 'Đặc sắc'),
            self::Type_HOT => Yii::t('app', 'Hot'),
            self::Type_ESPECIAL => Yii::t('app', 'Đặc biệt'),
        ];
        return $lst;
    }

    /**
     * @return int
     */
    public function getTypeName()
    {
        $lst = self::getListType();
        if (array_key_exists($this->type, $lst)) {
            return $lst[$this->type];
        }
        return $this->type;
    }

    public function getImageLink()
    {
        return $this->image ? Url::to(Yii::getAlias('@web') . DIRECTORY_SEPARATOR . Yii::getAlias('@content_images') . DIRECTORY_SEPARATOR . $this->image, true) : '';
    }

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

    public function updateEpisodeOrder()
    {
        // update thứ tự tập cho tập hiện tại
        $maxOrder = Content::find()->andWhere(['parent_id' => $this->parent_id])->orderBy(['episode_order' => SORT_DESC])->one();
        if (!$maxOrder) {
            $maxOrder = 1;
        } else {
            $maxOrder = $maxOrder->episode_order + 1;
        }
        $this->episode_order = $maxOrder;
        $this->update(false);

        if ($this->status == Content::STATUS_ACTIVE) {
            // Update tống số tập
            $modelParent = Content::findOne($this->parent_id);
            $countEpisode = Content::find()
                ->andWhere(['parent_id' => $this->parent_id])
                ->andWhere(['status' => Content::STATUS_ACTIVE])
                ->count();
            $modelParent->total_episode = $countEpisode;
            $modelParent->update(false);
        }
    }

    public function getParentName()
    {
        $model = Content::findOne($this->parent_id);
        if($model){
            return $model->display_name;
        }
        return '';
    }

    public function getUserCreated()
    {
        $model = User::findOne($this->created_user_id);
        if($model){
            return $model->username;
        }
        return '';
    }

    public function getCat()
    {
        $cca = ContentCategoryAsm::findOne(['content_id' => $this->id]);
        if($cca){
            $this->list_cat_id = $cca->category_id;
        }else{
            $this->list_cat_id = '';
        }
    }
}
