<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\User */

$this->title = '';
$this->params['breadcrumbs'][] = 'Редактировать адрес';
?>

<div class="user-update-address">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form-update-address', [
        'model' => $model,
    ]) ?>

</div>