<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "archive_unit".
 *
 * @property int $id
 * @property string $name
 *
 * @property Ved[] $veds
 */
class VedjustArchiveUnit extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'archive_unit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'name_rp'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Единица архивного хранения',
            'name_rp' => 'Единица архивного хранения в родительном падеже',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVeds()
    {
        return $this->hasMany(Ved::className(), ['archive_unit_id' => 'id']);
    }
}
