<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model frontend\models\VedjustAffairs */

$this->params['breadcrumbs'][] = ['label' => 'Ведомости', 'url' => ['vedjust-ved/index']];
$this->title = 'Создать';
$this->params['breadcrumbs'][] = ['label' => 'Дела', 'url' => ['index', 'id' => !empty(Yii::$app->request->get('id')) ? Yii::$app->request->get('id') : '',]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vedjust-affairs-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
