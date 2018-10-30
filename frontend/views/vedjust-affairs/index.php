<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use frontend\models\VedjustVed;
use frontend\models\VedjustAffairs;
use frontend\models\VedjustStorage;
use frontend\models\VedjustIssuance;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\VedjustAffairsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = ['label' => 'Ведомости', 'url' => ['vedjust-ved/index']];
$this->title = 'Ведомость №' . Yii::$app->request->get('id');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vedjust-affairs-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php if (Yii::$app->user->can('editArchive')): ?>
        <?php //$storage = $modelAffairs->getStoragePath(Yii::$app->request->get('id')); ?>
        <?php if ($storage): ?>
        <div class="row">
            <div class="col-md-3 col-sm-4 col-xs-6">
                <div class="dummy"></div>
                <div class="thumbnail purple">
                    <?= '<b>Зал:</b>' . $storage["hall"] . '<br>'; ?>
                    <?= '<b>Стеллаж:</b>' . $storage["rack"] . '<br>'; ?>
                    <?= '<b>Шкаф:</b>' . $storage["locker"] . '<br>'; ?>
                    <?= '<b>Полка:</b>' . $storage["shelf"] . '<br>'; ?>
                    <?= '<b>Позиция:</b>' . $storage["position"]; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    <?php endif; ?>

    <p>
    <?php
    if (($modelVed = VedjustVed::findOne(Yii::$app->request->get('id'))) !== null) {
        if ($modelVed->status_id === 1 && $modelVed->user_created_id === Yii::$app->user->identity->id)
        {
            echo Html::a('Добавить дело ', ['create', 'id' => !empty(Yii::$app->request->get('id')) ? Yii::$app->request->get('id') : '',], ['class' => 'btn btn-success']) . ' ';
            echo Html::a('Сформировать', 'javascript:void(0);', 
                    [
                        'class' => 'btn btn-success',
                        'onclick' => 'changeStatusVed(' . Yii::$app->request->get('id') . ');'
                    ]) . ' ';
        }

        if ($modelVed->status_id === 2 && $modelVed->user_created_id === Yii::$app->user->identity->id)
        {
            echo Html::a('Откатить', 'javascript:void(0);', 
                    [
                        'class' => 'btn btn-danger',
                        'onclick' => 'changeStatusVedReturn(' . Yii::$app->request->get('id') . ');'
                    ]) . ' ';
        }

        $target = 0;

        if (Yii::$app->user->can('editMfc') === true)
            $target = 1;
        if (Yii::$app->user->can('editZkp') === true)
            $target = 2;
        if (Yii::$app->user->can('editRosreestr') === true || Yii::$app->user->can('confirmExtDocs') === true)
            $target = 3;

        if ($modelVed->status_id === 2 && $modelVed->target === $target) {
            echo $modelVed->verified ? '' : Html::a('Принято', 'javascript:void(0);', ['class' => 'btn btn-success', 'onclick' => 'changeVerified(' . Yii::$app->request->get('id') . ');']);
        }
    }
    ?>
    <?= Html::a('Экспорт в PDF', ['vedjust-ved/createvedpdf', 'id' => !empty(Yii::$app->request->get('id')) ? Yii::$app->request->get('id') : '',], ['class' => 'btn btn-info']) . ' '; ?>
    <?php
        if (
                $modelVed->verified === 1 && 
                $modelVed->target === 3 && 
                $modelVed->ext_reg === 1 && 
                $modelVed->ext_reg_created !== 1 && 
                Yii::$app->user->can('confirmExtDocs')
            )
        {
            echo Html::button('Переместить в ...',
                [
                    'value' => Url::to('index.php?r=vedjust-ved/send-ext-docs&id=' . Yii::$app->request->get('id')),
                    'class' => 'btn btn-success',
                    'id' => 'modalVedExtDocCreate'
                ]
            );
        }
    ?>

    <?php // one ved record in storage
    if (Yii::$app->user->can('editArchive') && 
        empty(VedjustStorage::find()->where(['ved_id' => Yii::$app->request->get('id')])->one()->ved_id)):
    ?>
        <?= Html::a('Поместить в архив', 
            Url::to('index.php?r=vedjust-storage/create&ved=' . Yii::$app->request->get('id')), ['class' => 'btn btn-success']); 
        ?>
    <?php endif; ?>
    </p>

    <?php
    if (Yii::$app->user->can('addAudit')) {
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
                    if($model->ved->status_id === 1)
                    {
                        $customurl = Yii::$app->getUrlManager()->createUrl(['vedjust-affairs/delete','id' => $model['id']]);

                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', $customurl, 
                                [
                                    'title' => Yii::t('yii', 'Delete'),
                                    'aria-label' => Yii::t('yii', 'Delete'),
                                    'data-pjax' => '0',
                                    'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                    'data-method' => 'post',
                                ]);
                    }
                },
                'update' => function($url, $model, $key)
                {
                    if($model->ved->status_id === 1)
                    {
                        $customurl = Yii::$app->getUrlManager()->createUrl(['vedjust-affairs/update','id' => $model['id']]);

                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $customurl, 
                                [
                                    'title' => Yii::t('yii', 'Update'),
                                    'aria-label' => Yii::t('yii', 'Update'),
                                    'data-pjax' => '0',
                                ]);
                    }
                },
                'issuance' => function($url, $model, $key)
                {
                    $numIssuance = VedjustIssuance::find()->select(['count(*) num'])->where(['affairs_id' => $model->id])->asArray()->one()["num"];

                    if($model->ved->status_id === 5
                        && $model->status === 1
                        && $numIssuance !== $model->p_count
                        && $model->getCheckAffairsIssuance($model->ved_id)
                        && Yii::$app->user->can('editIssuance'))
                    {
                        $customurl = Yii::$app->getUrlManager()->createUrl(['vedjust-affairs/issuance', 'id' => $model['id']]);

                        return Html::a('<span class="glyphicon glyphicon-user"></span>', 'javascript:void(0);', 
                                [
                                    'title' => Yii::t('yii', 'Выдать дело'),
                                    'aria-label' => Yii::t('yii', 'Выдать дело'),
                                    'data-pjax' => '1',
                                    'onclick' => 'modalAffairIssuanceCreate(this);',
                                    'value' => $customurl,
                                ]);
                    }
                },
            ],
            'template' => '{update} {delete} {issuance}',
        ];
    }
    ?>

    <?php
    Modal::begin([
        'options' => [
            'tabindex' => false
        ],
        'header' => 'Документы',
        'id' => 'modalVedExtDoc',
        'size' => 'modal-sm',
    ]);

    echo "<div id='modalVedExtDocContent'></div>";

    Modal::end();

    ?>

    <?php
    Modal::begin([
        'options' => [
            'tabindex' => false
        ],
        'header' => 'Выдать дело',
        'id' => 'modalAffairIssuance',
        'size' => 'modal-sm',
    ]);

    echo "<div id='modalAffairIssuanceContent'></div>";

    Modal::end();

    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'kuvd',
            [
                'attribute' => 'date_create',
                'format' =>  ['date', 'php:d M Y h:i:s'],
            ],
            [
                'attribute' => 'date_status',
                'format' =>  ['date', 'php:d M Y h:i:s'],
            ],
            'comment',
            // 'status',
            [
                'label' => 'Выдано',
                'value' => 'countIssuance',
            ],
            [
                // 'header' => Html::checkbox('selection_all', false,
                //     [
                //         'class' => 'select-on-check-all',
                //         'value' => 1,
                //         'onclick' => '$(".kv-row-checkbox").prop("checked", $(this).is(":checked"));'
                //     ]),
                'contentOptions' => ['class' => 'kv-row-select'],
                'content' => function($model, $key) {
                    if ($model->ved->status_id === 2 && !Yii::$app->user->can('addAudit'))
                    {
                        return Html::checkbox("status$key",
                            isset($model->status) && !($model->status === 0) ? true : false,
                            [
                                'class' => 'kv-row-checkbox',
                                'id' => 'status' . $key,
                                'value' => $key,
                                'onclick' => 'changeStatusAffairs(this.value);',
                                'disabled' => (isset($model->ved->verified) && !($model->ved->verified === 0)) || ($model->ved->user_created_id == Yii::$app->user->identity->id) ? true : false,
                            ]
                        );
                    }
                },
                'hAlign' => 'center',
                'vAlign' => 'middle',
                'hiddenFromExport' => true,
                'mergeHeader' => true,
            ],
            // [
            //     'class' => '\kartik\grid\CheckboxColumn',
            //     'checkboxOptions' => function($model) {
            //         if($model->status) {
            //             return ['disabled' => true];
            //         } else {
            //             return [];
            //         }
            //     },
            // ],

            $buttons,
        ],
    ]); ?>
