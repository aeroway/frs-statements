<?php

namespace frontend\controllers;

use Yii;
use frontend\models\VedjustVed;
use frontend\models\VedjustArea;
use frontend\models\VedjustVedSearch;
use frontend\models\VedjustExtDocSearch;
use frontend\models\VedjustAffairs;
use frontend\models\VedjustExtDoc;
use yii\web\UploadedFile;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * VedjustVedController implements the CRUD actions for VedjustVed model.
 */
class VedjustVedController extends Controller
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
                        'actions' =>
                        [
                            'index', 'delete', 'create', 'view', 'update', // standard actions
                            'changestatus', // formed docs
                            'changestatusreturn', // return status step back
                            'changeverified', // accepted docs
                            'createvedpdf', // create pdf
                            'setarchive', // show archive docs
                            'reset', // reset filters
                            'fill-area', // get list of districts
                            'createcopy', // create a duplicate of ved and affairs
                        ],
                        'roles' => ['editMfc', 'editZkp', 'editRosreestr', 'confirmExtDocs', 'editArchive'],
                    ],
                    [
                        'allow' => true,
                        'actions' => 
                        [
                            'index',
                            'setarchive',
                            'view-ext-doc', // thumbnail previews
                            'index-ext-doc-detailed',
                            'view-ext-doc-detailed', // detail view
                            'reset',
                        ],
                        'roles' => ['audit', 'addAudit', 'limitAudit'],
                    ],
                    [
                        'allow' => true,
                        'actions' => 
                        [
                            'create-ext-doc-pdf', // create pdf
                            'ext-doc-accepted', // accepted an ext.ter. documents
                            'send-ext-docs', // create ext.ter. documents
                            'view-ext-doc',
                            'index-ext-doc-detailed',
                        ],
                        'roles' => ['confirmExtDocs'],
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    throw new ForbiddenHttpException('Необходимо подтверждение учётной записи, либо расширение полномочий через администратора системы.');
                }
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all VedjustVed models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new VedjustVedSearch();

        $params = Yii::$app->request->queryParams;

        if (count($params) <= 0) {
            $params = Yii::$app->session['VedjustVedSearch'];
            if(isset(Yii::$app->session['VedjustVedSearch']['page']))
                $_GET['page'] = Yii::$app->session['VedjustVedSearch']['page'];
        } else {
            Yii::$app->session['VedjustVedSearch'] = $params;
        }

        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single VedjustVed model.
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
     * Lists all VedjustExtDoc models.
     * @return mixed
     */
    public function actionViewExtDoc()
    {
        $modelExtDoc = new VedjustExtDoc();

        return $this->render('viewExtDoc', [
            'modelExtDoc' => $modelExtDoc,
        ]);
    }

    /**
     * Lists all VedjustExtDoc models.
     * @return mixed
     */
    public function actionIndexExtDocDetailed($loc)
    {
        $modelExtDoc = new VedjustExtDoc();
        $searchModel = new VedjustExtDocSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('indexExtDocDetailed', [
            'dataProvider' => $dataProvider,
            'modelExtDoc' => $modelExtDoc,
            'loc' => $loc,
        ]);
    }

    /**
     * Displays a single VedjustVed model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionViewExtDocDetailed($id)
    {
        return $this->render('viewExtDocDetailed', [
            'model' => $this->findModelExtDoc($id),
        ]);
    }

    /**
     * Creates a new VedjustVed model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new VedjustVed();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->file = UploadedFile::getInstance($model, 'file');
            $model->pkpvd_xlsx = UploadedFile::getInstance($model, 'pkpvd_xlsx');

            if ($model->file) {
                if ($this->batchImportAffairs($model)) {
                    return $this->redirect(['vedjust-affairs/index', 'id' => $model->id]);
                }
            }

            if ($model->pkpvd_xlsx) {
                if ($this->importPkpvdXlsx($model)) {
                    return $this->redirect(['vedjust-affairs/index', 'id' => $model->id]);
                }
            }

            return $this->redirect(['vedjust-affairs/create', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new VedjustVed model as a copy of the previous.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionCreatecopy($id)
    {
        $modelVed = new VedjustVed();


        if ($modelVed->load(Yii::$app->request->post()) && $modelVed->save()) {

            $modelSource = $this->findModel($id);
            $modelSource = VedjustAffairs::find()
                ->where(['ved_id' => $id])
                ->orderBy(['id' => SORT_ASC])
                ->all();

            foreach ($modelSource as $affairs) {
                $modelAffairs = new VedjustAffairs();
                $modelAffairs->status = 0;
                $modelAffairs->date_create = date('Y-m-d');
                $modelAffairs->comment = $affairs->comment;
                $modelAffairs->kuvd = $affairs->kuvd;
                $modelAffairs->ved_id = $modelVed->id;
                $modelAffairs->create_ip = ip2long(Yii::$app->request->userIP);
                $modelAffairs->user_created_id = Yii::$app->user->identity->id;
                $modelAffairs->ref_num = $affairs->ref_num;
                $modelAffairs->save();
            }

            return $this->redirect(['vedjust-affairs/index', 'id' => $modelVed->id]);
        }

        return $this->render('createcopy', [
            'model' => $modelVed,
            'copy' => true,
        ]);
    }

    /**
     * Updates an existing VedjustVed model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (!($model->status_id === 1 && $model->user_created_id === Yii::$app->user->identity->id)) {
            throw new ForbiddenHttpException('Вы не можете редактировать чужие записи.');
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->address_id != 393) {
                $model->area_id = NULL;
            }

            if ($model->save()) {
                $model->file = UploadedFile::getInstance($model, 'file');
                $model->pkpvd_xlsx = UploadedFile::getInstance($model, 'pkpvd_xlsx');

                if ($model->file) {
                    if ($this->batchImportAffairs($model)) {
                        return $this->redirect(['vedjust-affairs/index', 'id' => $model->id]);
                    }
                }

                if ($model->pkpvd_xlsx) {
                    if ($this->importPkpvdXlsx($model)) {
                        return $this->redirect(['vedjust-affairs/index', 'id' => $model->id]);
                    }
                }

                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing VedjustVed model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if($model->verified)
        {
            throw new ForbiddenHttpException('Вы не можете удалить сформированную заявку.');
        }

        if ($model->user_created_id !== Yii::$app->user->identity->id)
        {
            throw new ForbiddenHttpException('Вы не можете удалить чужую заявку.');
        }

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    // Action buttons 'Принято'
    public function actionChangeverified($id)
    {
        $model = $this->findModel($id);

        $count0 = VedjustAffairs::find()
                    ->where(['and', ['ved_id' => $id], ['status' => 0]])
                    ->count();

        $count1 = VedjustAffairs::find()
                    ->where(['and', ['ved_id' => $id], ['status' => 1]])
                    ->count();

        if($count0 == 0) {
            $model->status_id = 3;
        } elseif(($count0 > 0) && ($count1 > 0)) {
            $model->status_id = 4;
        } else {
            return 0;
        }

        $model->verified = 1;
        $model->date_reception = date('Y-m-d H:i:s');
        $model->accepted_ip = ip2long(Yii::$app->request->userIP);
        $model->user_accepted_id = Yii::$app->user->identity->id;

        if ($model->update() !== false) {
            return 1;
        } else {
            return 0;
        }
    }

    // Button action "Сформировать"
    public function actionChangestatus($id)
    {
        $model = $this->findModel($id);

        if ($model->status_id === 1 && $model->user_created_id === Yii::$app->user->identity->id)
        {
            // Пропуск одной стадии, если создаёт ведомость с типом "невостреб." сам на себя
            if ($model->user_created_id === Yii::$app->user->identity->id
                && $model->archive_unit_id === 4 
                && $model->address_id === Yii::$app->user->identity->address_id) {

                VedjustAffairs::updateAll([
                    'status' => 1,
                    'date_status' => date('Y-m-d H:i:s'),
                    'accepted_ip' => ip2long(Yii::$app->request->userIP),
                    'user_accepted_id' => Yii::$app->user->identity->id
                ],
                ['=', 'ved_id', $id]);

                $model->status_id = 3;
                $model->verified = 1;
                $model->date_reception = date('Y-m-d H:i:s');
                $model->accepted_ip = ip2long(Yii::$app->request->userIP);
                $model->user_accepted_id = Yii::$app->user->identity->id;

            } else {
                $model->status_id = 2;
            }

            $model->date_formed = date('Y-m-d H:i:s');
            $model->formed_ip = ip2long(Yii::$app->request->userIP);
            $model->user_formed_id = Yii::$app->user->identity->id;

            if ($model->update() !== false) {
                return 1;
            } else {
                return 0;
            }
        }
        else
        {
           return 0; 
        }
    }

    // Button action "Откатить"
    public function actionChangestatusreturn($id)
    {
        $model = $this->findModel($id);

        if ($model->user_formed_id === Yii::$app->user->identity->id && $model->status_id == 2)
        {
            $model->status_id = 1;

            foreach ($model->affairs as $affairs)
            {
                $affairs->status = 0;
                $affairs->date_status = NULL;
                $affairs->accepted_ip = NULL;
                $affairs->user_accepted_id = NULL;
                $affairs->update();
            }

            return $this->updateModel($model);

        } elseif ($model->user_accepted_id == Yii::$app->user->identity->id && ($model->status_id == 3 || $model->status_id == 4)) {

            //2019.08.05 запрет откатывать принятые ведомости
            return 0;

            $model->status_id = 2;
            $model->verified = NULL;
            $model->user_accepted_id = NULL;
            $model->date_reception = NULL;
            $model->accepted_ip = NULL;

            return $this->updateModel($model);

        } else {

           return 0;

        }
    }

    public function actionSendExtDocs($id)
    {
        $modelVed = $this->findModel($id);

        foreach ($modelVed->affairs as $value)
        {
            $modelExtDoc = new VedjustExtDoc();

            if ($modelExtDoc->load(Yii::$app->request->post())) {

                $modelExtDoc->affairs_id = $value->id;
                $modelExtDoc->date_create = date('Y-m-d H:i:s');
                $modelExtDoc->create_ip = ip2long(Yii::$app->request->userIP);
                $modelExtDoc->user_created_id = Yii::$app->user->identity->id;
                $modelExtDoc->save();
            } else {
                return $this->renderAjax('createExtDoc', [
                    'modelExtDoc' => $modelExtDoc,
                ]);
            }
        }

        $modelVed->ext_reg_created = 1;

        if ($modelVed->update() !== false) {
            return $this->redirect(['index']);
        } else {
            return 0;
        }
    }

    // create pdf file
    public function actionCreatevedpdf()
    {
        $model = $this->findModel(Yii::$app->request->get('id'));
        $model->getVedPdf();
    }

    // create pdf file
    public function actionCreateExtDocPdf($loc)
    {
        $model = new VedjustExtDoc();
        foreach ($model->getExtDocsPdf($loc) as $value) {
            $modelExtDocs = $this->findModelExtDoc($value);
            $modelExtDocs->date_formed = date('Y-m-d H:i:s');
            $modelExtDocs->formed_ip = ip2long(Yii::$app->request->userIP);
            $modelExtDocs->user_formed_id = Yii::$app->user->identity->id;
            $modelExtDocs->update();
        }

        exit();
    }

    // set accepted
    public function actionExtDocAccepted($loc)
    {
        $model = new VedjustExtDoc();
        foreach ($model->getExtDocsAccepted($loc) as $value) {
            $modelExtDocs = $this->findModelExtDoc($value);
            $modelExtDocs->date_reception = date('Y-m-d H:i:s');
            $modelExtDocs->accepted_ip = ip2long(Yii::$app->request->userIP);
            $modelExtDocs->user_accepted_id = Yii::$app->user->identity->id;
            $modelExtDocs->update();
        }

        return $this->redirect(['view-ext-doc']);
    }

    // show all records
    public function actionSetarchive($status)
    {
        $cookie = new yii\web\Cookie([
            'name' => 'archive',
            'value' => $status
        ]);

        Yii::$app->getResponse()->getCookies()->add($cookie);

        return 1;
    }

    // reset session
    public function actionReset()
    {
        Yii::$app->session['VedjustVedSearch'] = '';
        return $this->redirect(['index']);
    }

    public function actionFillArea()
    {
        $listArea = VedjustArea::find()->select(['id', 'name'])->all();
        $area = '';

        foreach ($listArea as $value) {
            $area .= "<option value='$value->id'>$value->name</option>";
        }

        return $area;
    }

    /**
     * Finds the VedjustVed model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return VedjustVed the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = VedjustVed::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Finds the VedjustExtDoc model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return VedjustExtDoc the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModelExtDoc($id)
    {
        if (($model = VedjustExtDoc::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    private function updateModel($model)
    {
        if ($model->update() !== false) {
            return 1;
        } else {
            return 0;
        }
    }

    public function convertToUTF8($text) {
        $encoding = mb_detect_encoding($text, mb_detect_order(), false);
    
        if ($encoding == "UTF-8") {
            $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');    
        }

        $out = iconv(mb_detect_encoding($text, mb_detect_order(), false), "UTF-8//IGNORE", $text);

        return $out;
    }

    private function batchImportAffairs($model) {
            $file_import = 'uploads/' . 'ved-import.csv'; //date('YmdHis') . '-' . $model->file->name;
            $model->file->saveAs($file_import);
            $handle = fopen($file_import, 'r');

            if ($handle) {
                while (($line = fgetcsv($handle, 0, ";")) != FALSE) {
                    $bulkInsertArray[] = [
                        'ref_num' => $this->convertToUTF8($line[0]),
                        'kuvd' => $this->convertToUTF8(preg_replace('/[^0-9\/, ]{5}/i', '', $line[4])),
                        'date_create' => date('Y-m-d H:i:s'),
                        'user_created_id' => Yii::$app->user->identity->id,
                        'create_ip' => ip2long(Yii::$app->request->userIP),
                        'ved_id' => $model->id,
                    ];
                }
                unset($bulkInsertArray[0]);

                fclose($handle);

                Yii::$app->db->createCommand()->batchInsert('affairs', 
                    ['ref_num', 'kuvd', 'date_create', 'user_created_id', 'create_ip', 'ved_id'],
                    $bulkInsertArray
                )->execute();
            }
    }

    private function importPkpvdXlsx($model) {
        $file_import = 'uploads/' . 'import-pkpvd.xlsx';

        $model->pkpvd_xlsx->saveAs($file_import);
        $handle = fopen($file_import, 'r');

        if ($handle) {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file_import);
            $worksheet = $spreadsheet->getActiveSheet()->toArray();
            $bulkInsertArray = array();

            foreach ($worksheet as $value) {
                if ($value[9] != NULL || $value[10] != NULL) {
                    $bulkInsertArray[] = [
                        'ref_num' => $value[6],
                        'kuvd' => $value[10],
                        'comment' => $value[9],
                        'date_create' => date('Y-m-d H:i:s'),
                        'user_created_id' => Yii::$app->user->identity->id,
                        'create_ip' => ip2long(Yii::$app->request->userIP),
                        'ved_id' => $model->id,
                    ];
                }
                
            }

            if ($bulkInsertArray[0]["ref_num"] == 'Внутренний номер обращения' 
                    && $bulkInsertArray[0]["kuvd"] == 'Номера КУВД/КУВИ'
                    && $bulkInsertArray[0]["comment"] == 'Номер пакета') {
                $result = true;
            } else {
                $result = false;
            }

            unset($bulkInsertArray[0]);
        }

        fclose($handle);

        Yii::$app->db->createCommand()->batchInsert('affairs', 
            ['ref_num', 'kuvd', 'comment', 'date_create', 'user_created_id', 'create_ip', 'ved_id'],
            $bulkInsertArray
        )->execute();

        return $result;
    }
}