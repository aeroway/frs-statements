<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\VedjustAffairs */

$this->params['breadcrumbs'][] = ['label' => 'Ведомости', 'url' => ['vedjust-ved/index']];
$this->title = 'Редактировать';
$this->params['breadcrumbs'][] = ['label' => 'Дела', 'url' => ['index', 'id' => $model->ved_id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="vedjust-affairs-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'vedId' => $model->ved_id,
    ]) ?>

</div>