</div>

<script>
function changeStatusAffairs(value) {
    var checkStatus = document.getElementById("status" + value).checked;

    $.ajax(
    {
        type: 'GET',
        url: 'index.php?r=vedjust-affairs/changestatus',
        data: 'id=' + value + '&status=' + +checkStatus,
        success: function(data) { 
            if (data == 0) alert('Ошибка обработки.');
        }
    });

}

function changeVerified(value, btn) {
    $.ajax(
    {
        type: 'GET',
        url: 'index.php?r=vedjust-ved/changeverified',
        data: 'id=' + value + '&button=' + btn,
        success: function(data) { 
            if (data == 0) {
                alert('Ошибка обработки.');
            }
            location.reload();
        }
    });
}

function changeStatusVed(value) {
    $.ajax(
    {
        type: 'GET',
        url: 'index.php?r=vedjust-ved/changestatus',
        data: 'id=' + value,
        success: function(data) { 
            if (data == 0) {
                alert('Ошибка обработки.');
            }
            location.reload();
        }
    });
}

function changeStatusVedReturn(value) {
    $.ajax(
    {
        type: 'GET',
        url: 'index.php?r=vedjust-ved/changestatusreturn',
        data: 'id=' + value,
        success: function(data) { 
            if (data == 0) {
                alert('Ошибка обработки.');
            }
            location.reload();
        }
    });
}

function modalAffairIssuanceCreate(value) {
    $('#modalAffairIssuance').modal('show')
        .find('#modalAffairIssuanceContent')
        .load($(value).attr('value'));
}
</script>
