<?php
namespace frontend\models;

use common\models\User;
use frontend\models\VedjustAgency;
use frontend\models\VedjustSubject;
use frontend\models\VedjustSubdivision;
use frontend\models\VedjustAddress;
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
    public $address_id;

    /**
     * @inheritdoc
     */
    public function getEmailLowercase()
    {
        return strtolower($this->email);
    }

    public function rules()
    {
        return [
            // ['username', 'required'],
            // ['username', 'filter', 'filter' => 'trim'],
            // ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Это имя пользователя уже занято.'],
            // [['username', 'full_name', 'position'], 'string', 'min' => 2, 'max' => 255],
            ['email', 'required'],
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'email'],
            ['email', 'unique', 'targetAttribute' => ['emailLowercase' => 'lower(email)'], 'targetClass' => '\common\models\User', 'message' => 'Этот адрес электронной почты уже занят.'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
            ['phone', 'string', 'min' => 2, 'max' => 100],

            [['full_name', 'position', 'phone', 'agency_id', 'subject_id', 'subdivision_id', 'address_id'], 'required'],
            [['agency_id', 'subject_id', 'subdivision_id', 'address_id'], 'integer'],

            [['agency_id'], 'exist', 'skipOnError' => true, 'targetClass' => VedjustAgency::className(), 'targetAttribute' => ['agency_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => VedjustSubject::className(), 'targetAttribute' => ['subject_id' => 'id']],
            [['subdivision_id'], 'exist', 'skipOnError' => true, 'targetClass' => VedjustSubdivision::className(), 'targetAttribute' => ['subdivision_id' => 'id']],
            [['address_id'], 'exist', 'skipOnError' => true, 'targetClass' => VedjustAddress::className(), 'targetAttribute' => ['address_id' => 'id']],
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
            'address_id' => 'Адрес',
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        // if (($model = VedjustSubdivision::findOne((int)$this->subdivision_id)) === null) {
        //     $model = new VedjustSubdivision();
        //     $model->name = $this->subdivision_id;
        //     $model->subject_id = $this->subject_id;
        //     $model->agency_id = $this->agency_id;
        //     $model->save();
        //     $this->subdivision_id = $model->id;
        // }

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
            $user->address_id = $this->address_id;
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddress()
    {
        return $this->hasOne(VedjustAddress::className(), ['id' => 'address_id']);
    }
}
