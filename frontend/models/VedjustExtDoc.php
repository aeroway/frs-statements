<?php

namespace frontend\models;

use Yii;
use kartik\mpdf\Pdf;

/**
 * This is the model class for table "ext_doc".
 *
 * @property int $id
 * @property int $user_created_id
 * @property int $user_formed_id
 * @property int $user_accepted_id
 * @property string $date_create
 * @property string $date_formed
 * @property string $date_reception
 * @property string $create_ip
 * @property string $formed_ip
 * @property string $accepted_ip
 * @property int $affairs_id
 * @property int $area_id
 *
 * @property User $userAccepted
 * @property User $userCreated
 * @property User $userFormed
 * @property VedjustArea $area
 * @property VedjustAffairs $affairs
 */
class VedjustExtDoc extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ext_doc';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_created_id', 'user_formed_id', 'user_accepted_id', 'create_ip', 'formed_ip', 'accepted_ip', 'affairs_id', 'area_id'], 'integer'],
            [['date_create', 'date_formed', 'date_reception'], 'safe'],
            [['affairs_id'], 'unique'],
            [['user_accepted_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_accepted_id' => 'id']],
            [['user_created_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_created_id' => 'id']],
            [['user_formed_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_formed_id' => 'id']],
            [['area_id'], 'exist', 'skipOnError' => true, 'targetClass' => VedjustArea::className(), 'targetAttribute' => ['area_id' => 'id']],
            [['affairs_id'], 'exist', 'skipOnError' => true, 'targetClass' => VedjustAffairs::className(), 'targetAttribute' => ['affairs_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_created_id' => 'Переместил в экстер. документы',
            'user_formed_id' => 'Сформировал экстер. документы',
            'user_accepted_id' => 'Подтвердил экстер. документы',
            'date_create' => 'Дата перемещения',
            'date_formed' => 'Дата формирования',
            'date_reception' => 'Дата подтверждения',
            'create_ip' => 'IP перемещения',
            'formed_ip' => 'IP формирования',
            'accepted_ip' => 'IP подтверждения',
            'affairs_id' => 'Дела',
            'area_id' => 'Район',
        ];
    }

    public function getExtDocs()
    {
        $modelExtDoc = VedjustExtDoc::find()
        ->select(['area.name loc', 'archive_unit.name unit', 'count(*) ct'])
        ->innerJoinWith('area', false)
        ->innerJoinWith('affairs', false)
        ->innerJoinWith('affairs.ved', false)
        ->innerJoinWith('affairs.ved.archiveUnit', false)
        ->where(['=', 'ext_doc.user_accepted_id', NULL])
        ->groupBy('area.name, archive_unit.name')
        ->asArray()
        ->all();

        return $modelExtDoc;
    }

    public function getExtDocsPdf($loc)
    {
        $modelVedjustExtDoc = VedjustExtDoc::find()
            ->select('ext_doc.id, kuvd, comment, archive_unit.name')
            ->asArray()
            ->innerJoinWith('area', false)
            ->innerJoinWith('affairs', false)
            ->innerJoinWith('affairs.ved', false)
            ->innerJoinWith('affairs.ved.archiveUnit', false)
            ->where(['and', ['area.name' => $loc], ['IS', 'ext_doc.user_accepted_id', NULL]])
            ->all();

        $content = 
        "
        <div style='text-align: center;'>
            <h1>Ведомость передачи дел в " . $loc . "</h1>
        </div>
        <div>
        <table border='1' cellpadding='3' width='100%' cellspacing='0'>
            <tr>
                <td>№</td>
                <td>КУВД</td>
                <td>Тип</td>
                <td>Комментарий</td>
            </tr>";
        $i = 0;
        foreach ($modelVedjustExtDoc as $value) {
            $i++;
            $content .= 
            "
            <tr>
                <td>" . $i . "</td>
                <td>" . $value['kuvd'] . "</td>
                <td>" . $value['name'] . "</td>
                <td>" . $value['comment'] . "</td>
            </tr>
            ";

            $idExtDoc[] = $value['id'];
        }

        $content .=
        "</table>
        </div>
        <div>
        <p>ФИО передал __________________________</p>
        <p>ФИО принял ___________________________</p>
        </div>
        ";

        $pdf = new Pdf();
        $mpdf = $pdf->api;
        $mpdf->WriteHtml($content);
        echo $mpdf->Output('ved.pdf', 'D');

        return $idExtDoc;
        //exit;
    }

    public function getExtDocsAccepted($loc)
    {
        $modelVedjustExtDoc = VedjustExtDoc::find()
            ->select('ext_doc.id')
            ->asArray()
            ->innerJoinWith('area', false)
            ->where(['and', ['area.name' => $loc], ['=', 'ext_doc.user_accepted_id', NULL]])
            ->all();

        return $modelVedjustExtDoc;
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
    public function getArea()
    {
        return $this->hasOne(VedjustArea::className(), ['id' => 'area_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAffairs()
    {
        return $this->hasOne(VedjustAffairs::className(), ['id' => 'affairs_id']);
    }
}
