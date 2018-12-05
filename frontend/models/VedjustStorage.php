<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "storage".
 *
 * @property int $id
 * @property string $comment
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
            [['ved_id', 'archive_id'], 'required'],
            [['ved_id', 'archive_id'], 'integer'],
            [['comment'], 'string'],
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
            'ved_id' => 'Ведомость',
            'archive_id' => 'Хранилище',
            'comment' => 'Размещение',
        ];
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
