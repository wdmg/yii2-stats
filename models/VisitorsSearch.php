<?php

namespace wdmg\stats\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use wdmg\stats\models\Visitors;

/**
 * VisitorsSearch represents the model behind the search form of `wdmg\stats\models\Visitors`.
 */
class VisitorsSearch extends Visitors
{
    public $period;
    public $start_date;
    public $end_date;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['period', 'start_date', 'end_date'], 'safe'],
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
        $query = Visitors::find();

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);


        $this->load($params);

        if(isset($params['period'])) {
            if(!$this->period)
                $this->period = $params['period'];
        }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }


        if(!$this->period)
            $this->period = 'today';

        if ($this->period == 'today') {
            $query->andFilterWhere(['<=', 'datetime', strtotime('NOW')]);
            $query->andFilterWhere(['>', 'datetime', strtotime('NOW - 1 day')]);
        } else if ($this->period == 'yesterday') {
            $query->andFilterWhere(['<=', 'datetime', strtotime('NOW - 1 day')]);
            $query->andFilterWhere(['>', 'datetime', strtotime('NOW - 2 day')]);
        } else if ($this->period == 'week') {
            $query->andFilterWhere(['<=', 'datetime', strtotime('NOW')]);
            $query->andFilterWhere(['>', 'datetime', strtotime('NOW - 1 week')]);
        } else if ($this->period == 'month') {
            $query->andFilterWhere(['<=', 'datetime', strtotime('NOW')]);
            $query->andFilterWhere(['>', 'datetime', strtotime('NOW - 1 month')]);
        } else if ($this->period == 'year') {
            $query->andFilterWhere(['<=', 'datetime', strtotime('NOW')]);
            $query->andFilterWhere(['>', 'datetime', strtotime('NOW - 1 year')]);
        }
/*
        $query->andFilterWhere([
            '<=',
            'datetime',
            Date('Y-m-d 00:00:00', strtotime('NOW() - 1 day')
        ]);
*/

/*
        // grid filtering conditions
        $query->andFilterWhere(['>=', 'datetime', Date('Y-m-d 00:00:00', strtotime($this->start_date))])
            ->andFilterWhere(['<', 'datetime', Date('Y-m-d 00:00:00', strtotime($this->end_date))]);
*/

        $query->orderBy(['datetime' => SORT_DESC]);
        return $dataProvider;
    }
}
