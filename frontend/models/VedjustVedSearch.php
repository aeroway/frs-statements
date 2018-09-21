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
            [['id', 'user_formed_id', 'verified', 'create_ip', 'formed_ip', 'accepted_ip', 'ext_reg'], 'integer'],
            [['date_create', 'num_ved', 'date_reception', 'date_formed', 'kuvd_affairs', 'status_id', 'user_created_id', 'user_accepted_id', 'archive_unit_id'], 'safe'],
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
        if(Yii::$app->user->can('editMfc')) {
            if (Yii::$app->getRequest()->getCookies()->getValue('archive')) {
                $query = VedjustVed::find()->distinct()->where(
                            ['or',
                                ['and', ['target' => 1], ['<>', 'status_id', 1]],
                                ['and', ['target' => 1], ['ved.user_created_id' => Yii::$app->user->identity->id]],
                                ['and', ['target' => 2], ['ved.user_created_id' => Yii::$app->user->identity->id]],
                                ['and', ['target' => 3], ['ved.user_created_id' => Yii::$app->user->identity->id]],
                            ]
                         );
            } else {
                $query = VedjustVed::find()->distinct()->where(
                            ['and',
                                ['or',
                                    ['>=', 'date_reception', date('Y-m-d', strtotime("-10 days"))],
                                    ['IS', 'date_reception', NULL],
                                ],
                                ['or',
                                    ['and', ['target' => 1], ['<>', 'status_id', 1]],
                                    ['and', ['target' => 1], ['ved.user_created_id' => Yii::$app->user->identity->id]],
                                    ['and', ['target' => 2], ['ved.user_created_id' => Yii::$app->user->identity->id]],
                                    ['and', ['target' => 3], ['ved.user_created_id' => Yii::$app->user->identity->id]],
                                ],
                            ]
                         );
            }
        } elseif (Yii::$app->user->can('editRosreestr') || Yii::$app->user->can('confirmExtDocs')) {
            if (Yii::$app->getRequest()->getCookies()->getValue('archive')) {
                $query = VedjustVed::find()->distinct()->where(
                            ['or', 
                                ['and', ['target' => 3], ['<>', 'status_id', 1]], 
                                ['and', ['target' => 1], ['ved.user_created_id' => Yii::$app->user->identity->id]],
                                ['and', ['target' => 2], ['ved.user_created_id' => Yii::$app->user->identity->id]],
                                ['and', ['target' => 3], ['ved.user_created_id' => Yii::$app->user->identity->id]],
                            ]
                         );
            } else {
                $query = VedjustVed::find()->distinct()->where(
                            ['and',
                                ['or',
                                    ['>=', 'date_reception', date('Y-m-d', strtotime("-10 days"))],
                                    ['IS', 'date_reception', NULL],
                                ],
                                ['or', 
                                    ['and', ['target' => 3], ['<>', 'status_id', 1]], 
                                    ['and', ['target' => 1], ['ved.user_created_id' => Yii::$app->user->identity->id]],
                                    ['and', ['target' => 2], ['ved.user_created_id' => Yii::$app->user->identity->id]],
                                    ['and', ['target' => 3], ['ved.user_created_id' => Yii::$app->user->identity->id]],
                                ],
                            ]
                         );
            }
        } elseif (Yii::$app->user->can('editZkp')) {
            if (Yii::$app->getRequest()->getCookies()->getValue('archive')) {
                $query = VedjustVed::find()->distinct()->where(
                            ['or', 
                                ['and', ['target' => 2], ['<>', 'status_id', 1]], 
                                ['and', ['target' => 1], ['ved.user_created_id' => Yii::$app->user->identity->id]],
                                ['and', ['target' => 2], ['ved.user_created_id' => Yii::$app->user->identity->id]],
                                ['and', ['target' => 3], ['ved.user_created_id' => Yii::$app->user->identity->id]],
                            ]
                         );
            } else {
                $query = VedjustVed::find()->distinct()->where(
                            ['and',
                                ['or',
                                    ['>=', 'date_reception', date('Y-m-d', strtotime("-10 days"))],
                                    ['IS', 'date_reception', NULL],
                                ],
                                ['or', 
                                    ['and', ['target' => 2], ['<>', 'status_id', 1]], 
                                    ['and', ['target' => 1], ['ved.user_created_id' => Yii::$app->user->identity->id]],
                                    ['and', ['target' => 2], ['ved.user_created_id' => Yii::$app->user->identity->id]],
                                    ['and', ['target' => 3], ['ved.user_created_id' => Yii::$app->user->identity->id]],
                                ],
                            ]
                         );
            }
        } elseif (Yii::$app->user->can('addAudit')) {
            if (Yii::$app->getRequest()->getCookies()->getValue('archive')) {
                $query = VedjustVed::find()->distinct();
            } else {
                $query = VedjustVed::find()->distinct()->where(
                            ['or',
                                ['>=', 'date_reception', date('Y-m-d', strtotime("-10 days"))],
                                ['IS', 'date_reception', NULL],
                            ]
                         );
            }
        } else {
            $query = VedjustVed::find();
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
        $query->orderBy(['ved.id' => SORT_DESC]);

        // grid filtering conditions
        $query->andFilterWhere([
            'ved.id' => $this->id,
            'ved.date_create' => $this->date_create,
            'date_reception' => $this->date_reception,
            'date_formed' => $this->date_formed,
            'user_formed_id' => $this->user_formed_id,
            'verified' => $this->verified,
            'create_ip' => $this->create_ip,
            'formed_ip' => $this->formed_ip,
            'accepted_ip' => $this->accepted_ip,
            'ext_reg' => $this->ext_reg,
            'a.kuvd' => $this->kuvd_affairs,
        ]);

        $query->andFilterWhere(['like', 'num_ved', $this->num_ved])
            ->andFilterWhere(['like', 's.name', $this->status_id])
            ->andFilterWhere(['like', 'uc.email', $this->user_created_id])
            ->andFilterWhere(['like', 'au.name', $this->archive_unit_id])
            ->andFilterWhere(['like', 'ua.email', $this->user_accepted_id]);

        return $dataProvider;
    }
}
