<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace xutl\umeditor;

use Yii;
use yii\base\Action;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\validators\FileValidator;

/**
 * UMeditorAction class file.
 */
class UMeditorAction extends Action
{
    /**
     * @var string file input name.
     */
    public $inputName = 'upfile';

    /**
     * @var string the directory to store temporary files during conversion. You may use path alias here.
     * If not set, it will use the "plupload" subdirectory under the application runtime path.
     */
    public $tempPath = '@runtime/umeditor';

    /**
     * @var integer the permission to be set for newly created cache files.
     * This value will be used by PHP chmod() function. No umask will be applied.
     * If not set, the permission will be determined by the current environment.
     */
    public $fileMode;

    /**
     * @var integer the permission to be set for newly created directories.
     * This value will be used by PHP chmod() function. No umask will be applied.
     * Defaults to 0775, meaning the directory is read-writable by owner and group,
     * but read-only for other users.
     */
    public $dirMode = 0775;

    /**
     * @var callable success callback with signature: `function($filename, $params)`
     */
    public $onComplete;

    /**
     * Initializes the action and ensures the temp path exists.
     */
    public function init()
    {
        parent::init();
        $this->controller->enableCsrfValidation = false;
        $this->tempPath = Yii::getAlias($this->tempPath);
        if (!is_dir($this->tempPath)) {
            FileHelper::createDirectory($this->tempPath, $this->dirMode, true);
        }
    }

    /**
     * Runs the action.
     * This method displays the view requested by the user.
     * @throws HttpException if the view is invalid
     */
    public function run($callback = null)
    {
        $uploadedFile = UploadedFile::getInstanceByName($this->inputName);
        $params = Yii::$app->request->getBodyParams();
        $validator = new FileValidator([
            //'extensions' => $this->imageAllowFiles,
            'checkExtensionByMimeType' => false,
            //"maxSize" => $this->options['scrawlMaxSize'],
        ]);
        if ($validator->validate($uploadedFile, $error)) {

        }
        $result = [];

        if (is_null($callback)) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $result;
        } else {
            Yii::$app->response->format = Response::FORMAT_JSONP;
            return ['callback' => $callback, 'data' => $result];
        }


        $filename = $this->getUnusedPath($this->tempPath . DIRECTORY_SEPARATOR . $uploadedFile->name);

        //背景保存在临时目录中
        $config["savePath"] = $Path;
        $up = new Uploader("upfile", $config);
        $type = $_REQUEST['type'];
        $callback = $_GET['callback'];

        $info = $up->getFileInfo();






        $isUploadComplete = ChunkUploader::process($uploadedFile, $filename);
        if ($isUploadComplete) {
            if ($this->onComplete) {
                return call_user_func($this->onComplete, $filename, $params);
            } else {
                return [
                    'filename' => $filename,
                    'params' => $params,
                ];
            }
        }
        return null;
    }
}