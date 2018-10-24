<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "affairs".
 *
 * @property int $id
 * @property int $status
 * @property string $date_create
 * @property string $date_status
 * @property string $comment
 * @property string $kuvd
 * @property int $ved_id
 * @property int $create_ip
 * @property int $accepted_ip
 * @property int $user_created_id
 * @property int $user_accepted_id
 *
 * @property Ved $ved
 */
class VedjustAffairs extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'affairs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kuvd'], 'required'],
            [['comment', 'kuvd'], 'string'],
            [['date_create', 'date_status'], 'safe'],
            [['ved_id', 'status', 'user_created_id', 'user_accepted_id', 'create_ip', 'accepted_ip'], 'integer'],
            [['ved_id'], 'exist', 'skipOnError' => true, 'targetClass' => VedjustVed::className(), 'targetAttribute' => ['ved_id' => 'id']],
            [['user_accepted_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_accepted_id' => 'id']],
            [['user_created_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_created_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status' => 'Статус',
            'date_create' => 'Дата создания',
            'date_status' => 'Дата подтверждения',
            'comment' => 'Комментарий',
            'kuvd' => 'КУВД',
            'ved_id' => 'Ведомость',
            'user_created_id' => 'Создал',
            'user_accepted_id' => 'Принял',
            'create_ip' => 'IP создания',
            'accepted_ip' => 'IP подтверждения',
        ];
    }

    // location of docs in storage
    public function getStoragePath($id)
    {
        $modelStorage = VedjustStorage::find()
        ->select(['hall', 'rack', 'locker', 'shelf', 'position'])
        ->where(['=', 'ved_id', $id])
        ->asArray()
        ->one();

        return $modelStorage;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVed()
    {
        return $this->hasOne(VedjustVed::className(), ['id' => 'ved_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserAccepted()
    {
        return $this->hasOne(User::className(), ['id' => 'user_accepted_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserCreated()
    {
        return $this->hasOne(User::className(), ['id' => 'user_created_id']);
    }

}
