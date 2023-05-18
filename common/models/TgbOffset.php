<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tgb_offset".
 *
 * @property int $id
 * @property int|null $update_id
 */
class TgbOffset extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tgb_offset';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('dbTgBot');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['update_id'], 'default', 'value' => null],
            [['update_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'update_id' => 'update_id',
        ];
    }
}
