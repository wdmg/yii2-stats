<?php

namespace wdmg\stats\controllers;

use Yii;
use wdmg\stats\models\Robots;
use wdmg\stats\models\RobotsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * RobotsController implements the CRUD actions for Tasks model.
 */
class RobotsController extends Controller
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
        if (!Yii::$app->authManager) {
            $behaviors['access'] = [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'roles' => ['@'],
                        'allow' => true
                    ],
                ]
            ];
        } else if ($this->module->moduleExist('admin/rbac')) { // Ok, then we check access according to the rules
            $behaviors['access'] = [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['update', 'delete'],
                        'roles' => ['updatePosts'],
                        'allow' => true
                    ], [
                        'roles' => ['viewDashboard'],
                        'allow' => true
                    ],
                ],
            ];
        }

        return $behaviors;
    }

    /**
     * Lists all Robots models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RobotsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $module = $this->module;
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'module' => $module
        ]);
    }

    /**
     * Robots update model.
     * @return mixed
     */
    public function actionUpdate($id)
    {

        if(intval($id) !== 0)
            $model = self::findModel($id);
        else
            $model = new Robots();

        if ($model->load(Yii::$app->request->post())) {

            if($model->save())
                Yii::$app->getSession()->setFlash(
                    'success',
                    Yii::t('app/modules/stats', 'Information about the robot successfully updated!')
                );
            else
                Yii::$app->getSession()->setFlash(
                    'danger',
                    Yii::t('app/modules/stats', 'Error while updating information about the robot.')
                );

            return $this->redirect(['index']);
        }

        return $this->renderAjax('_form', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Robot model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the Robots model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Robots the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Robots::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app/modules/stats', 'The requested page does not exist.'));
    }
}
