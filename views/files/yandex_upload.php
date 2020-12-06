<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $message */
/* @var $this yii\web\View */
/* @var $model app\models\UploadForm */

?>

<div class="files-yandex_update">
    <h1> <?= $message; ?> </h1>

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

    <?= $form->field($model, 'uploadedFile')->fileInput()->label('Yandex disk') ?>

    <div class="form-group">
        <?= Html::submitButton('Загрузить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end() ?>

</div>
