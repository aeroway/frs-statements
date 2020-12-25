<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\export\ExportMenu;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use frontend\models\VedjustAgency;
use frontend\models\VedjustStatus;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\VedjustVedSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Ведомости';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vedjust-ved-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_searchNumVed', ['model' => $searchModel]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?= $this->render('_searchRefNumAffairs', ['model' => $searchModel]); ?>
    <?= $this->render('_searchCommentVedAffairs', ['model' => $searchModel]); ?>

    <p>
        <?php if (!Yii::$app->user->can('addAudit') && !Yii::$app->user->can('limitAudit')): ?>
            <?php // echo Html::button('Создать ведомость', ['value' => Url::to('/vedjust-ved/create'), 'class' => 'btn btn-success', 'id' => 'modalVedCreate']); ?>
            <?= Html::a(NULL, Url::to('/vedjust-ved/create'), ['class' => 'btn btn-success glyphicon glyphicon-plus', 'title' => 'Создать ведомость']); ?>
        <?php endif; ?>

        <?php if (Yii::$app->getRequest()->getCookies()->getValue('archive')): ?>
            <?= Html::a(NULL, 'javascript:void(0);', ['class' => 'btn btn-danger glyphicon glyphicon-resize-small', 'onclick' => 'setArchive(0);', 'title' => 'Скрыть архивные']); ?>
        <?php endif; ?>

        <?php if(!Yii::$app->getRequest()->getCookies()->getValue('archive')): ?> 
            <?= Html::a(NULL, 'javascript:void(0);', ['class' => 'btn btn-info glyphicon glyphicon-resize-full', 'onclick' => 'setArchive(1);', 'title' => 'Показать все']); ?>
        <?php endif; ?>

        <?= Html::a(NULL, ['import-pkpvd-xlsx-notice'], ['class' => 'btn btn-success glyphicon glyphicon-upload', 'title' => 'Импорт списка обращений']); ?>

        <?= Html::a(NULL, ['reset'], ['class' => 'btn btn-warning glyphicon glyphicon-refresh', 'title' => 'Сброс фильтров']); ?>

        <?php if(Yii::$app->user->can('confirmExtDocs') || Yii::$app->user->can('addAudit') || Yii::$app->user->can('limitAudit')): ?>
            <?= Html::a('Экстер. документы', Url::to('/vedjust-ved/view-ext-doc'), ['class' => 'btn btn-info']); ?>
        <?php endif; ?>
    </p>

    <?php if( Yii::$app->session->hasFlash('limitOpenVed') ): ?>
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php echo Yii::$app->session->getFlash('limitOpenVed'); ?>
        </div>
    <?php endif; ?>

    <?php
    $buttons =
    [
        'class' => 'yii\grid\ActionColumn',
        'buttons' =>
        [
            'view' => function($url, $model, $key) {
                $customurl = Yii::$app->getUrlManager()->createUrl(['vedjust-ved/view', 'id' => $model['id']]);

                return Html::a('<span class="btn-xs btn-info glyphicon glyphicon-eye-open"></span>', $customurl, 
                    [
                        'title' => Yii::t('yii', 'View'),
                        'aria-label' => Yii::t('yii', 'View'),
                        'data-pjax' => '0',
                    ]);
            },
            'delete' => function($url, $model, $key) {
                if($model->status_id === 1 && $model->user_created_id === Yii::$app->user->identity->id) {
                    return Html::a('<span class="btn-xs btn-danger glyphicon glyphicon-trash"></span>', 
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
            'update' => function($url, $model, $key) {
                if (($model->status_id === 1 && $model->user_created_id === Yii::$app->user->identity->id)) {
                    $customurl = Yii::$app->getUrlManager()->createUrl(['vedjust-ved/update', 'id' => $model['id']]);

                    return Html::a('<span class="btn-xs btn-warning glyphicon glyphicon-pencil"></span>', $customurl, 
                        [
                            'title' => Yii::t('yii', 'Update'),
                            'aria-label' => Yii::t('yii', 'Update'),
                            'data-pjax' => '0',
                        ]);
                }
            },
            'createvedpdf' => function ($url, $model, $key) {
                if ($model->status_id != 1) {
                    return Html::a('<span class="btn-xs btn-info glyphicon glyphicon-file"></span>', $url,
                        [
                            'title' => Yii::t('yii', 'Сформировать PDF'),
                            'aria-label' => Yii::t('yii', 'Сформировать PDF'),
                            'target' => '_blank',
                        ],
                    );
                }
            },
            'createcopy' => function ($url, $model, $key) {
                if ($model->status_id === 3 || $model->status_id === 4) {
                    return Html::a('<span class="btn-xs btn-warning glyphicon glyphicon-plus"></span>', $url,
                        [
                            'title' => Yii::t('yii', 'Создать с копированием'),
                            'aria-label' => Yii::t('yii', 'Создать с копированием'),
                            'target' => '_blank',
                        ],
                    );
                }
            }
        ],
        'contentOptions' => ['style' => 'width: 99px;'],
        'template' => '{view} {delete} {createvedpdf} {update} {createcopy}',
    ];

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
                return $data->userCreated->full_name;
            },
        ],
        // [
        //     'attribute' => 'user_created_id',
        //     'value' => 'userAccepted.username',
        //     'content'=>function($model){
        //         return $model->user_created_id;
        //     }
        // ],
        [
            'attribute' => 'user_accepted_id',
            'value' => 'userAccepted.full_name',
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
    // Modal::begin([
    //     'options' => [
    //         'tabindex' => false
    //     ],
    //     'header' => 'Создать ведомость',
    //     'id' => 'modalVed',
    //     'size' => 'modal-sm',
    // ]);

    // echo "<div id='modalVedContent'></div>";

    // Modal::end();

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

            [
                //'attribute' => 'archive_unit_id',
                'label' => 'Тип',
                'format' => 'html',
                'value' => 'iconUnit',
                'contentOptions' => ['style' => 'text-align: center; width: 40px;'],
            ],
            [
                'attribute' => 'id',
                'label' => '№ вед.',
                'value' => function($data) {
                    return Html::a(Html::encode($data->id), ['vedjust-affairs/index', 'id' => $data['id']]);
                },
                'format' => 'html',
            ],
            [
                'attribute' => 'date_create',
                'label' => 'Создано',
                'format' =>  ['date', 'php:d.m.Y'],
            ],
            [
                'attribute' => 'status_id',
                'value' => function($data) {
                    return Html::a(Html::encode($data->status->name), ['vedjust-affairs/index', 'id' => $data['id']]);
                },
                'filter' => ArrayHelper::map(VedjustStatus::find()->asArray()->all(), 'id', 'name'),
                'format' => 'html',
            ],
            [
                'attribute' => 'user_created_id',
                'label' => 'Источник',
                'content' => function($model) {
                    return $model->userCreated->AgencyName . ' (' . $model->userCreated->SubdivisionName . ')';
                },
                'format' => 'html',
                'filter' => ArrayHelper::map(VedjustAgency::find()->asArray()->all(), 'id', 'name'),
                'contentOptions' => [
                    'style' => 'min-width: 180px; overflow: auto; white-space: normal; word-wrap: break-word;'
                ],
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
                'filter' => ArrayHelper::map(VedjustAgency::find()->asArray()->all(), 'id', 'name'),
                'format' => 'html',
                'contentOptions' => [
                    'style' => 'min-width: 150px; overflow: auto; white-space: normal; word-wrap: break-word;'
                ],
            ],
            // [
            //     'attribute' => 'user_accepted_id',
            //     'content'=>function($model) {
            //         return (!empty($model->userAccepted)) ? 
            //             $model->userAccepted->AgencyName . ' (' . $model->userAccepted->SubdivisionName . ')' : '';
            //     }
            // ],
            [
                'attribute' => 'date_reception',
                'label' => 'Принято',
                'format' =>  ['date', 'php:d.m.Y'],
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
            [
                'attribute' => 'address_id',
                'value' => 'address.name',
                'format' => 'html',
                'contentOptions' => [
                    'style' => 'min-width: 180px; overflow: auto; white-space: normal; word-wrap: break-word;'
                ],
            ],
            [
                'class' => 'kartik\grid\DataColumn',
                'attribute' => 'comment',
                'format' => 'html',
                'contentOptions' => [
                    'style' => 'max-width:150px; overflow: auto; white-space: normal; word-wrap: break-word;'
                ],
            ],

            $buttons,
        ],

    ]); ?>
</div>

<script type="text/javascript">
function setArchive(btn) {
    $.ajax(
    {
        type: 'GET',
        url: '/vedjust-ved/setarchive',
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
