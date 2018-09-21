<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "agency".
 *
 * @property int $id
 * @property string $name
 *
 * @property User[] $users
 */
class VedjustAgency extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'agency';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['agency_id' => 'id']);
    }
}
