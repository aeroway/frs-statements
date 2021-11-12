<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use kartik\export\ExportMenu;
use frontend\models\VedjustVed;
use frontend\models\VedjustAffairs;
use frontend\models\VedjustStorage;
use frontend\models\VedjustIssuance;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\VedjustAffairsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = ['label' => 'Ведомости', 'url' => ['vedjust-ved/index']];
$this->title = 'Ведомость №' . $idVed;
$this->params['breadcrumbs'][] = 'Дела';
$this->registerJs(' 
    $(document).ready(function(){
        $(\'#mdel\').click(function(){
            let idAffairs = $(\'#w4\').yiiGridView(\'getSelectedRows\');
            $.ajax({
                type: \'POST\',
                url : \'delete-multiple\',
                data : {idAffairs: idAffairs, idVed: ' . $idVed . '},
                success : function() {
                $(this).closest(\'tr\').remove();
                }
            });
        });
    });', \yii\web\View::POS_READY);
$this->registerJs('
    $(document).ready(function(){
        $(\'#verified\').click(function(){
            let idAffairs = $(\'#w4\').yiiGridView(\'getSelectedRows\');
            $.ajax({
                type: \'POST\',
                url : \'verify-multiple\',
                data : {idAffairs: idAffairs, idVed: ' . $idVed . '},
                success : function(data) {
                    if (data == 0) {
                        alert(\'Некорректно.\');
                    }
                    location.reload();
                }
            });
        });
    });', \yii\web\View::POS_READY);
?>

<div class="vedjust-affairs-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php if (Yii::$app->user->can('archive')): ?>
        <?php //$storage = $modelAffairs->getStoragePath($idVed); ?>
        <?php if ($storage): ?>
        <div class="row">
            <div class="col-md-3 col-sm-4 col-xs-6">
                <div class="dummy"></div>
                <div class="thumbnail purple">
                    <?= '<b>Название хранилища:</b> ' . $storage["name"] . '<br>' ?>
                    <?= '<b>Размещено:</b> ' . $storage["comment"]; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    <?php endif; ?>

    <p>
    <?php if (VedjustAffairs::$vedStatusId === 1 && $modelVed->user_created_id === Yii::$app->user->identity->id) : ?>
        <?= Html::a('Добавить', ['create', 'id' => $modelVed->id], ['class' => 'btn btn-success']); ?>
        <?= Html::submitButton('Удалить', ['class' => 'btn btn-danger', 'id' => 'mdel']); ?>
    <?php endif; ?>

    <?php if ($modelVed->checkPermitformed($modelVed)) : ?>
        <?= Html::a('Сформировать', 'javascript:void(0);', ['class' => 'btn btn-success', 'onclick' => 'changeStatusVed(' . $modelVed->id . ');']); ?>
    <?php endif; ?>

    <?php
    if ($model->checkPermitAffairsBarcode($modelVed)) {
        echo Html::a('', ['check-affairs-barcode', 'id' => $modelVed->id], ['class' => 'btn btn-warning glyphicon glyphicon-barcode', 'title' => 'Продтвердить получение дела по штрих-коду']) . ' ';
    }

    if (VedjustAffairs::$vedStatusId === 3 || VedjustAffairs::$vedStatusId === 4) {
        echo Html::a('', ['vedjust-ved/createcopy', 'id' => $modelVed->id], ['class' => 'btn btn-warning glyphicon glyphicon-plus', 'title' => 'Создать с копированием']);
    }

    if ($modelVed->checkPermitStatusReturn($modelVed)) {
        echo Html::a('Откатить', 'javascript:void(0);', [
            'class' => 'btn btn-danger', 
            'onclick' => 'changeStatusVedReturn(' . $modelVed->id . ');'
        ]);
    }

    // принять может тот, кому было отправлено
    $target = 0;

    if (Yii::$app->user->can('mfc') === true)
        $target = 1;

    if (Yii::$app->user->can('zkp') === true)
        $target = 2;

    if (Yii::$app->user->can('rosreestr') === true || Yii::$app->user->can('confirmExtDocs') === true)
        $target = 3;

    ?>

    <?php if ($model->isVedNotVerified($modelVed)) : ?>
        <?= Html::a('Принято', 'javascript:void(0);', ['class' => 'btn btn-success', 'id' => 'verified']); ?>
    <?php endif; ?>

    <?= (VedjustAffairs::$vedStatusId != 1) ? Html::a('<img src="/images/icons/pdf-32x32.png">', ['vedjust-ved/createvedpdf', 'id' => $modelVed->id], ['class' => '', 'title' => 'Экспорт в PDF']) : ''; ?>

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
                'value' => Url::to('/vedjust-ved/send-ext-docs?id=' . $modelVed->id),
                'class' => 'btn btn-success',
                'id' => 'modalVedExtDocCreate'
            ]
        );
    }
    ?>

    <?php if ($modelVed->canPutVedIntoStorage($modelVed)): ?>
        <?= Html::a('Поместить в архивохранилище', Url::to('/vedjust-storage/create?ved=' . $modelVed->id), ['class' => 'btn btn-success']); ?>
    <?php endif; ?>

    <?php if ($modelVed->checkPermitChangesuspense()) : ?>
        <?= Html::a('Приостановлено', 'javascript:void(0);', ['class' => 'btn btn-danger', 'onclick' => 'changeSuspense(' . $modelVed->id . ", 7" . ');']); ?>
        <?= Html::a('Отказ в снятии с приостановки', 'javascript:void(0);', ['class' => 'btn btn-danger', 'onclick' => 'changeSuspense(' . $modelVed->id . ", 8" . ');']); ?>
        <?= Html::a('Отказано', 'javascript:void(0);', ['class' => 'btn btn-danger', 'onclick' => 'changeSuspense(' . $modelVed->id . ", 9" . ');']); ?>
    <?php endif; ?>

    </p>

    <?php
    $buttons =
    [
        'class' => 'yii\grid\ActionColumn',
        'contentOptions' => ['style'=>'width: 98px;'],
        'buttons' =>
        [
            'view' => function($url, $model, $key) {
                $customurl = Yii::$app->getUrlManager()->createUrl(['vedjust-affairs/view', 'id' => $model['id']]);

                return Html::a('<span class="btn-xs btn-info glyphicon glyphicon-eye-open"></span>', $customurl, 
                    [
                        'title' => Yii::t('yii', 'View'),
                        'aria-label' => Yii::t('yii', 'View'),
                        'data-pjax' => '0',
                    ]);
            },
            'delete' => function($url, $model, $key)
            {
                if(VedjustAffairs::$vedStatusId === 1 && $model->user_created_id === Yii::$app->user->identity->id)
                {
                    $customurl = Yii::$app->getUrlManager()->createUrl(['vedjust-affairs/delete','id' => $model['id']]);

                    return Html::a('<span class="btn-xs btn-danger glyphicon glyphicon-trash"></span>', $customurl, 
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
                //редактировать - статус "создаётся" или статус "принято" и тот, кто принял
                // if (($model->ved->status_id === 1 && $model->user_created_id === Yii::$app->user->identity->id) 
                //     || ($model->ved->status_id === 3 && $model->user_accepted_id === Yii::$app->user->identity->id))
                // {
                    $customurl = Yii::$app->getUrlManager()->createUrl(['vedjust-affairs/update','id' => $model['id']]);

                    return Html::a('<span class="btn-xs btn-warning glyphicon glyphicon-pencil"></span>', $customurl, 
                            [
                                'title' => Yii::t('yii', 'Update'),
                                'aria-label' => Yii::t('yii', 'Update'),
                                'data-pjax' => '0',
                            ]);
                // }
            },
            'issuance' => function($url, $model, $key)
            {
                // $numIssuance = VedjustIssuance::find()->select(['count(*) num'])->where(['affairs_id' => $model->id])->asArray()->one()["num"];

                if((VedjustAffairs::$vedStatusId === 5 || VedjustAffairs::$vedStatusId === 6)
                    && $model->status === 1
                    // && $numIssuance !== $model->p_count
                    && VedjustAffairs::$checkAffairsIssuance
                    && Yii::$app->user->can('issuance'))
                {
                    $customurl = Yii::$app->getUrlManager()->createUrl(['vedjust-affairs/issuance', 'id' => $model['id']]);

                    return Html::a('<span class="btn-xs btn-info glyphicon glyphicon-user"></span>', 'javascript:void(0);', 
                            [
                                'title' => Yii::t('yii', 'Выдать дело'),
                                'aria-label' => Yii::t('yii', 'Выдать дело'),
                                'data-pjax' => '1',
                                'onclick' => 'modalAffairIssuanceCreate(this);',
                                'value' => $customurl,
                            ]);
                }
            },
            'applicants' => function($url, $model, $key) {
                $applicants = $model->getApplicants();

                if (!empty($applicants)) {
                    $styleStatusSend = 'btn-xs btn-info';

                    if ($model->send_sms === 1) {
                        $styleStatusSend = 'btn-xs btn-success';
                    }

                    if ($model->send_sms === 0) {
                        $styleStatusSend = 'btn-xs btn-danger';
                    }

                    return Html::a('<span class="glyphicon glyphicon-envelope ' . $styleStatusSend . '"></span>', 'javascript:void(0);', 
                    [
                        'title' => Yii::t('yii', $applicants),
                        'aria-label' => Yii::t('yii', $applicants),
                        'data-pjax' => '1',
                        // 'onclick' => 'modalAffairIssuanceCreate(this);',
                        // 'value' => $customurl,
                    ]);
                }
            }
        ],
        'template' => '{update} {delete} {issuance} {view} {applicants}',
    ];

    if ($modelVed->status_id > 2) {
        $verifedColumn = [];
    } else {
        $verifedColumn = [
            'class' => '\kartik\grid\CheckboxColumn', 
            'rowHighlight' => false,
            'checkboxOptions' => function($model) {
                if($model->status) {
                    return ['disabled' => true];
                } else {
                    return [];
                }
            },
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

    <?php
    $gridColumns = [
        'ref_num',
        'kuvd',
        [
            'attribute' => 'date_create',
            'format' =>  ['date', 'php:d M Y'],
        ],
        [
            'attribute' => 'user_created_id',
            'value' => function($data) {
                return $data->userCreated->full_name;
            },
        ],
        [
            'attribute' => 'date_status',
            'format' =>  ['date', 'php:d M Y'],
        ],
        [
            'attribute' => 'userAccepted.full_name',
            'label' => 'Подтвердил',
        ],
        'p_count',
        'comment',
    ];
    ?>

    <?= ExportMenu::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumns,
        'exportConfig' => [
            ExportMenu::FORMAT_HTML => false,
            ExportMenu::FORMAT_TEXT => false,
            ExportMenu::FORMAT_PDF => false,
        ],
    ]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function($model)
        {
            if (!empty(Yii::$app->session['VedjustVedSearch'])) {
                foreach (Yii::$app->session['VedjustVedSearch'] as $value) {
                    if (!empty($value['search_ref_num'])) {
                        $searchRefNum = addcslashes($value["search_ref_num"], '/');
                        $patternSearchRefNum = "/($searchRefNum)+/iu";

                        if (preg_match($patternSearchRefNum, $model->ref_num)) {
                            return ['class' => 'info'];
                        }
                    }

                    if (!empty($value['search_affairs'])) {
                        $searchAffairs = addcslashes($value["search_affairs"], '/');
                        $patternSearchAffairs = "/($searchAffairs)+/iu";

                        if (preg_match($patternSearchAffairs, $model->kuvd)) {
                            return ['class' => 'info'];
                        }
                    }

                    if (!empty($value['search_comment_ved_affairs'])) {
                        $searchCommentVedAffairs = addcslashes($value["search_comment_ved_affairs"], '/');
                        $patternSearchCommentVedAffairs = "/($searchCommentVedAffairs)+/iu";

                        if (preg_match($patternSearchCommentVedAffairs, $model->comment)) {
                            return ['class' => 'info'];
                        }
                    }
                }
            }

            if (VedjustAffairs::$vedStatusId >= 3 && $model->status == 0) return ['class' => 'danger'];
        },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'ref_num',
                'contentOptions' => ['style'=>'width: 200px;'],
            ],
            [
                'attribute' => 'kuvd',
                'contentOptions' => ['style'=>'width: 200px;'],
            ],
            [
                'label' => 'Создано',
                'attribute' => 'date_create',
                'format' =>  ['date', 'php:d.m.Y'],
            ],
            [
                'label' => 'Подтверждено',
                'attribute' => 'date_status',
                'format' =>  ['date', 'php:d.m.Y'],
            ],
            'comment',
            // 'status',
            [
                'label' => 'Выдано',
                'value' => function($model) {
                    return $model->getCountIssuance();
                },
            ],
            $verifedColumn,
            $buttons,
        ],
    ]);
    ?>
</div>

<script>
if (document.getElementsByClassName("verify-affair-checkbox").length !== 0 && !document.getElementsByClassName("verify-affair-checkbox")[0].disabled) {
    var element = document.getElementsByClassName("verify-affairs-check-all");
    // удалить "выбрать все", если отсутствуют чекбокс на каждом деле
    // element[0].parentNode.removeChild(element[0]);
    element[0].disabled = false;
}

function changeSuspense(value, btn) {
    $.ajax(
    {
        type: 'GET',
        url: '/vedjust-ved/changesuspense',
        data: 'id=' + value + '&button=' + btn,
        success: function(data) { 
            if (data == 0) {
                alert('Ошибка обработки приостановки.');
            }
            location.reload();
        }
    });
}

function changeStatusVed(value) {
    $.ajax(
    {
        type: 'GET',
        url: '/vedjust-ved/changestatus',
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
        url: '/vedjust-ved/changestatusreturn',
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
