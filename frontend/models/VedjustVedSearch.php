<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\VedjustVed;

/**
 * VedjustVedSearch represents the model behind the search form of `frontend\models\VedjustVed`.
 */
class VedjustVedSearch extends VedjustVed
{
    /**
     * @inheritdoc
     */
    public $kuvd_affairs;

    public function rules()
    {
        return [
            [['id', 'user_formed_id', 'verified', 'create_ip', 'formed_ip', 'accepted_ip', 'ext_reg', 'target'], 'integer'],
            [['date_create', 'num_ved', 'date_reception', 'date_formed', 'kuvd_affairs', 'status_id', 'user_created_id', 'user_accepted_id', 'archive_unit_id', 'comment'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        //по умолчанию пользователь должен видеть только те записи, которые созданы его отделом или направлены в его отдел
        //исключение для кадастровой палаты - по умолчанию могут видеть все ведомости по своему органу
        if (Yii::$app->user->identity->agency_id == 2) {
            $userSd = User::find()
                ->alias('us')
                ->select(['us.id'])
                ->where(['=', 'us.agency_id', Yii::$app->user->identity->agency_id]);
        } else {
            $userSd = User::find()
                ->alias('us')
                ->select(['us.id'])
                ->where(['=', 'us.subdivision_id', Yii::$app->user->identity->subdivision_id]);
        }

        if (Yii::$app->getRequest()->getCookies()->getValue('archive')) {
            $query = VedjustVed::find()
                ->alias('v')
                ->distinct(['v.id'])
                ->leftJoin('user u_ac', 'u_ac.subdivision_id = v.subdivision_id')
                ->where(
                ['or',
                    ['in', 'v.user_created_id', $userSd], // Ведомости всех коллег пользователя
                    ['and', ['<>', 'v.status_id', 1], ['=', 'v.subdivision_id', Yii::$app->user->identity->subdivision_id]], // Ведомости направленные в отдел пользователя
                ]);
        } else {
            $query = VedjustVed::find()
                ->alias('v')
                ->distinct(['v.id'])
                ->leftJoin('user u_ac', 'u_ac.subdivision_id = v.subdivision_id')
                ->where(
                    ['and',
                        ['or',
                            ['>=', 'date_reception', date('Y-m-d', strtotime("-10 days"))],
                            ['IS', 'date_reception', NULL],
                        ],
                        ['or',
                            ['in', 'v.user_created_id', $userSd], // Ведомости всех коллег пользователя
                            ['and', ['<>', 'v.status_id', 1], ['=', 'v.subdivision_id', Yii::$app->user->identity->subdivision_id]], // Ведомости направленные в отдел пользователя
                        ],
                    ]);
        }

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->joinWith('affairs a');
        $query->joinWith('status s');
        $query->joinWith('userCreated uc');
        $query->joinWith('userAccepted ua');
        $query->joinWith('archiveUnit au');
        $query->orderBy(['v.id' => SORT_DESC]);

        // grid filtering conditions
        $query->andFilterWhere([
            'v.id' => $this->id,
            'v.date_create' => $this->date_create,
            'date_reception' => $this->date_reception,
            'date_formed' => $this->date_formed,
            'user_formed_id' => $this->user_formed_id,
            'verified' => $this->verified,
            'create_ip' => $this->create_ip,
            'formed_ip' => $this->formed_ip,
            'accepted_ip' => $this->accepted_ip,
            'ext_reg' => $this->ext_reg,
            //'a.kuvd' => $this->kuvd_affairs,
            'target' => $this->target,
        ]);

        $query->andFilterWhere(['like', 'num_ved', $this->num_ved])
            ->andFilterWhere(['like', 's.name', $this->status_id])
            ->andFilterWhere(['like', 'a.kuvd', $this->kuvd_affairs])
            ->andFilterWhere(['like', 'uc.email', $this->user_created_id])
            ->andFilterWhere(['like', 'au.name', $this->archive_unit_id])
            ->andFilterWhere(['like', 'v.comment', $this->comment])
            ->andFilterWhere(['like', 'ua.email', $this->user_accepted_id]);

        return $dataProvider;
    }
}
