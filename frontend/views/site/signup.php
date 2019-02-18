<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use frontend\models\VedjustAgency;
use frontend\models\VedjustSubject;
use frontend\models\VedjustSubdivision;
use frontend\models\VedjustAddress;
use frontend\models\User;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

$this->title = 'Регистрация';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Пожалуйста заполните следующие поля для регистрации:</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>
                <?= $form->field($model, 'email') ?>
                <?= $form->field($model, 'password')->passwordInput() ?>
                <?= $form->field($model, 'full_name') ?>
                <?= $form->field($model, 'position') ?>
                <?= $form->field($model, 'phone') ?>

                <?= $form->field($model, 'agency_id')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(VedjustAgency::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                    'language' => 'ru',
                    'options' => ['placeholder' => 'Выберите орган', 'onchange' => 'changeStatusAgency(this.value);'],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]);
                ?>

                <?= $form->field($model, 'subject_id')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(VedjustSubject::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                    'language' => 'ru',
                    'options' => ['placeholder' => 'Выберите субъект РФ', 'onchange' => 'changeStatusSubject(this.value);'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'disabled' => true,
                    ],
                ]);
                ?>

                <?= $form->field($model, 'subdivision_id')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(VedjustSubdivision::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                    'language' => 'ru',
                    'options' => ['placeholder' => 'Выберите или введите название', 'onchange' => 'changeStatusAddress(this.value);'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        //'tags' => true,
                        'disabled' => true,
                    ],
                ]);
                ?>

                <?= $form->field($model, 'address_id')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(VedjustAddress::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                    'language' => 'ru',
                    'options' => ['placeholder' => 'Выберите адрес'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        //'tags' => true,
                        'disabled' => true,
                    ],
                ]);
                ?>

                <div class="form-group">
                    <?= Html::submitButton('Регистрация', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<script>
function changeStatusAgency(value) {
    if (value) {
        document.getElementById('signupform-subject_id').disabled = false;
    } else {
        document.getElementById('signupform-subject_id').disabled = true;
    }
}

function changeStatusSubject(value) {
    if (value) {
        document.getElementById('signupform-subdivision_id').disabled = false;
    } else {
        document.getElementById('signupform-subdivision_id').disabled = true;
    }
}

function changeStatusAddress(value) {
    if (value) {
        document.getElementById('signupform-address_id').disabled = false;
    } else {
        document.getElementById('signupform-address_id').disabled = true;
    }
}
</script>