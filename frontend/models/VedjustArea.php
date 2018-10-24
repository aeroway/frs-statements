<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "area".
 *
 * @property int $id
 * @property string $name
 * @property string $id_dpt
 *
 * @property Subdivision[] $subdivisions
 */
class VedjustArea extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'area';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'id_dpt'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Отдел',
            'id_dpt' => 'Id Dpt',
        ];
    }

}