<?php

namespace wdmg\stats\controllers;

use Yii;
use wdmg\stats\models\Visitors;
use wdmg\stats\models\VisitorsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * VisitorsController implements the CRUD actions for Tasks model.
 */
class VisitorsController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public $defaultAction = 'index';

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['GET'],
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'roles' => ['admin'],
                        'allow' => true
                    ],
                ],
            ],
        ];

        // If auth manager not configured use default access control
        if(!Yii::$app->authManager) {
            $behaviors['access'] = [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'roles' => ['@'],
                        'allow' => true
                    ],
                ]
            ];
        }

        return $behaviors;
    }

    /**
     * Lists all Tasks models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new VisitorsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $visitors = $dataProvider->query->all();

        $chartData = [];
        $labels = [];
        $dataset = [];
        $output = [];

        if ($searchModel->period == 'today' || $searchModel->period == 'yesterday') {

            if($searchModel->period == 'yesterday') {
                $label = 'Yesterday visitors';
                $addPeriod = '1 days ';
            } else {
                $label = 'Today visitors';
                $addPeriod = '';
            }

            foreach ($visitors as $visitor) {
                for ($i = 1; $i <= 24; $i++) {
                    if($visitor->datetime <= strtotime('now -'.$addPeriod.''.$i.' hours') && $visitor->datetime > strtotime('now -'.$addPeriod.''.($i + 1).' hours'))
                        $output[$i][] = $visitor->datetime;
                }
            }
            for ($i = 1; $i <= 24; $i++) {
                $labels[] = date('d-m-Y H:i:s', strtotime('now -'.$addPeriod.''.$i.' hours'));
                if(isset($output[$i]))
                    $dataset[] = count($output[$i]);
                else
                    $dataset[] = 0;
            }

            $chartData = [
                'labels' => array_reverse($labels),
                'datasets' => [
                    [
                        'label'=> $label,
                        'data' => array_values(array_reverse($dataset)),
                        'backgroundColor' => [
                            'rgba(54, 162, 235, 0.2)'
                        ],
                        'borderColor' => [
                            'rgba(54, 162, 235, 1)'
                        ],
                        'borderWidth' => 1
                    ]
                ]
            ];
        }



        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'chartData' => $chartData
        ]);
    }

    /**
     * Finds the Tasks model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Tasks the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Tasks::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app/modules/tasks', 'The requested page does not exist.'));
    }
}
