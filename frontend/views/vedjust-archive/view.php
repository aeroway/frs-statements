<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\VedjustArchive */

$this->params['breadcrumbs'][] = ['label' => 'Ведомости', 'url' => ['vedjust-ved/index']];
//$this->params['breadcrumbs'][] = ['label' => 'Архивохранилище', 'url' => ['vedjust-storage/index']];
$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Справочник архивохранилища', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vedjust-archive-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if ($model->subdivision_id === Yii::$app->user->identity->subdivision_id) : ?>
            <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php endif; ?>
        <?php /*echo Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ])*/ ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            'name',
            'userCreated.username',
            'agency.name',
            'subject.name',
            'subdivision.name',
        ],
    ]) ?>

</div>
