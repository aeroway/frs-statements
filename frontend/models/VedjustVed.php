<?php

namespace frontend\models;

use Yii;
use kartik\mpdf\Pdf;

/**
 * This is the model class for table "ved".
 *
 * @property int $id
 * @property string $date_create
 * @property string $num_ved
 * @property string $comment
 * @property int $status_id
 * @property string $date_reception
 * @property string $date_formed
 * @property int $user_created_id
 * @property int $user_accepted_id
 * @property int $user_formed_id
 * @property int $verified
 * @property int $target
 * @property int $create_ip
 * @property int $formed_ip
 * @property int $accepted_ip
 * @property int $archive_unit_id
 * @property int $subdivision_id
 * @property int $address_id
 * @property int $area_id
 * @property int $ext_reg
 * @property int $ext_reg_created
 * @property int $search_all
 *
 * @property Affairs[] $affairs
 * @property Status $status
 * @property User $userAccepted
 * @property User $userCreated
 */
class VedjustVed extends \yii\db\ActiveRecord
{
    public $search_all;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ved';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['target', 'subdivision_id', 'address_id'], 'required'],
            [['date_create', 'date_reception', 'date_formed'], 'safe'],
            [['num_ved', 'comment'], 'string'],
            [['status_id', 'user_formed_id', 'user_created_id', 'user_accepted_id', 'verified', 'target', 'create_ip', 'formed_ip', 'accepted_ip', 'archive_unit_id', 'subdivision_id', 'address_id', 'ext_reg', 'ext_reg_created', 'area_id', 'search_all'], 'integer'],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => VedjustStatus::className(), 'targetAttribute' => ['status_id' => 'id']],
            [['user_accepted_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_accepted_id' => 'id']],
            [['user_created_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_created_id' => 'id']],
            [['user_formed_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_formed_id' => 'id']],
            [['archive_unit_id'], 'exist', 'skipOnError' => true, 'targetClass' => VedjustArchiveUnit::className(), 'targetAttribute' => ['archive_unit_id' => 'id']],
            [['subdivision_id'], 'exist', 'skipOnError' => true, 'targetClass' => VedjustSubdivision::className(), 'targetAttribute' => ['subdivision_id' => 'id']],
            [['address_id'], 'exist', 'skipOnError' => true, 'targetClass' => VedjustAddress::className(), 'targetAttribute' => ['address_id' => 'id']],
            [['area_id'], 'exist', 'skipOnError' => true, 'targetClass' => VedjustArea::className(), 'targetAttribute' => ['area_id' => 'id']],
            [['target'], 'exist', 'skipOnError' => true, 'targetClass' => VedjustAgency::className(), 'targetAttribute' => ['target' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Номер ведомости',
            'date_create' => 'Дата создания',
            'num_ved' => 'Номер ведомости',
            'comment' => 'Комментарий',
            'status_id' => 'Состояние',
            'date_reception' => 'Дата принятия',
            'date_formed' => 'Дата формирования',
            'user_created_id' => 'Создал',
            'user_accepted_id' => 'Принял',
            'user_formed_id' => 'Сформировал',
            'verified' => 'Проверено',
            'target' => 'Получатель',
            'create_ip' => 'IP создания',
            'formed_ip' => 'IP формирования',
            'accepted_ip' => 'IP принятия',
            'kuvd_affairs' => 'КУВД дела',
            'ref_num_affairs' => '№ обращения',
            'archive_unit_id' => 'Ед. арх. хранения',
            'subdivision_id' => 'Отдел',
            'address_id' => 'Адрес',
            'storage_id' => 'Архивохранилище',
            'ext_reg' => 'Экстерриториальная регистрация',
            'ext_reg_created' => 'Перемещено в таблицу экстер. документов',
            'area_id' => 'Район',
            'search_all' => 'Поиск по краю (МФЦ, Росреестр, Палата)',
        ];
    }

    public function getIconStatus()
    {
        switch ($this->verified) {
            case 1:
                return '<span class="glyphicon glyphicon-ok" title="Подтверждено"> </span>';
                break;
            case 0:
                return '<span class="glyphicon glyphicon-remove" title="На проверке"> </span>';
                break;
            default:
                return $this->verified;
        }
    }

    public function getIconExtReg()
    {
        switch ($this->ext_reg) {
            case 1:
                return '<span class="glyphicon glyphicon-plus" title="Экстерриториальная регистрация"> </span>';
                break;
            default:
                return '<span class="glyphicon glyphicon-minus" title="Обычная регистрация"> </span>';
        }
    }

    public function getTargetRecipient()
    {
        $target = '';
        $target = $this->AgencyName;

        return $target . ' (' . $this->subdivision->name . ')';
    }

    public function getVedPdf()
    {
        $modelAffairs = VedjustAffairs::find()
            ->select('kuvd, comment')
            ->asArray()
            ->where(["ved_id" => $this->id])
            ->orderBy(["id" => SORT_ASC])
            ->all();

        $modelVed = VedjustVed::find()
            ->select("archive_unit.name_rp, address.name, ved.user_created_id, ved.user_accepted_id, area.name as area_name, agency.name as agName, agsr.name as ags, adrs.name as adr, ved.comment")
            ->asArray()
            ->leftJoin('archive_unit', 'archive_unit.id = ved.archive_unit_id')
            ->leftJoin('address', 'address.id = ved.address_id')
            ->leftJoin('agency', 'agency.id = ved.target')
            ->leftJoin('area', 'area.id = ved.area_id')
            ->innerJoin('user', '"user".id = ved.user_created_id')
            ->innerJoin('address adrs', 'adrs.id = "user".address_id')
            ->innerJoin('agency agsr', 'agsr.id = "user".agency_id')
            ->where(["ved.id" => $this->id])
            ->one();

        $dateCreate = Yii::$app->formatter->asDate($this->date_create, 'dd.MM.yyyy');

        if (!empty($modelVed['area_name'])) {
            $vedAreaName = "<h4>" . $modelVed['area_name'] . "</h4>";
        } else {
            $vedAreaName = '';
        }

        if (!empty($modelVed['comment'])) {
            $vedComment = "<h4>" . $modelVed['comment'] . "</h4>";
        } else {
            $vedComment = '';
        }

        $content = 
        "
        <div style='text-align: center;'>
            <h1>Ведомость " . $modelVed['name_rp'] . " №$this->id</h1>
            <h2>от $dateCreate</h2>
            <h3>Передал: " . '<br>' . $modelVed['ags'] . '<br>' . $modelVed['adr'] . "</h3>
            <h3>Получатель: " . '<br>' . $modelVed['agName'] . '<br>' . $modelVed['name'] . "</h3>
            " . $vedAreaName . "
            " . $vedComment . "
        </div>
        <div>
        <table border='1' cellpadding='3' width='100%' cellspacing='0'>
            <tr>
                <td>№</td>
                <td>КУВД</td>
                <td>Комментарий</td>
            </tr>";

        $i = 0;
        foreach ($modelAffairs as $value) {
            $i++;
            $content .= 
            "
            <tr>
                <td>" . $i . "</td>
                <td>" . $value['kuvd'] . "</td>
                <td>" . $value['comment'] . "</td>
            </tr>
            ";
        }

        $usCrName = User::find()->select(['full_name'])->where(['id' => $modelVed['user_created_id']])->one();
        $usAcName = User::find()->select(['full_name'])->where(['id' => $modelVed['user_accepted_id']])->one();

        $content .=
        "</table>
        </div>
        <div>
        <p>ФИО передал: <u>" . (empty($usCrName) ? '' : $usCrName->full_name) . "</u></p>
        <p>ФИО принял: <u>" . (empty($usAcName) ? '' : $usAcName->full_name) . "</u></p>
        </div>
        ";

        $pdf = new Pdf();
        $mpdf = $pdf->api;
        // $mpdf->SetHeader('AW');
        $mpdf->WriteHtml($content);
        echo $mpdf->Output('ved.pdf', 'D');

        exit;
    }

    public function getIconUnit()
    {
        switch ($this->archive_unit_id) {
            case 1:
                return '<span class="glyphicon glyphicon-briefcase" title="Реестровое дело"> </span>';
                break;
            case 2:
                return '<span class="glyphicon glyphicon-file" title="Расписки"> </span>';
                break;
            case 3:
                return '<span class="glyphicon glyphicon-folder-open" title="Выходные документы"> </span>';
                break;
            case 4:
                return '<span class="glyphicon glyphicon-floppy-disk" title="Невостребованные документы"> </span>';
                break;
            default:
                return $this->archive_unit_id;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgency()
    {
        return $this->hasOne(VedjustAgency::className(), ['id' => 'target']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgencyName()
    {
        if (!empty($this->agency)) {
            return $this->agency->name;
        } else {
            return 'не указан';
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAffairs()
    {
        return $this->hasMany(VedjustAffairs::className(), ['ved_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(VedjustStatus::className(), ['id' => 'status_id']);
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
    public function getUserFormed()
    {
        return $this->hasOne(User::className(), ['id' => 'user_formed_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArchiveUnit()
    {
        return $this->hasOne(VedjustArchiveUnit::className(), ['id' => 'archive_unit_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubdivision()
    {
        return $this->hasOne(VedjustSubdivision::className(), ['id' => 'subdivision_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddress()
    {
        return $this->hasOne(VedjustAddress::className(), ['id' => 'address_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArea()
    {
        return $this->hasOne(VedjustArea::className(), ['id' => 'area_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStorage()
    {
        return $this->hasMany(VedjustStorage::className(), ['ved_id' => 'id']);
    }
}
