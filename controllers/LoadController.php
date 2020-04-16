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
 * LoadController implements the CRUD actions for Tasks model.
 */
class LoadController extends Controller
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
                'class' => VerbFilter::class,
                'actions' => [
                    'index' => ['GET'],
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
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
                'class' => AccessControl::class,
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
     * Lists all Visitors models.
     * @return mixed
     */
    public function actionIndex()
    {
        $labels = [];
        $output = [];

        $elapsed_time_summ = [];
        $elapsed_time_avrg = [];

        $memory_usage_summ = [];
        $memory_usage_avrg = [];

        $db_queries_summ = [];
        $db_queries_avrg = [];

        $db_time_summ = [];
        $db_time_avrg = [];

        $searchModel = new VisitorsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $visitors = $dataProvider->query->all();
        $module = $this->module;

        if ($module->useChart && $searchModel->viewChart && ($searchModel->period == 'today' || $searchModel->period == 'yesterday' || $searchModel->period == 'week' || $searchModel->period == 'month' || $searchModel->period == 'year')) {

            $dateTime = new \DateTime(null, new \DateTimeZone(ini_get('date.timezone')));
            $timestamp = $dateTime->getTimestamp();

            if ($searchModel->period == 'today') {
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
                        $output[$i][] = $visitor->params;

                }
            }

            for ($i = 1; $i <= $iterations; $i++) {

                if ($searchModel->period == 'year')
                    $labels[] = date($format, strtotime('-'.($i).' '.$metrik, $timestamp));
                else
                    $labels[] = date($format, strtotime('-'.($i+1).' '.$metrik, $timestamp));

                if (isset($output[$i])) {

                    $et = 0;
                    $et_count = 0;
                    foreach ($output[$i] as $item) {
                        if (isset($item['et'])) {
                            $et += $item['et'];
                            $et_count++;
                        }
                    }
                    $elapsed_time_summ[] = round($et, 4);
                    $elapsed_time_avrg[] = round((($et_count) ? ($et / $et_count) : $et), 4);

                    $mu = 0;
                    $mu_count = 0;
                    foreach ($output[$i] as $item) {
                        if (isset($item['mu'])) {
                            $mu += $item['mu'];
                            $mu_count++;
                        }
                    }
                    $memory_usage_summ[] = round($mu, 2);
                    $memory_usage_avrg[] = round((($mu_count) ? ($mu / $mu_count) : $mu), 2);

                    $dbq = 0;
                    $dbq_count = 0;
                    foreach ($output[$i] as $item) {
                        if (isset($item['dbq'])) {
                            $dbq += $item['dbq'];
                            $dbq_count++;
                        }
                    }
                    $db_queries_summ[] = round($dbq, 4);
                    $db_queries_avrg[] = round((($dbq_count) ? ($dbq / $dbq_count) : $dbq), 4);

                    $dbt = 0;
                    $dbt_count = 0;
                    foreach ($output[$i] as $item) {
                        if (isset($item['dbt'])) {
                            $dbt += $item['dbt'];
                            $dbt_count++;
                        }
                    }
                    $db_time_summ[] = round($dbt, 4);
                    $db_time_avrg[] = round((($dbt_count) ? ($dbt / $dbt_count) : $dbt), 4);

                }

            }

            $chartDataSumm = [
                'server' => [
                    'labels' => array_reverse($labels),
                    'datasets' => [
                        [
                            'label'=> Yii::t('app/modules/stats', 'Elapsed time, sec.'),
                            'data' => array_reverse($elapsed_time_summ),
                            'backgroundColor' => [
                                'rgba(118, 207, 41, 0.2)'
                            ],
                            'borderColor' => [
                                'rgba(101, 176, 34, 1)'
                            ],
                            'borderWidth' => 1
                        ],
                        [
                            'label'=> Yii::t('app/modules/stats', 'Memory usage, Mb'),
                            'data' => array_reverse($memory_usage_summ),
                            'backgroundColor' => [
                                'rgba(251, 163, 35, 0.2)'
                            ],
                            'borderColor' => [
                                'rgba(213, 139, 29, 1)'
                            ],
                            'borderWidth' => 1
                        ]
                    ]
                ],
                'db' => [
                    'labels' => array_reverse($labels),
                    'datasets' => [
                        [
                            'label'=> Yii::t('app/modules/stats', 'DB queries'),
                            'data' => array_reverse($db_queries_summ),
                            'backgroundColor' => [
                                'rgba(65, 148, 226, 0.2)'
                            ],
                            'borderColor' => [
                                'rgba(50, 126, 192, 1)'
                            ],
                            'borderWidth' => 1
                        ],
                        [
                            'label'=> Yii::t('app/modules/stats', 'DB time, sec.'),
                            'data' => array_reverse($db_time_summ),
                            'backgroundColor' => [
                                'rgba(146, 61, 253, 0.2)'
                            ],
                            'borderColor' => [
                                'rgba(124, 51, 215, 1)'
                            ],
                            'borderWidth' => 1
                        ]
                    ]
                ]
            ];

            $chartDataAvrg = [
                'server' => [
                    'labels' => array_reverse($labels),
                    'datasets' => [
                        [
                            'label'=> Yii::t('app/modules/stats', 'Elapsed time, sec.'),
                            'data' => array_reverse($elapsed_time_avrg),
                            'backgroundColor' => [
                                'rgba(118, 207, 41, 0.2)'
                            ],
                            'borderColor' => [
                                'rgba(101, 176, 34, 1)'
                            ],
                            'borderWidth' => 1
                        ],
                        [
                            'label'=> Yii::t('app/modules/stats', 'Memory usage, Mb'),
                            'data' => array_reverse($memory_usage_avrg),
                            'backgroundColor' => [
                                'rgba(251, 163, 35, 0.2)'
                            ],
                            'borderColor' => [
                                'rgba(213, 139, 29, 1)'
                            ],
                            'borderWidth' => 1
                        ]
                    ]
                ],
                'db' => [
                    'labels' => array_reverse($labels),
                    'datasets' => [
                        [
                            'label'=> Yii::t('app/modules/stats', 'DB queries'),
                            'data' => array_reverse($db_queries_avrg),
                            'backgroundColor' => [
                                'rgba(65, 148, 226, 0.2)'
                            ],
                            'borderColor' => [
                                'rgba(50, 126, 192, 1)'
                            ],
                            'borderWidth' => 1
                        ],
                        [
                            'label'=> Yii::t('app/modules/stats', 'DB time, sec.'),
                            'data' => array_reverse($db_time_avrg),
                            'backgroundColor' => [
                                'rgba(146, 61, 253, 0.2)'
                            ],
                            'borderColor' => [
                                'rgba(124, 51, 215, 1)'
                            ],
                            'borderWidth' => 1
                        ]
                    ]
                ]
            ];

            $dataProvider = [
                'elapsed_time_summ' => array_sum($elapsed_time_summ),
                'elapsed_time_avrg' => round(array_sum($elapsed_time_avrg) / (($elapsed_time_avrg) ? count($elapsed_time_avrg) : 1), 4),
                'memory_usage_summ' => array_sum($memory_usage_summ),
                'memory_usage_avrg' => round(array_sum($memory_usage_avrg) / (($memory_usage_avrg) ? count($memory_usage_avrg) : 1), 2),
                'db_queries_summ' => array_sum($db_queries_summ),
                'db_queries_avrg' => round(array_sum($db_queries_avrg) / (($db_queries_avrg) ? count($db_queries_avrg) : 1), 4),
                'db_time_summ' => array_sum($db_time_summ),
                'db_time_avrg' => round(array_sum($db_time_avrg) / (($db_time_avrg) ? count($db_time_avrg) : 1), 4),
            ];
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'chartDataSumm' => $chartDataSumm,
            'chartDataAvrg' => $chartDataAvrg,
            'dataProvider' => $dataProvider,
            'module' => $module
        ]);
    }

}
