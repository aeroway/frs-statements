<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "authority".
 *
 * @property int $id
 * @property string $name
 *
 * @property Subdivision[] $subdivisions
 */
class VedjustAuthority extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'authority';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string'],
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubdivisions()
    {
        return $this->hasMany(VedjustSubdivision::className(), ['authority_id' => 'id']);
    }
}
