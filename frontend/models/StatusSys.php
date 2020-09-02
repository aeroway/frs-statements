<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "status_sys".
 *
 * @property int $id
 * @property string $ext_sys_num
 * @property string|null $status
 * @property string|null $date_update
 */
class StatusSys extends \yii\db\ActiveRecord
{
    public $verifyCode;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'status_sys';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db2');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ext_sys_num'], 'required'],
            [['date_update'], 'safe'],
            [['ext_sys_num', 'status'], 'string', 'max' => 256],
            [['ext_sys_num'], 'unique'],
            ['verifyCode', 'captcha'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ext_sys_num' => '№ обращения',
            'status' => 'Статус',
            'date_update' => 'Дата редактирования',
            'verifyCode' => 'Проверочный код',
        ];
    }
}
