<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\rbac\Item;

/**
 * This is the model class for table "auth_item".
 *
 * @property string $name
 * @property int $type
 * @property string $description
 * @property string $rule_name
 * @property string $data
 * @property int $created_at
 * @property int $updated_at
 * @property int $acc_type
 *
 * @property AuthAssignment[] $authAssignments
 * @property User[] $users
 * @property AuthRule $ruleName
 * @property AuthItemChild[] $authItemChildren
 * @property AuthItemChild[] $authItemChildren0
 * @property AuthItem[] $children
 * @property AuthItem[] $parents
 */
class AuthItem extends \yii\db\ActiveRecord
{
    const TYPE_ROLE = Item::TYPE_ROLE;
    const TYPE_PERMISSION = Item::TYPE_PERMISSION;

    const ACC_TYPE_BACKEND = 1;
    const ACC_TYPE_CP = 2;

    public static $acc_types = [
        self::ACC_TYPE_BACKEND => '@backend',
        self::ACC_TYPE_CP => '@cp',
    ];

    public static function findPermission()
    {
        return AuthItem::find()->andWhere(['type' => AuthItem::TYPE_PERMISSION]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public static function findRole()
    {
        return AuthItem::find()->andWhere(['type' => AuthItem::TYPE_ROLE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auth_item';
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

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['name', 'filter', 'filter' => 'trim'],
            [['name', 'type',], 'required', 'message' => Yii::t('app', '{attribute} không được để trống, vui lòng nhập lại.')],
            [['type', 'created_at', 'updated_at', 'acc_type'], 'integer'],
            [['data'], 'string'],
            [['rule_name'], 'string', 'max' => 64],
            [['name'], 'unique'],
            [['name'], 'string', 'max' => 12, 'min' => 4, 'on' => 'action-with-role'],
            [['description'], 'string', 'max' => 30, 'min' => 4, 'on' => 'action-with-role'],
            [['rule_name'], 'exist', 'skipOnError' => true, 'targetClass' => AuthRule::className(), 'targetAttribute' => ['rule_name' => 'name']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('app', 'Tên nhóm quyền'),
            'type' => 'Type',
            'description' => Yii::t('app', 'Mô tả'),
            'rule_name' => 'Rule Name',
            'data' => 'Data',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'acc_type' => 'Acc Type',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthAssignments()
    {
        return $this->hasMany(AuthAssignment::className(), ['item_name' => 'name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['id' => 'user_id'])->viaTable('auth_assignment', ['item_name' => 'name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRuleName()
    {
        return $this->hasOne(AuthRule::className(), ['name' => 'rule_name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthItemChildren()
    {
        return $this->hasMany(AuthItemChild::className(), ['parent' => 'name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthItemChildren0()
    {
        return $this->hasMany(AuthItemChild::className(), ['child' => 'name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChildren()
    {
        return $this->hasMany(AuthItem::className(), ['name' => 'child'])->viaTable('auth_item_child', ['parent' => 'name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParents()
    {
        return $this->hasMany(AuthItem::className(), ['name' => 'parent'])->viaTable('auth_item_child', ['child' => 'name']);
    }

    /**
     * @return ActiveDataProvider
     */
    public function getChildrenProvider()
    {
        return new ActiveDataProvider([
            'query' => $this->getChildren()
        ]);
    }

    /**
     * @return ActiveDataProvider
     */
    public function getParentProvider()
    {
        return new ActiveDataProvider([
            'query' => $this->getParent()
        ]);
    }

    public function getParent()
    {
        return $this->hasMany(AuthItem::className(), ['name' => 'parent'])->viaTable('auth_item_child', ['child' => 'name']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMissingRoles()
    {
        return AuthItem::find()->andWhere(['type' => AuthItem::TYPE_ROLE, 'acc_type' => $this->acc_type])
            ->andWhere('name != :name', [':name' => $this->name])
            ->andWhere('name not in (select child from auth_item_child where parent = :name)', [':name' => $this->name])
            ->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMissingPermissions()
    {
        return AuthItem::find()->andWhere(['type' => AuthItem::TYPE_PERMISSION, 'acc_type' => $this->acc_type])
            ->andWhere('name != :name', [':name' => $this->name])
            ->andWhere('name not in (select child from auth_item_child where parent = :name)', [':name' => $this->name])
            ->all();
    }
}
