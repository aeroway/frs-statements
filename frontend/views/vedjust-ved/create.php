<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model frontend\models\VedjustVed */

$this->title = 'Создать ведомость';
$this->params['breadcrumbs'][] = ['label' => 'Ведомости', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vedjust-ved-create">

    <h2><?php /* echo Html::encode($this->title) */ ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
