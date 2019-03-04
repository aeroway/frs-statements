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
            'archiveUnit.name',
            [
                'attribute' => 'status.name',
                'label' => 'Состояние',
            ],
            [
                'attribute' => 'date_create',
                'format' =>  ['date', 'php:d M Y'],
            ],
            // [
            //     'attribute' => 'create_ip',
            //     'value' => function ($model) {
            //         return $model->create_ip ? long2ip($model->create_ip) : '';
            //     },
            // ],
            [
                'attribute' => 'userCreated.full_name',
                'label' => 'Создал',
            ],
            'userCreated.agency.name',
            //'num_ved',
            [
                'attribute' => 'date_formed',
                'format' =>  ['date', 'php:d M Y'],
            ],
            // [
            //     'attribute' => 'formed_ip',
            //     'value' => function ($model) {
            //         return $model->formed_ip ? long2ip($model->formed_ip) : '';
            //     },
            // ],
            [
                'attribute' => 'userFormed.full_name',
                'label' => 'Сформировал',
            ],
            [
                'attribute' => 'date_reception',
                'format' =>  ['date', 'php:d M Y h:i:s'],
            ],
            // [
            //     'attribute' => 'accepted_ip',
            //     'value' => function ($model) {
            //         return $model->accepted_ip ? long2ip($model->accepted_ip) : '';
            //     },
            // ],
            [
                'attribute' => 'userAccepted.full_name',
                'label' => 'Принял',
            ],
            'userAccepted.agency.name',
            'subdivision.name',
            'address.name',
            'comment',
            'area.name',
        ],
    ]) ?>

</div>
