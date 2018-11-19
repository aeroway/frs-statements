<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "archive".
 *
 * @property int $id
 * @property int $hall_max
 * @property int $rack_max
 * @property int $locker_max
 * @property int $shelf_max
 * @property int $position_max
 * @property string $name
 * @property int $user_created_id
 * @property int $agency_id
 * @property int $subject_id
 * @property int $subdivision_id
 *
 * @property Storage[] $storages
 */
class VedjustArchive extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'archive';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'hall_max', 'rack_max', 'locker_max', 'shelf_max', 'position_max'], 'required'],
            [['name'], 'string'],
            [['user_created_id', 'agency_id', 'subject_id', 'subdivision_id', 'hall_max', 'rack_max', 'locker_max', 'shelf_max', 'position_max'], 'integer'],
            [['user_created_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_created_id' => 'id']],
            [['agency_id'], 'exist', 'skipOnError' => true, 'targetClass' => VedjustAgency::className(), 'targetAttribute' => ['agency_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => VedjustSubject::className(), 'targetAttribute' => ['subject_id' => 'id']],
            [['subdivision_id'], 'exist', 'skipOnError' => true, 'targetClass' => VedjustSubdivision::className(), 'targetAttribute' => ['subdivision_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'hall_max' => 'Максимальное количество залов в хранилище',
            'rack_max' => 'Максимальное количество стеллажей в зале',
            'locker_max' => 'Максимальное количество шкафов в стеллаже',
            'shelf_max' => 'Максимальное количество полок в шкафу',
            'position_max' => 'Максимальное количество позиций на полке',
            'name' => 'Название хранилища',
            'user_created_id' => 'Сотрудник',
            'agency_id' => 'Орган',
            'subject_id' => 'Субъект РФ',
            'subdivision_id' => 'Отдел',
        ];
    }

    public function getMaxSizeArchive($id, $name)
    {
        return VedjustArchive::find()->select([$name])->where(['id' => $id])->one()->$name;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStorages()
    {
        return $this->hasMany(VedjustStorage::className(), ['archive_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserCreated()
    {
        return $this->hasOne(User::className(), ['id' => 'user_created_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgency()
    {
        return $this->hasOne(VedjustAgency::className(), ['id' => 'agency_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubject()
    {
        return $this->hasOne(VedjustSubject::className(), ['id' => 'subject_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubdivision()
    {
        return $this->hasOne(VedjustSubdivision::className(), ['id' => 'subdivision_id']);
    }
}
