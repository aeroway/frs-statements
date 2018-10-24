<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use kartik\grid\GridView;
use kartik\export\ExportMenu;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\VedjustVedSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Ведомости';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vedjust-ved-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php if (!Yii::$app->user->can('addAudit') && !Yii::$app->user->can('limitAudit')): ?>
            <?= Html::button('Создать ведомость', ['value' => Url::to('index.php?r=vedjust-ved/create'), 'class' => 'btn btn-success', 'id' => 'modalVedCreate']); ?>
        <?php endif; ?>

        <?php if (Yii::$app->getRequest()->getCookies()->getValue('archive')): ?>
            <?= Html::a('Скрыть архивные', 'javascript:void(0);', ['class' => 'btn btn-warning', 'onclick' => 'setArchive(0);']); ?>
        <?php endif; ?>

        <?php if(!Yii::$app->getRequest()->getCookies()->getValue('archive')): ?> 
            <?= Html::a('Показать все', 'javascript:void(0);', ['class' => 'btn btn-info', 'onclick' => 'setArchive(1);']); ?>
        <?php endif; ?>

        <?= Html::a('Сброс фильтров', ['reset'], ['class' => 'btn btn-warning']); ?>

        <?php if(Yii::$app->user->can('confirmExtDocs') || Yii::$app->user->can('addAudit') || Yii::$app->user->can('limitAudit')): ?>
            <?= Html::a('Экстер. документы', Url::to('index.php?r=vedjust-ved/view-ext-doc'), ['class' => 'btn btn-info']); ?>
        <?php endif; ?>
    </p>

    <?php
    if (Yii::$app->user->can('addAudit') || Yii::$app->user->can('limitAudit')) {
        $buttons =
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{view}',
        ];
    } else {
        $buttons =
        [
            'class' => 'yii\grid\ActionColumn',
            'buttons' =>
            [
                'delete' => function($url, $model, $key)
                {
                    if($model->status_id === 1) 
                    {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', 
                                ['vedjust-ved/delete','id' => $model['id']], 
                                [
                                    'title' => Yii::t('yii', 'Delete'),
                                    'aria-label' => Yii::t('yii', 'Delete'),
                                    'data-pjax' => '0',
                                    'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                    'data-method' => 'post',
                                ]);
                    }
                },
                'createvedpdf' => function ($url, $model, $key)
                {
                    return Html::a('<span class="glyphicon glyphicon-file"></span>',
                            $url,
                            [
                                'title' => Yii::t('yii', 'Сформировать PDF'),
                                'aria-label' => Yii::t('yii', 'Сформировать PDF'),
                                'target' => '_blank',
                            ]);
                },
            ],
            'template' => '{delete} {createvedpdf}',
        ];
    }

    $gridColumns = [
        'id',
        [
            'attribute' => 'date_create',
            'format' =>  ['date', 'php:d M Y h:i:s'],
        ],
        [
            'attribute' => 'status_id',
            'value' => function($data) {
                return $data->status->name;
            },
        ],
        [
            'attribute' => 'user_created_id',
            'value' => function($data) {
                return $data->userCreated->username;
            },
        ],
        //[
//            'attribute' => 'user_created_id',
//            //'value' => 'userAccepted.username',
//            'content'=>function($model){
//                return $model->user_created_id;
//            }
//        ],
        [
            'attribute' => 'user_accepted_id',
            'value' => 'userAccepted.username',
        ],
        [
            'attribute' => 'date_reception',
            'format' =>  ['date', 'php:d M Y'],
        ],
    ];

    echo ExportMenu::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumns,
        'exportConfig' => [
            ExportMenu::FORMAT_HTML => false,
            ExportMenu::FORMAT_TEXT => false,
            ExportMenu::FORMAT_PDF => false,
        ],
    ]);
    ?>

    <?php
    Modal::begin([
        'options' => [
            'tabindex' => false
        ],
        'header' => 'Создать ведомость',
        'id' => 'modalVed',
        'size' => 'modal-sm',
    ]);

    echo "<div id='modalVedContent'></div>";

    Modal::end();

    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function($model)
        {
            if ($model->status_id != 1 && $model->userCreated->username == Yii::$app->user->identity->username) return ['class' => 'warning'];
        },
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'num_ved',
            [
                //'attribute' => 'archive_unit_id',
                'label' => 'Тип',
                'format' => 'html',
                'value' => 'iconUnit',
                'contentOptions' => ['style' => 'text-align: center; width: 40px;'],
            ],
            [
                'attribute' => 'id',
                'label' => '№ ведомости',
                'value' => function($data) {
                    return Html::a(Html::encode($data->id), ['vedjust-affairs/index', 'id' => $data['id']]);
                },
                'format' => 'html',
            ],
            [
                'attribute' => 'date_create',
                'label' => 'Создано',
                'format' =>  ['date', 'php:d M Y'],
            ],
            [
                'attribute' => 'status_id',
                'value' => function($data) {
                    return Html::a(Html::encode($data->status->name), ['vedjust-affairs/index', 'id' => $data['id']]);
                },
                'format' => 'html',
            ],
            [
                'attribute' => 'user_created_id',
                'label' => 'Источник',
                'content' => function($model) {
                    return $model->userCreated->AgencyName . ' ( ' . $model->userCreated->SubdivisionName . ')';
                }
            ],
            // [
            //     'attribute' => 'user_created_id',
            //     'value' => function($data) {
            //      return Html::a(Html::encode($data->userCreated->username), ['vedjust-affairs/index', 'id' => $data['id']]);
            //     },
            //     'format' => 'html',
            // ],
            [
                'attribute' => 'target',
                'label' => 'Получатель',
                'value' => 'targetRecipient',
                'filter' => ['1' => 'МФЦ', '2' => 'ФКП', '3' => 'Росреестр'],
            ],
            // [
            //     'attribute' => 'user_accepted_id',
            //     'content'=>function($model) {
            //         return (!empty($model->userAccepted)) ? 
            //             $model->userAccepted->AgencyName . ' ( ' . $model->userAccepted->SubdivisionName . ')' : '';
            //     }
            // ],
            [
                'attribute' => 'date_reception',
                'label' => 'Подтверждено',
                'format' =>  ['date', 'php:d M Y'],
            ],
            [
                'attribute' => 'ext_reg',
                'label' => 'ЭР',
                'format' => 'html',
                'value' => 'iconExtReg',
                'contentOptions' => ['style' => 'text-align: center; width: 80px;'],
                'filter' => ['1' => '✔ - Экстерриториальная регистрация', '0' => '✖ - Обычная регистрация'],
            ],
            // [
            //     'attribute' => 'kuvd_affairs',
            //     'value' => function($data) {
            //         $output = '';
            //         for ($i = 0; $i < count($data->affairs); $i++) { 
            //             $output .= $data->affairs[$i]["kuvd"] . '<br>';
            //         }

            //         return $output;
            //     },
            //     'format' => 'html',
            // ],
            // 'verified',
            // [
            //     'attribute' => 'IconStatus',
            //     'label' => 'Статус',
            //     'format' => 'html',
            //     'value' => 'IconStatus',
            //     'contentOptions' => ['style'=>'text-align: center; width: 65px;'],
            // ],
            'comment',

            $buttons,
        ],

    ]); ?>
</div>

<script type="text/javascript">
function setArchive(btn) {
    $.ajax(
    {
        type: 'GET',
        url: 'index.php?r=vedjust-ved/setarchive',
        data: 'status=' + btn,
        success: function(data) { 
            if (data == 0) 
                alert('Ошибка обработки.');
            else
                location.reload();
        }
    });
}
</script>
