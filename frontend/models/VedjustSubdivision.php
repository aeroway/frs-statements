<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "subdivision".
 *
 * @property int $id
 * @property string $name
 * @property int $area_id
 * @property int $authority_id
 *
 * @property Area $area
 * @property Authority $authority
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
            [['area_id', 'authority_id'], 'integer'],
            [['area_id'], 'exist', 'skipOnError' => true, 'targetClass' => VedjustArea::className(), 'targetAttribute' => ['area_id' => 'id']],
            [['authority_id'], 'exist', 'skipOnError' => true, 'targetClass' => VedjustAuthority::className(), 'targetAttribute' => ['authority_id' => 'id']],
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
            'area_id' => 'Area ID',
            'authority_id' => 'Authority ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArea()
    {
        return $this->hasOne(VedjustArea::className(), ['id' => 'area_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthority()
    {
        return $this->hasOne(VedjustAuthority::className(), ['id' => 'authority_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['subdivision_id' => 'id']);
    }
}
