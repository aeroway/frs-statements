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
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            'name',
            'hall_max',
            'rack_max',
            'locker_max',
            'shelf_max',
            'position_max',
            'userCreated.username',
            'agency.name',
            'subject.name',
            [
                'label' => 'Муниципальное образование',
                'attribute' => 'municipality'
            ],
        ],
    ]) ?>

</div>
