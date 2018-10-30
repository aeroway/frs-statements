<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "issuance".
 *
 * @property int $id
 * @property string $date_issue
 * @property int $create_ip
 * @property int $user_created_id
 * @property string $name
 * @property int $affairs_id
 *
 * @property Affairs $affairs
 * @property User $userCreated
 */
class VedjustIssuance extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'issuance';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date_issue', 'create_ip', 'user_created_id', 'name', 'affairs_id'], 'required'],
            [['date_issue'], 'safe'],
            [['create_ip', 'user_created_id', 'affairs_id'], 'default', 'value' => null],
            [['create_ip', 'user_created_id', 'affairs_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['affairs_id'], 'exist', 'skipOnError' => true, 'targetClass' => VedjustAffairs::className(), 'targetAttribute' => ['affairs_id' => 'id']],
            [['user_created_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_created_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date_issue' => 'Дата выдачи',
            'create_ip' => 'IP создавшего запись',
            'user_created_id' => 'Пользователь создавший запись',
            'name' => 'ФИО заявителя',
            'affairs_id' => 'Дело',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAffairs()
    {
        return $this->hasOne(VedjustAffairs::className(), ['id' => 'affairs_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserCreated()
    {
        return $this->hasOne(User::className(), ['id' => 'user_created_id']);
    }
}
