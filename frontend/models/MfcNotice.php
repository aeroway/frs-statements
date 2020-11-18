<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "mfc_notice".
 *
 * @property int $id
 * @property string $ref_num Номер обращения
 * @property string|null $package_num Номер пакета
 * @property string|null $kuvd Номер КУВД
 * @property string|null $applicants Заявители
 * @property string|null $date_create Дата создания
 * @property int|null $user_created_id Создал дело
 * @property int|null $create_ip IP создавшего
 * @property int|null $send Статус СМС
 */
class MfcNotice extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mfc_notice';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ref_num'], 'required'],
            [['date_create'], 'safe'],
            [['user_created_id', 'create_ip', 'send'], 'default', 'value' => null],
            [['user_created_id', 'create_ip', 'send'], 'integer'],
            [['ref_num'], 'string', 'max' => 255],
            [['package_num'], 'string', 'max' => 50],
            [['kuvd', 'applicants'], 'string', 'max' => 4000],
            [['ref_num'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ref_num' => 'Ref Num',
            'package_num' => 'Package Num',
            'kuvd' => 'Kuvd',
            'applicants' => 'Applicants',
            'date_create' => 'Date Create',
            'user_created_id' => 'User Created ID',
            'create_ip' => 'Create Ip',
            'send' => 'Send',
        ];
    }
}
