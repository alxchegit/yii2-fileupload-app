<?php

namespace app\controllers;

use app\models\User;
use Faker\Provider\File;
use phpDocumentor\Reflection\Types\String_;
use Yii;
use app\models\Files;
use app\models\FilesSearch;
use app\models\UploadForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;
use strider2038\yandexDiskTools;
use strider2038\yandexDiskTools\Client;
use Yandex\Disk\DiskClient;

/**
 * FilesController implements the CRUD actions for Files model.
 */
class FilesController extends Controller
{

    private $YandexError;
    private $YandexFilePath;

    const OAUTH_TOKEN = 'OAuth AgAAAAAw8-RIAAa_apLyY7o3wkFhhsEZn4ggt6o';
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
            return $this->redirect(['view', 'id' => $model->id]);
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
                    return $this->redirect('files/index');
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
     *
     */
    public function actionDownload()
    {
       $message = 'Message';
        if(Yii::$app->request->isGet) {
            $path = Yii::$app->request->get('path');

            $message = $this->downloadFromYandexDisk($path);
        }


       return $this->render('download', [
           'message' => $message,
       ]);

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

    private function downloadFromYandexDisk(string $path)
    {
       $token = self::OAUTH_TOKEN;
       $yd_file = $path;

        $ch = curl_init('https://cloud-api.yandex.net/v1/disk/resources/download?path=' . urlencode($yd_file));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: ' . $token));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $res = curl_exec($ch);
        curl_close($ch);

        $res = json_decode($res, true);
        if (empty($res['error'])) {
            $file_name = 'uploads/' . basename($yd_file);
            $file = @fopen($file_name, 'w');

            $ch = curl_init($res['href']);
            curl_setopt($ch, CURLOPT_FILE, $file);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: OAuth ' . $token));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_exec($ch);
            curl_close($ch);
            fclose($file);
        }
    }
    /**
     * Создает каталог в соответствии с id пользователя
     *
     * @param mixed $dir
     * @return bool
     */
    private function getYandexDirectory( $dir )
    {
        $token = self::OAUTH_TOKEN;
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
