<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "subdivision".
 *
 * @property int $id
 * @property string $name
 * @property int $subject_id
 * @property int $agency_id
 *
 * @property Subject $subject
 * @property Agency $agency
 * @property User[] $users
 */
class VedjustSubdivision extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'subdivision';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string'],
            [['subject_id', 'agency_id'], 'integer'],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => VedjustSubject::className(), 'targetAttribute' => ['subject_id' => 'id']],
            [['agency_id'], 'exist', 'skipOnError' => true, 'targetClass' => VedjustAgency::className(), 'targetAttribute' => ['agency_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'subject_id' => 'Subject ID',
            'agency_id' => 'Agency ID',
        ];
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
    public function getAgency()
    {
        return $this->hasOne(VedjustAgency::className(), ['id' => 'agency_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['subdivision_id' => 'id']);
    }
}
