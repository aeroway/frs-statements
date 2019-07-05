<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "affairs".
 *
 * @property int $id
 * @property int $status
 * @property string $date_create
 * @property string $date_status
 * @property string $comment
 * @property string $kuvd
 * @property string $ref_num
 * @property int $ved_id
 * @property int $create_ip
 * @property int $accepted_ip
 * @property int $p_count
 * @property int $user_created_id
 * @property int $user_accepted_id
 *
 * @property Ved $ved
 */
class VedjustAffairs extends \yii\db\ActiveRecord
{
    public static $numIssuance;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'affairs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kuvd', 'p_count'], 'required'],
            [['kuvd'], 'unique', 'targetAttribute' => ['kuvd', 'ved_id']],
            [['comment', 'kuvd', 'ref_num'], 'string'],
            [['date_create', 'date_status'], 'safe'],
            [['ved_id', 'status', 'user_created_id', 'user_accepted_id', 'create_ip', 'accepted_ip', 'p_count'], 'integer'],
            [['ved_id'], 'exist', 'skipOnError' => true, 'targetClass' => VedjustVed::className(), 'targetAttribute' => ['ved_id' => 'id']],
            [['user_accepted_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_accepted_id' => 'id']],
            [['user_created_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_created_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status' => 'Статус',
            'date_create' => 'Дата создания',
            'date_status' => 'Дата подтверждения',
            'comment' => 'Комментарий',
            'kuvd' => 'КУВД',
            'ref_num' => 'Номер обращения',
            'ved_id' => 'Ведомость',
            'user_created_id' => 'Создал',
            'user_accepted_id' => 'Принял',
            'create_ip' => 'IP создания',
            'accepted_ip' => 'IP подтверждения',
            'p_count' => 'Число заявителей',
        ];
    }

    // location of docs in storage
    public function getStoragePath($id)
    {
        $modelStorage = VedjustStorage::find()
        ->select(['comment', 'name'])
        ->innerJoin('archive', 'storage.archive_id = archive.id')
        ->where(['=', 'ved_id', $id])
        ->asArray()
        ->one();

        return $modelStorage;
    }

    public function getCheckAffairsIssuance($id)
    {
        $modelVed = VedjustVed::find()
        ->select(['count(*) ct'])
        ->innerJoin('storage', 'ved.id = storage.ved_id')
        ->innerJoin('archive', 'storage.archive_id = archive.id')
        ->where(['and', ['ved.id' => $id], 
            ['archive.agency_id' => Yii::$app->user->identity->agency_id], 
            ['archive.subject_id' => Yii::$app->user->identity->subject_id], 
            ['archive.subdivision_id' => Yii::$app->user->identity->subdivision_id]])
        ->asArray()
        ->one();

        return $modelVed["ct"];
    }

    public function getCountIssuance()
    {
        $numIssuance = VedjustIssuance::find()->select(['count(*) num'])->where(['affairs_id' => $this->id])->asArray()->one()["num"];

        return $numIssuance; // . ' из ' . $this->p_count;
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
    public function getUserAccepted()
    {
        return $this->hasOne(User::className(), ['id' => 'user_accepted_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserCreated()
    {
        return $this->hasOne(User::className(), ['id' => 'user_created_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIssuance()
    {
        return $this->hasMany(VedjustIssuance::className(), ['affairs_id' => 'id']);
    }

}
