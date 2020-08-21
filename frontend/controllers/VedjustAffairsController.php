<?php

namespace frontend\controllers;

use Yii;
use frontend\models\VedjustVed;
use frontend\models\VedjustAffairs;
use frontend\models\VedjustIssuance;
use frontend\models\VedjustAffairsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * VedjustAffairsController implements the CRUD actions for VedjustAffairs model.
 */
class VedjustAffairsController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'create', 'delete', 'update', 'changestatus', 'changestatusall', 'issuance', 'view', 'check-affairs-barcode'],
                        'roles' => ['editMfc', 'editZkp', 'editRosreestr', 'confirmExtDocs', 'editArchive'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'view'],
                        'roles' => ['addAudit', 'limitAudit'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'changestatus' => ['GET'],
                    'changestatusall' => ['GET'],
                ],
            ],
        ];
    }

    /**
     * Lists all VedjustAffairs models.
     * @return mixed
     */
    public function actionIndex($id)
    {
        $searchModel = new VedjustAffairsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $model = new VedjustAffairs();
        $modelVed = VedjustVed::findOne($id);

        if ($modelVed === NULL) {
            return $this->goHome();
        }

        $dataProvider->pagination->pageSize = 200;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'storage' => $model->getStoragePath($id),
            'idVed' => $id,
            'model' => $model,
            'modelVed' => $modelVed,
        ]);
    }

    /**
     * Displays a single VedjustAffairs model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new VedjustAffairs model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionCreate($id)
    {
        if (!is_numeric($id) || $id < 1 || is_float($id)) {
            return $this->goHome();
        }

        $modelVed = VedjustVed::findOne($id);

        if ($modelVed !== null) {
            if ($modelVed->status_id !== 1 || $modelVed->user_created_id !== Yii::$app->user->identity->id) {
                throw new ForbiddenHttpException('Вы не можете получить доступ к этой странице.');
            }
        }

        $model = new VedjustAffairs();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('successAffairs', 'block');

            return $this->render('create', [
                'model' => new VedjustAffairs(),
                'vedId' => $id,
                'kuvd' => '<br>'. $model->ref_num . '<br>' . $model->kuvd,
            ]);
        }

        Yii::$app->getSession()->setFlash('successAffairs', 'none');

        return $this->render('create', [
            'model' => $model,
            'vedId' => $id,
            'kuvd' => '',
        ]);
    }

    /**
     * Check of receipt an affairs with barcode scanner
     * If accept is successful, the browser will show a message.
     * @return mixed
     */
    public function actionCheckAffairsBarcode($id)
    {
        if (!is_numeric($id) || $id < 1 || is_float($id)) {
            return $this->goHome();
        }

        $modelVed = VedjustVed::findOne($id);
        $model = new VedjustAffairs();

        if ($modelVed === NULL || !$model->checkPermitAffairsBarcode($modelVed)) {
            throw new ForbiddenHttpException('Вы не можете получить доступ к этой странице.');
        }

        if ($model->load(Yii::$app->request->post())) {
            Yii::$app->getSession()->setFlash('successCheckAffairsBarcode', 'block');

            return $this->render('checkAffairsBarcode', [
                'model' => new VedjustAffairs(),
                'vedId' => $id,
                'barcode' => $model->barcode,
                'status' => $model->statusAffairsBarcode($model),
            ]);
        }

        Yii::$app->getSession()->setFlash('successCheckAffairsBarcode', 'none');

        return $this->render('checkAffairsBarcode', [
            'model' => $model,
            'vedId' => $id,
            'barcode' => '',
            'status' => '',
        ]);
    }

    /**
     * Updates an existing VedjustAffairs model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        // if ($model->user_created_id === Yii::$app->user->identity->id || $model->ved->address_id === Yii::$app->user->identity->address_id) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['index', 'id' => $model->ved_id]);
            }

            return $this->render('update', [
                'model' => $model,
            ]);
        // } else {
            // throw new ForbiddenHttpException('Вы не можете получить доступ к этой странице.');
        // }
    }

    public function actionIssuance($id)
    {
        $numIssuance = VedjustIssuance::find()->select(['count(*) num'])->where(['affairs_id' => $id])->asArray()->one()["num"];
        ($numIssuance > 0) ? $nameIssuance = VedjustIssuance::find()->select(['name'])->where(['affairs_id' => $id])->asArray()->all() : $nameIssuance = [];
        $model = $this->findModel($id);
        $modelIssuance = new VedjustIssuance();

        if (($model->ved->status_id === 5 || $model->ved->status_id === 6)
            && $model->status === 1
            // && $numIssuance !== $model->p_count
            && $model->getCheckAffairsIssuance($model->ved_id)
            && Yii::$app->user->can('issuance')
        ) {
            if ($modelIssuance->load(Yii::$app->request->post()) && $modelIssuance->save()) {
                return $this->redirect(['index', 'id' => $model->ved_id]);
            }

            return $this->renderAjax('createIssuance', [
                'modelIssuance' => $modelIssuance,
                'idVed' => $id,
                'numIssuance' => $numIssuance,
                'p_count' => $model->p_count,
                'nameIssuance' => $nameIssuance,
            ]);
        }

        return $this->redirect(['index', 'id' => $model->ved_id]);
    }

    /**
     * Deletes an existing VedjustAffairs model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->ved->status_id !== 1 || $model->user_created_id !== Yii::$app->user->identity->id)
        {
            throw new ForbiddenHttpException('Вы не можете получить доступ к этой странице.');
        }

        $model->delete();

        return $this->redirect(['index', 'id' => $model->ved_id]);
    }

    // Check box
    public function actionChangestatus($id, $status)
    {
        $model = $this->findModel($id);

        if ($model->isCheckBoxDisabled($model->ved)) {
            return 0;
        } else {
            if (!$model->status) {
                $model->status = 1;
                $model->date_status = date('Y-m-d H:i:s');
                $model->accepted_ip = ip2long(Yii::$app->request->userIP);
                $model->user_accepted_id = Yii::$app->user->identity->id;
            } else {
                $model->status = 0;
                $model->date_status = NULL;
                $model->accepted_ip = NULL;
                $model->user_accepted_id = NULL;
            }

            if ($model->update() !== false) {
                return 1;
            } else {
                return 0;
            }
        }
    }

    public function actionChangestatusall($id, $status)
    {
        $modelVed = $this->findModelVed($id);
        $model = new VedjustAffairs();

        if ($model->isCheckBoxDisabled($modelVed)) {
            return 0;
        } else {
            if (!$model->status) {
                VedjustAffairs::updateAll([
                    'status' => 1,
                    'date_status' => date('Y-m-d H:i:s'),
                    'accepted_ip' => ip2long(Yii::$app->request->userIP),
                    'user_accepted_id' => Yii::$app->user->identity->id,
                ],
                ['=', 'ved_id', $id]);
            }

            return 1;
        }
    }


    /**
     * Finds the VedjustAffairs model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return VedjustAffairs the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = VedjustAffairs::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function findModelVed($id)
    {
        if (($model = VedjustVed::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}