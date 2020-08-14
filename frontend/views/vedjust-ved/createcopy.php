<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model frontend\models\VedjustVed */

$this->title = 'Создать ведомость с копированием';
$this->params['breadcrumbs'][] = ['label' => 'Ведомости', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vedjust-ved-createcopy">

    <h2><?php /* echo Html::encode($this->title) */ ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
        'copy' => true,
    ]) ?>

</div>
