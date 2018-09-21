<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\VedjustExtDoc */

$this->params['breadcrumbs'][] = ['label' => 'Ведомости', 'url' => ['vedjust-ved/index']];
$this->params['breadcrumbs'][] = ['label' => 'Экстерриториальные документы', 'url' => ['vedjust-ved/view-ext-doc']];
$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => $model->area->name, 'url' => ['index-ext-doc-detailed', 'loc' => $model->area->name]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vedjust-affairs-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [

            'affairs.kuvd',
            'affairs.comment',
            'affairs.ved.archiveUnit.name',

            [
                'attribute' => 'userCreated.username',
                'label' => 'Переместил в экстер. документы',
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
                'attribute' => 'userFormed.username',
                'label' => 'Сформировал экстер. документы',
            ],
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
            // [
            //     'attribute' => 'userAccepted.username',
            //     'label' => 'Подтвердил экстер. документы',
            // ],
            // [
            //     'attribute' => 'date_reception',
            //     'format' =>  ['date', 'php:d M Y h:i:s'],
            // ],
            // [
            //     'attribute' => 'accepted_ip',
            //     'value' => function ($model) {
            //         return $model->accepted_ip ? long2ip($model->accepted_ip) : '';
            //     },
            // ],
            [
                'attribute' => 'affairs.userCreated.username',
                'label' => 'Создал дело',
            ],
            [
                'attribute' => 'affairs.date_create',
                'format' =>  ['date', 'php:d M Y h:i:s'],
            ],
            [
                'attribute' => 'affairs.create_ip',
                'value' => function ($model) {
                    return $model->create_ip ? long2ip($model->create_ip) : '';
                },
            ],
            [
                'attribute' => 'affairs.userAccepted.username',
                'label' => 'Принял дело',
            ],
            [
                'attribute' => 'affairs.date_status',
                'format' =>  ['date', 'php:d M Y h:i:s'],
            ],
            [
                'attribute' => 'affairs.accepted_ip',
                'value' => function ($model) {
                    return $model->accepted_ip ? long2ip($model->accepted_ip) : '';
                },
            ],
            'area.name',
        ],
    ]) ?>

</div>
