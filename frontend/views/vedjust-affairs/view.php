<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\VedjustAffairs */

$this->params['breadcrumbs'][] = ['label' => 'Ведомости', 'url' => ['vedjust-ved/index']];
$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Дела', 'url' => ['index', 'id' => $model->ved_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vedjust-affairs-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [

            //'status',
            'kuvd',
            'ref_num',
            [
                'attribute' => 'date_create',
                'format' =>  ['date', 'php:d M Y h:i:s'],
            ],
            [
                'attribute' => 'userCreated.username',
                'label' => 'Создал',
            ],
            // [
            //     'attribute' => 'create_ip',
            //     'value' => function ($model) {
            //         return $model->create_ip ? long2ip($model->create_ip) : '';
            //     },
            // ],
            [
                'attribute' => 'date_status',
                'format' =>  ['date', 'php:d M Y h:i:s'],
            ],
            [
                'attribute' => 'userAccepted.username',
                'label' => 'Принял',
            ],
            // [
            //     'attribute' => 'accepted_ip',
            //     'value' => function ($model) {
            //         return $model->accepted_ip ? long2ip($model->accepted_ip) : '';
            //     },
            // ],
            'comment',
            [
                'label' => 'Выдано',
                'format' => 'html',
                'value' => function ($model) {
                    $listIssuance = '';

                    foreach($model->issuance as $value) {
                        $listIssuance .= '<b>Заявитель:</b> ' . $value->name . '. <b>Регистратор:</b> ' . $value->userCreated->full_name . '<br>';
                    }

                    return $listIssuance;
                },
            ],
        ],
    ]) ?>

</div>
