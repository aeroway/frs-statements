<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\VedjustVed */

$this->title = 'Редактировать';
$this->params['breadcrumbs'][] = ['label' => 'Ведомости', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="vedjust-ved-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
