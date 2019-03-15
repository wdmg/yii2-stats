<?php

namespace wdmg\stats\models;

use yii;
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

    public $viewChart = true;
    public $viewRobots = false;
    public $viewOnlyRobots = false;
    public $viewReferrerURI = false;
    public $viewReferrerHost = false;
    public $viewClientIP = true;
    public $viewClientOS = true;
    public $viewTransitionType = true;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['period', 'start_date', 'end_date'], 'safe'],
            [['viewChart', 'viewRobots', 'viewOnlyRobots', 'viewReferrerURI', 'viewReferrerHost', 'viewClientIP', 'viewClientOS', 'viewTransitionType'], 'safe'],
            [['request_uri', 'referer_uri', 'remote_addr'], 'string'],
            [['user_id', 'unique'], 'integer'],
            [['datetime'], 'date', 'format' => 'php:Y-m-d'],
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
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'viewChart' => Yii::t('app/modules/stats', 'Show chart'),
            'viewRobots' => Yii::t('app/modules/stats', 'Show robots'),
            'viewOnlyRobots' => Yii::t('app/modules/stats', 'Show only robots'),
            'viewReferrerURI' => Yii::t('app/modules/stats', 'Show referrer URI'),
            'viewReferrerHost' => Yii::t('app/modules/stats', 'Show referrer Host'),
            'viewClientIP' => Yii::t('app/modules/stats', 'Show client IP'),
            'viewClientOS' => Yii::t('app/modules/stats', 'Show client OS'),
            'viewTransitionType' => Yii::t('app/modules/stats', 'Show type of transition'),
        ];
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

        if($this->viewOnlyRobots) {
            $this->viewRobots = true;
            $query->andFilterWhere(['>', 'robot_id', 0]);
        } else {
            if($this->viewRobots) {
                $query->andFilterWhere(['>=', 'robot_id', 0]);
            } else {
                $query->andFilterWhere(['=', 'robot_id', 0]);
            }
        }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'request_uri' => $this->request_uri,
            'referer_uri' => $this->referer_uri,
            'user_id' => $this->user_id,
            'remote_addr' => $this->remote_addr,
            'unique' => $this->unique,
        ]);

        if(!$this->period)
            $this->period = 'today';

        $dateTime = new \DateTime('00:00:00');
        if ($this->period == 'today') {
            $dateNew = clone $dateTime;
            $start = $dateNew->modify('+1 day')->getTimestamp();
            $end = $dateTime->getTimestamp();
        } else if ($this->period == 'yesterday') {
            $dateNew = clone $dateTime;
            $start = $dateNew->getTimestamp();
            $end = $dateNew->modify('-1 day')->getTimestamp();
        } else if ($this->period == 'week') {
            $dateNew = clone $dateTime;
            $start = $dateNew->getTimestamp();
            $end = $dateNew->modify('-1 week')->getTimestamp();
        } else if ($this->period == 'month') {
            $dateNew = clone $dateTime;
            $start = $dateNew->getTimestamp();
            $end = $dateNew->modify('-1 month')->getTimestamp();
        } else if ($this->period == 'year') {
            $dateNew = clone $dateTime;
            $start = $dateNew->getTimestamp();
            $end = $dateNew->modify('-1 year')->getTimestamp();
        }

        $query->andFilterWhere(['<', 'datetime', $start]);
        $query->andFilterWhere(['>=', 'datetime', $end]);

        if($this->datetime) {
            $this->period = 'custom';
            $dateTime = new \DateTime($this->datetime);
            $dateNew = clone $dateTime;
            $start = $dateNew->modify('+1 day')->getTimestamp();
            $end = $dateTime->getTimestamp();
            $query->andFilterWhere(['<', 'datetime', $start]);
            $query->andFilterWhere(['>=', 'datetime', $end]);
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
