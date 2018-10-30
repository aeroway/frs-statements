<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model frontend\models\VedjustAffairs */

$this->params['breadcrumbs'][] = ['label' => 'Ведомости', 'url' => ['vedjust-ved/index']];
$this->title = 'Создать';
$this->params['breadcrumbs'][] = ['label' => 'Дела', 'url' => ['index', 'id' => $idVed]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vedjust-affairs-create-issuance">

    <h1><?php //echo Html::encode($this->title) ?></h1>

    <?= $this->render('_formIssuance', [
        'modelIssuance' => $modelIssuance,
        'idVed' => $idVed,
        'numIssuance' => $numIssuance,
        'p_count' => $p_count,
        'nameIssuance' => $nameIssuance,
    ]) ?>

</div>
