<?php

namespace wdmg\stats\models;

use yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use wdmg\stats\models\Robots;

/**
 * RobotsSearch represents the model behind the search form of `wdmg\stats\models\Robots`.
 */
class RobotsSearch extends Robots
{

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
        $query = Robots::find();

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

        return $dataProvider;
    }
}
