<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "storage".
 *
 * @property int $id
 * @property int $hall
 * @property int $rack
 * @property int $locker
 * @property int $shelf
 * @property int $position
 * @property int $ved_id
 * @property int $archive_id
 *
 * @property Ved $ved
 * @property Archive $archive
 */
class VedjustStorage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'storage';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ved_id'], 'unique'],
            [['hall', 'rack', 'locker', 'shelf', 'position'], 'unique', 'targetAttribute' => ['hall', 'rack', 'locker', 'shelf', 'position']],
            [['hall', 'rack', 'locker', 'shelf', 'position', 'ved_id', 'archive_id'], 'required'],
            [['hall', 'rack', 'locker', 'shelf', 'position', 'ved_id', 'archive_id'], 'integer'],
            [['ved_id'], 'exist', 'skipOnError' => true, 'targetClass' => VedjustVed::className(), 'targetAttribute' => ['ved_id' => 'id']],
            [['archive_id'], 'exist', 'skipOnError' => true, 'targetClass' => VedjustArchive::className(), 'targetAttribute' => ['archive_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'hall' => 'Зал',
            'rack' => 'Стеллаж',
            'locker' => 'Шкаф',
            'shelf' => 'Полка',
            'position' => 'Позиция',
            'ved_id' => 'Ведомость',
            'archive_id' => 'Хранилище',
        ];
    }

    public function getLastValueArchive($id, $name)
    {
        $out = VedjustStorage::find()->select([$name])->where(['id' => $id])->one();

        if ($out !== null) {
            if ($name === 'position') {
                return $out->$name + 1;
            }

            return $out->$name;
        }

        return 1;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVed()
    {
        return $this->hasOne(VedjustVed::className(), ['id' => 'ved_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArchive()
    {
        return $this->hasOne(VedjustArchive::className(), ['id' => 'archive_id']);
    }
}
