<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\VedjustArchive;

/**
 * VedjustArchiveSearch represents the model behind the search form of `frontend\models\VedjustArchive`.
 */
class VedjustArchiveSearch extends VedjustArchive
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name', 'subdivision_id', 'user_created_id', 'agency_id', 'subject_id'], 'safe'],
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
        $query = VedjustArchive::find()->where(['archive.subdivision_id' => Yii::$app->user->identity->subdivision_id]);

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

        $query->innerJoinWith('userCreated', false);
        $query->innerJoinWith('agency', false);
        $query->innerJoinWith('subject', false);
        $query->innerJoinWith('subdivision', false);

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'archive.name', $this->name])
            ->andFilterWhere(['like', 'user.full_name', $this->user_created_id])
            ->andFilterWhere(['like', 'agency.name', $this->agency_id])
            ->andFilterWhere(['like', 'subject.name', $this->subject_id])
            ->andFilterWhere(['like', 'subdivision.name', $this->subdivision_id]);

        return $dataProvider;
    }
}
