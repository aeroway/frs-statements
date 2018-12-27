<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\VedjustExtDoc;

/**
 * VedjustAffairsSearch represents the model behind the search form of `frontend\models\VedjustExtDoc`.
 */
class VedjustExtDocSearch extends VedjustExtDoc
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_created_id', 'user_formed_id', 'user_accepted_id', 'create_ip', 'formed_ip', 'accepted_ip', 'affairs_id', 'area_id'], 'integer'],
            [['date_create', 'date_formed', 'date_reception'], 'safe'],
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
        if(!empty(Yii::$app->request->get('loc')))
            $query = VedjustExtDoc::find()->where(['and', ['area.name' => Yii::$app->request->get('loc')], ['IS', 'ext_doc.user_accepted_id', NULL]]);
        else
            $query = VedjustExtDoc::find();

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

        $query->innerJoinWith('area', false);
        $query->innerJoinWith('affairs', false);
        $query->innerJoinWith('affairs.ved', false);
        $query->innerJoinWith('affairs.ved.archiveUnit', false);

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'user_formed_id' => $this->user_formed_id,
            'user_accepted_id' => $this->user_accepted_id,
            'create_ip' => $this->create_ip,
            'formed_ip' => $this->formed_ip,
            'accepted_ip' => $this->accepted_ip,
            'date_create' => $this->date_create,
            'date_formed' => $this->date_formed,
            'date_reception' => $this->date_reception,
        ]);

        $query->andFilterWhere(['like', 'user_created_id', $this->user_created_id])
            ->andFilterWhere(['like', 'affairs_id', $this->affairs_id])
            ->andFilterWhere(['like', 'area_id', $this->area_id]);

        return $dataProvider;
    }
}
