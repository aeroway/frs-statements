<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\VedjustVed */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Ведомости', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vedjust-ved-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            [
                'attribute' => 'status.name',
                'label' => 'Состояние',
            ],
            [
                'attribute' => 'date_create',
                'format' =>  ['date', 'php:d M Y h:i:s'],
            ],
            [
                'attribute' => 'create_ip',
                'value' => function ($model) {
                    return $model->create_ip ? long2ip($model->create_ip) : '';
                },
            ],
            [
                'attribute' => 'userCreated.username',
                'label' => 'Создал',
            ],
            //'num_ved',
            'archiveUnit.name',
            [
                'attribute' => 'date_formed',
                'format' =>  ['date', 'php:d M Y h:i:s'],
            ],
            [
                'attribute' => 'formed_ip',
                'value' => function ($model) {
                    return $model->formed_ip ? long2ip($model->formed_ip) : '';
                },
            ],
            [
                'attribute' => 'userFormed.username',
                'label' => 'Сформировал',
            ],
            [
                'attribute' => 'date_reception',
                'format' =>  ['date', 'php:d M Y h:i:s'],
            ],
            [
                'attribute' => 'accepted_ip',
                'value' => function ($model) {
                    return $model->accepted_ip ? long2ip($model->accepted_ip) : '';
                },
            ],
            [
                'attribute' => 'userAccepted.username',
                'label' => 'Принял',
            ],
        ],
    ]) ?>

</div>
