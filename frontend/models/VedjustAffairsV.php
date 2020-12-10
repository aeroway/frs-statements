<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "affairs".
 *
 * @property int $id
 * @property string $comment
 * @property string $kuvd
 * @property string $ref_num
 * @property int $ved_id
 *
 * @property Ved $ved
 */
class VedjustAffairsV extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'v_affairs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kuvd', 'ref_num'], 'string', 'max' => 40],
            [['comment'], 'string'],
            [['ved_id'], 'integer'],
            [['ved_id'], 'exist', 'skipOnError' => true, 'targetClass' => VedjustVed::className(), 'targetAttribute' => ['ved_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'comment' => 'Комментарий',
            'kuvd' => 'КУВД',
            'ref_num' => 'Номер обращения',
            'ved_id' => 'Ведомость',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVed()
    {
        return $this->hasOne(VedjustVed::className(), ['id' => 'ved_id']);
    }
}