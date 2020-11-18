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
    public $barcode;
    public static $checkAffairsIssuance;
    public static $vedStatusId;
    public static $isCheckBoxDisabled;

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
            ['ref_num', 'either', 'skipOnEmpty' => false, 'params' => ['other' => 'kuvd']],
            [['kuvd', 'ref_num'], 'unique', 'targetAttribute' => ['kuvd', 'ref_num', 'ved_id']],
            [['kuvd', 'ref_num', 'barcode'], 'string', 'max' => 40],
            [['comment'], 'string'],
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
            'barcode' => '№ обращения или КУВД',
        ];
    }

    public function either($attribute_name, $params)
    {
        $field1 = $this->getAttributeLabel($attribute_name);
        $field2 = $this->getAttributeLabel($params['other']);
        if (empty($this->$attribute_name) && empty($this->{$params['other']})) {
            $this->addError($attribute_name, "Необходимо заполнить {$field1} или {$field2}.");
        }
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
        if ($this->status === 1 && (VedjustAffairs::$vedStatusId === 5 || VedjustAffairs::$vedStatusId === 6)) {
            return VedjustIssuance::find()->select(['count(*) num'])->where(['affairs_id' => $this->id])->asArray()->one()["num"];
        }

        return 0;
    }

    public function checkPermitAffairsBarcode($modelVed)
    {
        return ($modelVed->status_id == 2 && $modelVed->address_id == Yii::$app->user->identity->address_id);
    }

    public function statusAffairsBarcode($model)
    {
        if (empty($model->barcode)) {
            return false;
        }

        return VedjustAffairs::updateAll(
            [
                'status' => 1,
                'date_status' => date('Y-m-d H:i:s'),
                'accepted_ip' => ip2long(Yii::$app->request->userIP),
                'user_accepted_id' => Yii::$app->user->identity->id,
            ],
            ['and',
                ['or',
                    ['=', 'ref_num', $model->barcode],
                    ['=', 'kuvd', $model->barcode],
                ],
                ['=', 'ved_id', $model->ved_id],
            ],
        );
    }

    public function isCheckBoxDisabled($modelVed) {
        return !empty($modelVed->verified)
            || $modelVed->status_id !== 2
            || Yii::$app->user->can('addAudit')
            || $modelVed->user_created_id === Yii::$app->user->identity->id
            || $modelVed->address_id !== Yii::$app->user->identity->address_id;
    }

    public function isVedNotVerified($modelVed) {
        return empty($modelVed->verified)
            && $modelVed->status_id === 2
            && !Yii::$app->user->can('addAudit')
            && $modelVed->user_created_id !== Yii::$app->user->identity->id
            && $modelVed->address_id == Yii::$app->user->identity->address_id;
    }

    public function getApplicants()
    {
        if (empty($this->ref_num)) {
            return '';
        }

        $mfcNotice = MfcNotice::find()->select(['applicants'])->where(['ref_num' => $this->ref_num])->asArray()->one();

        if (empty($mfcNotice["applicants"])) {
            return '';
        }

        preg_match_all('/[+0-9]+/', $mfcNotice["applicants"], $matches);

        return implode(", ", $matches[0]);
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