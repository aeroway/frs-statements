<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model frontend\models\VedjustStorage */

$this->params['breadcrumbs'][] = ['label' => 'Ведомости', 'url' => ['vedjust-ved/index']];
$this->title = 'Создать';
//$this->params['breadcrumbs'][] = ['label' => 'Архивохранилище', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vedjust-storage-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
