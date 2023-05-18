<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "reference_book".
 *
 * @property int $id
 * @property string $question Вопрос
 * @property string $answer Ответ 
 * @property string|null $keywords Ключевые слова
 */
class ReferenceBook extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reference_book';
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
            [['question', 'answer'], 'required'],
            [['question', 'keywords'], 'string', 'max' => 512],
            [['answer'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'question' => 'Question',
            'answer' => 'Answer',
            'keywords' => 'Keywords',
        ];
    }
}
