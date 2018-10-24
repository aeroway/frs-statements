<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model frontend\models\VedjustArchive */

$this->params['breadcrumbs'][] = ['label' => 'Ведомости', 'url' => ['vedjust-ved/index']];
//$this->params['breadcrumbs'][] = ['label' => 'Архивохранилище', 'url' => ['vedjust-storage/index']];
$this->title = 'Создать архивохранилище';
$this->params['breadcrumbs'][] = ['label' => 'Справочник архивохранилища', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vedjust-archive-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
