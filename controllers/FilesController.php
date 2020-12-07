<?php

namespace app\controllers;

use Yii;
use app\models\User;
use Faker\Provider\File;
use app\models\Files;
use app\models\FilesSearch;
use app\models\UploadForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;

/**
 * FilesController implements the CRUD actions for Files model.
 */
class FilesController extends Controller
{

    private $YandexError;
    private $YandexFilePath;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Files models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->isAllow();
        $searchModel = new FilesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Files model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Files model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UploadForm();

        if (Yii::$app->request->isPost) {
            $model->uploadedFile = UploadedFile::getInstance($model, 'uploadedFile');
            if ($model->upload()) {
                // file is uploaded successfully
                $model->uploadedFile = UploadedFile::reset();
                Yii::$app->session->setFlash('success', 'File uploaded');
                $this->goBack('index');
            } else {
                Yii::$app->session->setFlash('error', 'File Not uploaded');
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Files model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Files model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Files model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Files the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Files::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Контролер загрузки файла на ЯндексДиск
     * 
     */
    public function actionYandexUpload()
    {
        $this->isAllow();
        $user_id = Yii::$app->user->id;
        $model = new UploadForm();
        if (Yii::$app->request->isPost) {
            $upfile = UploadedFile::getInstance($model, 'uploadedFile');

            if ($this->getYandexDirectory($user_id) && $this->uploadYandexDiskFile($upfile)) {
                $files = new Files();
                $files->username = $user_id;
                $files->title = $this->YandexFilePath['baseName'];
                $files->url = $this->YandexFilePath['fullPath'];
                if($files->validate() && $files->save()){
                    return $this->redirect('index');
                }
            }

        }

        $message = 'Message';

        return $this->render('yandex_upload', [
            'message' => $message,
            'model' => $model,
        ]);
    }

    /**
     * Запуск скачки файла по url переданному в GET
     * 
     */
    public function actionDownload()
    {
        $this->isAllow();
      
        if(Yii::$app->request->isGet) {
            $path = Yii::$app->request->get('path');
           
            $this->downloadFromYandexDisk($path);
                 
            
        }
        $this->response->redirect('index');
    }

    /**
     * @return string
     *
     */
    private function isAllow()
    {
        if(Yii::$app->user->isGuest){
            return $this->render('site/index');
        }
    }

    /**
     * Произвести скачивание файла с ЯндексДиска по заданному $path
     * 
     * @param string $path
     * @return bool
     */
    private function downloadFromYandexDisk(string $path)
    {
       $token = Yii::$app->params['OAuth'];
       $yd_file = $path;
        
        $ch = curl_init('https://cloud-api.yandex.net/v1/disk/resources/download?path=' . urlencode($yd_file));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: ' . $token));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $res = curl_exec($ch);
        curl_close($ch);

//  Не удалось у меня напрямую отправить файл из урла пользователю для скачивания
//  Яндекс диск постоянно отвергает такие попытки.
//  Дает спокойно прочитать файл из урла
//  Дает сохранить его на сервере
//  Но ни редиректы ни response не работают.
//  Пришлось сначало сохранять файл на сервере, а потом отдавать пользователю на скачивание  
//  
        $res = json_decode($res, true);
        if (empty($res['error'])) {
            $file_name = 'uploads/' . basename($yd_file);          
            $newfname = $path;
            $file = fopen ($res['href'], 'rb');
            if ($file) {
                $newf = fopen($file_name, 'wb');
                if ($newf) {
                    while(!feof($file)) {
                        fwrite($newf, fread($file, 1024 * 8), 1024 * 8);
                    }
                }
            }
            if ($file) {
                fclose($file);
            }
            if ($newf) {
                fclose($newf);
            }
            $this->response->sendFile($file_name)->send();
            unlink($file_name);
            return true;
        }
        $this->YandexError = $res;
        return false;
    }
    /**
     * Создает каталог в соответствии с id пользователя
     *
     * @param mixed $dir
     * @return bool
     */
    private function getYandexDirectory( $dir )
    {
        $token = Yii::$app->params['OAuth'];
        $path = 'disk:/Приложения/hotgear_fileupload/' . $dir ;

        $ch = curl_init('https://cloud-api.yandex.net/v1/disk/resources/?path=' . urlencode($path));
        curl_setopt($ch, CURLOPT_PUT, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: ' . $token));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false );
        $res = curl_exec($ch);
        curl_close($ch);

        $res = json_decode($res, true);

        $this->YandexError = $res;
        return true;
    }

    /**
     * Загрузка файла на ЯндексДиск в папку id пользователя
     * 
     * @param UploadedFile $file
     * @return bool
     */
    private function uploadYandexDiskFile(UploadedFile $file)
    {
        $token = Yii::$app->params['OAuth'];
        $path = 'disk:/Приложения/hotgear_fileupload/' . Yii::$app->user->id . '/';
        $filename = $file->name;
        $tempFilePath = $file->tempName;
        $fileSize = $file->size;
        // Запрашиваем URL для загрузки.
        $ch = curl_init('https://cloud-api.yandex.net/v1/disk/resources/upload?path=' . urlencode($path . $filename));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: ' . $token));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $res = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($res, true);
        if (empty($res['error'])) {
            // Если ошибки нет, то отправляем файл на полученный URL.
            $fp = fopen($tempFilePath, 'r');

            $ch = curl_init($res['href']);
            curl_setopt($ch, CURLOPT_PUT, true);
            curl_setopt($ch, CURLOPT_UPLOAD, true);
            curl_setopt($ch, CURLOPT_INFILESIZE, $fileSize);
            curl_setopt($ch, CURLOPT_INFILE, $fp);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code == 201) {
                $this->YandexFilePath = [
                    'fullPath' => $path . $filename,
                    'fileName' => $filename,
                    'baseName' => $file->getBaseName(),

                ];
                return true;
            }
        }
        $this->YandexError = $res['message'];
        return false;
    }
}
