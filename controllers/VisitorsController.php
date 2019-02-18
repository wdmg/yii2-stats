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
        $chartData = [];
        $labels = [];
        $all_visitors = [];
        $unique_visitors = [];
        $output1 = [];
        $output2 = [];

        $searchModel = new VisitorsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $visitors = $dataProvider->query->all();

        $module = Yii::$app->getModule('stats');
        $clientPlatforms = $module->clientPlatforms;
        $clientBrowsers = $module->clientBrowsers;

        $locale = \Locale::getPrimaryLanguage(Yii::$app->language); // Get short locale string
        if(!$locale)
            $locale = 'en';

        try {
            $reader = new \GeoIp2\Database\Reader(__DIR__ .'/../database/GeoLite2-Country.mmdb', [$locale]);
        } catch (Exception $e) {
            $reader = null;
            Yii::warning($e->getMessage());
        }

        if ($module->useChart && ($searchModel->period == 'today' || $searchModel->period == 'yesterday' || $searchModel->period == 'week' || $searchModel->period == 'month' || $searchModel->period == 'year')) {

            $dateTime = new \DateTime('00:00:00');
            $timestamp = $dateTime->getTimestamp();

            if($searchModel->period == 'today') {
                $format = 'H:i';
                $metrik = 'hours';
                $iterations = 24;
                $timestamp = $dateTime->modify('+1 day')->getTimestamp();
            } else if($searchModel->period == 'yesterday') {
                $format = 'H:i';
                $metrik = 'hours';
                $iterations = 24;
                $timestamp = $dateTime->getTimestamp();
            } else if($searchModel->period == 'week') {
                $format = 'd M';
                $metrik = 'days';
                $iterations = 7;
                $timestamp = $dateTime->modify('+1 day')->getTimestamp();
            } else if($searchModel->period == 'month') {
                $format = 'd M';
                $metrik = 'days';
                $iterations = 31;
                $timestamp = $dateTime->modify('+1 day')->getTimestamp();
            } else if($searchModel->period == 'year') {
                $format = 'M, Y';
                $metrik = 'months';
                $iterations = 12;
                $timestamp = $dateTime->modify('+1 month')->getTimestamp();
            }

            foreach ($visitors as $visitor) {
                for ($i = 1; $i <= $iterations; $i++) {

                    if($visitor->datetime <= strtotime('-'.$i.' '.$metrik, $timestamp) && $visitor->datetime > strtotime('-'.($i + 1).' '.$metrik, $timestamp))
                        $output1[$i][] = $visitor->datetime;

                    if($visitor->unique == 1 && $visitor->datetime <= strtotime('-'.$i.' '.$metrik, $timestamp) && $visitor->datetime > strtotime('-'.($i + 1).' '.$metrik, $timestamp))
                        $output2[$i][] = $visitor->datetime;

                }
            }

            for ($i = 1; $i <= $iterations; $i++) {

                if($searchModel->period == 'year')
                    $labels[] = date($format, strtotime('-'.($i).' '.$metrik, $timestamp));
                else
                    $labels[] = date($format, strtotime('-'.($i+1).' '.$metrik, $timestamp));

                if(isset($output1[$i]))
                    $all_visitors[] = count($output1[$i]);
                else
                    $all_visitors[] = 0;

                if(isset($output2[$i]))
                    $unique_visitors[] = count($output2[$i]);
                else
                    $unique_visitors[] = 0;
            }


            $chartData = [
                'labels' => array_reverse($labels),
                'datasets' => [
                    [
                        'label'=> 'Visitors',
                        'data' => array_values(array_reverse($all_visitors)),
                        'backgroundColor' => [
                            'rgba(54, 162, 235, 0.2)'
                        ],
                        'borderColor' => [
                            'rgba(54, 162, 235, 1)'
                        ],
                        'borderWidth' => 1
                    ],
                    [
                        'label'=> 'Unique',
                        'data' => array_values(array_reverse($unique_visitors)),
                        'backgroundColor' => [
                            'rgba(255, 99, 132, 0.2)'
                        ],
                        'borderColor' => [
                            'rgba(255,99,132,1)'
                        ],
                        'borderWidth' => 1
                    ]
                ]
            ];
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'clientPlatforms' => $clientPlatforms,
            'clientBrowsers' => $clientBrowsers,
            'chartData' => $chartData,
            'module' => $module,
            'reader' => $reader
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
