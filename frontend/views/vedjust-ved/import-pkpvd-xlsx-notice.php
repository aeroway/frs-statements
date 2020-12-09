<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model frontend\models\VedjustVed */

$this->title = 'Импорт списка обращений';
$this->params['breadcrumbs'][] = ['label' => 'Ведомости', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="vedjust-ved-import-pkpvd-xlsx-notice">

    <?php if (Yii::$app->session->hasFlash('successImportPkpvdXlsxNotice')): ?>
        <div class="alert alert-success alert-dismissable" role="alert">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
            <?= Yii::$app->session->getFlash('successImportPkpvdXlsxNotice') ?>
        </div>
    <?php endif ?>

    <?php if (Yii::$app->session->hasFlash('errorImportPkpvdXlsxNotice')): ?>
        <div class="alert alert-danger alert-dismissable" role="alert">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
            <?= Yii::$app->session->getFlash('errorImportPkpvdXlsxNotice') ?>
        </div>
    <?php endif ?>

    <?= $this->render('_formImportPkpvdXlsxNotice', [
        'model' => $model,
    ]) ?>

</div>
