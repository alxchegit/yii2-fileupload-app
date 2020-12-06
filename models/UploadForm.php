<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $uploadedFile;

    public function rules()
    {
        return [
            [['uploadedFile'], 'file', 'skipOnEmpty' => false,],
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            $files = new Files();
            $user_id = Yii::$app->user->identity->id;
            $url = 'uploads/' . $this->uploadedFile->baseName . '.' . $this->uploadedFile->extension;

            $this->uploadedFile->saveAs($url);

            $files->title = $this->uploadedFile->baseName;
            $files->url = '/' . $url;
            $files->username = $user_id;
            if ($files->validate() && $files->save()) {
                return true;
            }

        }

        return false;

    }
}