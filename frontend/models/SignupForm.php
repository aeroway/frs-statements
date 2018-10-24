<?php
namespace frontend\models;

use common\models\User;
use frontend\models\VedjustAgency;
use frontend\models\VedjustSubject;
use frontend\models\VedjustSubdivision;
use yii\base\Model;
use Yii;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $full_name;
    public $position;
    public $phone;
    public $agency_id;
    public $subject_id;
    public $subdivision_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            //['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.'],
            [['username', 'full_name', 'position'], 'string', 'min' => 2, 'max' => 255],
            ['phone', 'string', 'min' => 2, 'max' => 100],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This email address has already been taken.'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],

            [['full_name', 'position', 'phone', 'agency_id', 'subject_id', 'subdivision_id'], 'required'],
            [['agency_id', 'subject_id'], 'integer'],
            //['subdivision_id', 'string'],

            [['agency_id'], 'exist', 'skipOnError' => true, 'targetClass' => VedjustAgency::className(), 'targetAttribute' => ['agency_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => VedjustSubject::className(), 'targetAttribute' => ['subject_id' => 'id']],
            [['subdivision_id'], 'exist', 'skipOnError' => true, 'targetClass' => VedjustSubdivision::className(), 'targetAttribute' => ['subdivision_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => 'Имя пользователя',
            'password' => 'Пароль',
            'full_name' => 'ФИО',
            'position' => 'Должность',
            'phone' => 'Телефон',
            'agency_id' => 'Орган',
            'subject_id' => 'Субъект РФ',
            'subdivision_id' => 'Муниципальное образование (отдел)',
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (($model = VedjustSubdivision::findOne((int)$this->subdivision_id)) === null) {
            $model = new VedjustSubdivision();
            $model->name = $this->subdivision_id;
            $model->subject_id = $this->subject_id;
            $model->agency_id = $this->agency_id;
            $model->save();
            $this->subdivision_id = $model->id;
        }

        if ($this->validate()) {
            $user = new User();
            $user->username = $this->email;
            $user->email = $this->email;
            $user->full_name = $this->full_name;
            $user->position = $this->position;
            $user->phone = $this->phone;
            $user->agency_id = $this->agency_id;
            $user->subject_id = $this->subject_id;
            $user->subdivision_id = $this->subdivision_id;
            $user->setPassword($this->password);
            $user->generateAuthKey();
            if ($user->save()) {
                return $user;
            }
        }

        return null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgency()
    {
        return $this->hasOne(VedjustAgency::className(), ['id' => 'agency_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubject()
    {
        return $this->hasOne(VedjustSubject::className(), ['id' => 'subject_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubdivision()
    {
        return $this->hasOne(VedjustSubdivision::className(), ['id' => 'subdivision_id']);
    }

}
