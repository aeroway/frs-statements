<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\VedjustStorage;

/**
 * VedjustStorageSearch represents the model behind the search form of `frontend\models\VedjustStorage`.
 */
class VedjustStorageSearch extends VedjustStorage
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'ved_id', 'archive_id'], 'integer'],
            [['comment'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = VedjustStorage::find();

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

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'ved_id' => $this->ved_id,
            'comment' => $this->comment,
            'archive_id' => $this->archive_id,
        ]);

        return $dataProvider;
    }
}
