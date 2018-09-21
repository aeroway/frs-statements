<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\VedjustAffairs;

/**
 * VedjustAffairsSearch represents the model behind the search form of `frontend\models\VedjustAffairs`.
 */
class VedjustAffairsSearch extends VedjustAffairs
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_created_id', 'user_accepted_id', 'create_ip', 'accepted_ip'], 'integer'],
            [['status', 'date_create', 'date_status', 'comment', 'kuvd', 'ved_id'], 'safe'],
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
        if(!empty(Yii::$app->request->get('id')))
            $query = VedjustAffairs::find()->where(['ved_id' => Yii::$app->request->get('id')]);
        else
            $query = VedjustAffairs::find();

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
            'date_create' => $this->date_create,
            'date_status' => $this->date_status,
            'user_created_id' => $this->user_created_id,
            'user_accepted_id' => $this->user_accepted_id,
            'create_ip' => $this->create_ip,
            'accepted_ip' => $this->accepted_ip,
        ]);

        $query->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'comment', $this->comment])
            ->andFilterWhere(['like', 'kuvd', $this->kuvd]);

        return $dataProvider;
    }
}
