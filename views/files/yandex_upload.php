<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $message */
/* @var $this yii\web\View */
/* @var $model app\models\UploadForm */

$this->title = 'Загрузка файлов';
$this->params['breadcrumbs'][] = ['label' => 'Мои файлы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="files-yandex_update">
    <h1> <?= $this->title ?> </h1>
    <p>Загрузить файлы на Yandex.Disk</p>
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

    <?= $form->field($model, 'uploadedFile')->fileInput()->label('') ?>

    <div class="form-group">
        <?= Html::submitButton('Загрузить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end() ?>

</div>
