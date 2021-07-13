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
    // public $kuvd_affairs, $ref_num_affairs, $search_all;
    public $search_num_ved;
    public $isStrictSearchRefNumAffairs;
    public $isStrictSearchCommentVedAffairs;
    public $search_ref_num_affairs;
    public $search_comment_ved_affairs;

    public $isStrictSearchRefNum = 1;
    public $isStrictSearchAffairs = 1;
    public $search_ref_num;
    public $search_affairs;

    public function rules()
    {
        return [
            [['id', 'user_formed_id', 'verified', 'create_ip', 'formed_ip', 'accepted_ip'
                , 'ext_reg', 'target', 'search_all', 'search_num_ved',
                'isStrictSearchRefNumAffairs', 'isStrictSearchCommentVedAffairs', 'isStrictSearchRefNum', 'isStrictSearchAffairs'], 'integer'],
            [['date_create', 'num_ved', 'date_reception', 'date_formed', 'kuvd_affairs', 'status_id', 'user_created_id', 
                'user_accepted_id', 'archive_unit_id', 'comment', 'address_id', 'ref_num_affairs',
                'search_comment_ved_affairs', 'search_ref_num_affairs', 'search_ref_num', 'search_affairs'], 'safe'],
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
        if (!empty($params["VedjustVedSearch"]["isStrictSearchRefNum"]) ||
            !empty($params["VedjustVedSearch"]["isStrictSearchAffairs"]) ||
            !empty($params["VedjustVedSearch"]["isStrictSearchRefNumAffairs"]) ||
            !empty($params["VedjustVedSearch"]["isStrictSearchCommentVedAffairs"])) {
            $symbolStrict = '=';
            $symbolStrict2 = '=';
        } else {
            $symbolStrict = 'ILIKE';
            $symbolStrict2 = 'LIKE';
        }

        if (!empty($params["VedjustVedSearch"]["search_num_ved"])) {
            $numVed = $params["VedjustVedSearch"]["search_num_ved"];
            $query = VedjustVed::find()
                ->alias('v')
                ->distinct(['v.id'])
                ->innerJoin('user us', 'us.id = v.user_created_id')
                ->where(
                    ['or',
                        ['and',
                            ['=', 'v.status_id', 1],
                            ['=', 'us.address_id', Yii::$app->user->identity->address_id],
                            ['=', 'v.id', $numVed],
                        ],
                        ['and',
                            ['<>', 'v.status_id', 1],
                            ['=', 'v.id', $numVed]
                        ],
                    ],
                );
        } elseif (!empty($params["VedjustVedSearch"]["search_comment_ved_affairs"])) {
            $commentVedAffairs = $params["VedjustVedSearch"]["search_comment_ved_affairs"];
            $query = VedjustVed::find()
                ->alias('v')
                ->distinct(['v.id'])
                ->where(
                    ['and',
                        ['or',
                            ['<>', 'v.status_id', 1],
                            ['and',
                                ['=', 'v.status_id', 1],
                                ['=', 'uc.address_id', Yii::$app->user->identity->address_id],
                            ],
                        ],
                        ['or',
                            [$symbolStrict, 'a.comment', $commentVedAffairs],
                            [$symbolStrict, 'v.comment', $commentVedAffairs],
                        ],
                    ],
                );
            $query->innerJoinWith('affairs a');
        } elseif (!empty($params["VedjustVedSearch"]["search_ref_num_affairs"])) {
            $refNumAffairs = $params["VedjustVedSearch"]["search_ref_num_affairs"];
            $query = VedjustVed::find()
                ->alias('v')
                ->distinct(['v.id'])
                ->where(
                    ['and',
                        ['or',
                            ['<>', 'v.status_id', 1],
                            ['and',
                                ['=', 'v.status_id', 1],
                                ['=', 'uc.address_id', Yii::$app->user->identity->address_id],
                            ],
                        ],
                        ['or',
                            [$symbolStrict2, 'a.ref_num', strtoupper($refNumAffairs)],
                            [$symbolStrict2, 'a.kuvd', strtoupper($refNumAffairs)],
                        ],
                    ],
                );
            $query->innerJoinWith('affairsV a');
        } elseif (!empty($params["VedjustVedSearch"]["search_ref_num"])) {
            $refNumAffairs = $params["VedjustVedSearch"]["search_ref_num"];
            $query = VedjustVed::find()
                ->alias('v')
                ->distinct(['v.id'])
                ->where(
                    ['and',
                        ['or',
                            ['<>', 'v.status_id', 1],
                            ['and',
                                ['=', 'v.status_id', 1],
                                ['=', 'uc.address_id', Yii::$app->user->identity->address_id],
                            ],
                        ],
                        [$symbolStrict2, 'a.ref_num', strtoupper($refNumAffairs)],
                    ],
                );
            $query->innerJoinWith('affairsV a');
        } elseif (!empty($params["VedjustVedSearch"]["search_affairs"])) {
            $refNumAffairs = $params["VedjustVedSearch"]["search_affairs"];
            $query = VedjustVed::find()
                ->alias('v')
                ->distinct(['v.id'])
                ->where(
                    ['and',
                        ['or',
                            ['<>', 'v.status_id', 1],
                            ['and',
                                ['=', 'v.status_id', 1],
                                ['=', 'uc.address_id', Yii::$app->user->identity->address_id],
                            ],
                        ],
                        [$symbolStrict2, 'a.kuvd', strtoupper($refNumAffairs)],
                    ],
                );
            $query->innerJoinWith('affairsV a');
        } else {
            //по умолчанию пользователь должен видеть только те записи, которые созданы его отделом или направлены в его отдел
            //исключение для кадастровой палаты - по умолчанию могут видеть все ведомости по своему органу
            if (Yii::$app->user->identity->agency_id == 2) {
                $userSd = ['=', 'uc.subdivision_id', Yii::$app->user->identity->agency_id];
            } else {
                $userSd = ['=', 'uc.subdivision_id', Yii::$app->user->identity->subdivision_id];
            }

            if (Yii::$app->getRequest()->getCookies()->getValue('archive')) {
                $query = VedjustVed::find()
                    ->alias('v')
                    ->distinct(['v.id'])
                    ->where(
                        ['or',
                            $userSd, // Ведомости всех коллег пользователя
                            ['and', ['<>', 'v.status_id', 1], ['=', 'v.subdivision_id', Yii::$app->user->identity->subdivision_id]], // Ведомости направленные в отдел пользователя
                        ]
                    );
            } else {
                $query = VedjustVed::find()
                    ->alias('v')
                    ->distinct(['v.id'])
                    ->where(
                        ['and',
                            ['or',
                                ['>=', 'date_reception', date('Y-m-d', strtotime("-3 days"))],
                                ['and', ['IS', 'date_reception', NULL], ['>=', 'date_formed', date('Y-m-d', strtotime("-3 days"))]],
                                ['=', 'v.status_id', 1],
                            ],
                            ['or',
                                $userSd, // Ведомости всех коллег пользователя
                                ['and', ['<>', 'v.status_id', 1], ['=', 'v.subdivision_id', Yii::$app->user->identity->subdivision_id]], // Ведомости направленные в отдел пользователя
                            ],
                        ]
                    );
            }

            if ($this->checkLimitOpenVed()) {
                $query = VedjustVed::find()
                    ->where(
                        ['and',
                            ['status_id' => 1],
                            ['user_created_id' => Yii::$app->user->identity->id],
                        ],
                    );
            }
        }

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->innerJoinWith('userCreated uc');
        $query->joinWith('address adr');

        // grid filtering conditions
        $query->andFilterWhere([
            'v.id' => $this->id,
            'v.date_create' => $this->date_create ? date('Y-m-d', strtotime($this->date_create)) : $this->date_create,
            'date_reception' => $this->date_reception ? date('Y-m-d', strtotime($this->date_reception)) : $this->date_reception,
            'date_formed' => $this->date_formed,
            'user_formed_id' => $this->user_formed_id,
            'verified' => $this->verified,
            'create_ip' => $this->create_ip,
            'formed_ip' => $this->formed_ip,
            'accepted_ip' => $this->accepted_ip,
            'ext_reg' => $this->ext_reg,
            'target' => $this->target,
            'status_id' => $this->status_id,
            'uc.agency_id' => $this->user_created_id,
        ]);

        $query->andFilterWhere(['like', 'num_ved', $this->num_ved])
            ->andFilterWhere(['ilike', 'v.comment', $this->comment])
            ->andFilterWhere(['like', 'adr.name', $this->address_id]);

        return $dataProvider;
    }
}
