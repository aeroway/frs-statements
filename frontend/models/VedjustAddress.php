<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "address".
 *
 * @property int $id
 * @property string $name
 * @property int $subdivision_id
 *
 * @property Subdivision $subdivision
 */
class VedjustAddress extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'address';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'subdivision_id'], 'required'],
            [['subdivision_id'], 'default', 'value' => null],
            [['subdivision_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
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
            'name' => 'Адрес',
            'subdivision_id' => 'Subdivision ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubdivision()
    {
        return $this->hasOne(VedjustSubdivision::className(), ['id' => 'subdivision_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVeds()
    {
        return $this->hasMany(VedjustVed::className(), ['address_id' => 'id']);
    }
}
