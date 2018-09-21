<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model frontend\models\ExtDoc */

$this->title = 'Переместить';
$this->params['breadcrumbs'][] = ['label' => 'Ведомости', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vedjust-ved-create-ext-doc">

    <h2><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_formExtDoc', [
        'model' => $modelExtDoc,
    ]) ?>

</div>
