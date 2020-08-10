<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model frontend\models\VedjustAffairs */

$this->params['breadcrumbs'][] = ['label' => 'Ведомости', 'url' => ['vedjust-ved/index']];
$this->title = 'Проверить дело по штрих-коду';
$this->params['breadcrumbs'][] = ['label' => 'Проверка дел', 'url' => ['index', 'id' => $vedId,]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vedjust-affairs-check-affairs-barcode">

    <h1><?php //echo Html::encode($this->title) ?></h1>
    <div id="successCheckAffairsBarcode" style="display: <?= Yii::$app->session->getFlash('successCheckAffairsBarcode'); ?>;">
        <?php echo ($status ? '<div class="alert alert-success">Подтвержена запись: ' : '<div class="alert alert-danger">Запись отсутствует: ') . $barcode . '</div>' ?>
    </div>

    <?= $this->render('_formAffairsBarcode', [
        'model' => $model,
        'vedId' => $vedId,
    ]) ?>

</div>