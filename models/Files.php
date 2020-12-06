<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "files".
 *
 * @property int $id
 * @property int $username
 * @property string $url
 * @property string $title
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $username0
 */
class Files extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'files';
    }

    /**
     * Get files by User id
     * {@inheritdoc}
     */
    public static function findFilesByUserId($id)
    {
        return static::findAll(['username' => $id]);
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'url', 'title'], 'required'],
            [['username'], 'integer'],
            [['url'], 'string', 'max' => 1000],
            [['title'], 'string', 'max' => 250],
            [['username'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['username' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'url' => 'Url',
            'title' => 'Название',
            'created_at' => 'Загружен',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Username0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsername0()
    {
        return $this->hasOne(User::className(), ['id' => 'username']);
    }
}
