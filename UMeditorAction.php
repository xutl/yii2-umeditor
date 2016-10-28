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
use yii\helpers\Json;
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
     * @return int the max upload size in MB
     */
    public static function getPHPMaxUploadSize()
    {
        $max_upload = (int)(ini_get('upload_max_filesize'));
        $max_post = (int)(ini_get('post_max_size'));
        $memory_limit = (int)(ini_get('memory_limit'));
        return min($max_upload, $max_post, $memory_limit);
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
            'extensions' => 'gif, jpg, jpeg, png, bmp',
            'checkExtensionByMimeType' => true,
            'mimeTypes' => 'image/*',
            "maxSize" => static::getPHPMaxUploadSize() * 1048576,
        ]);
        if (!$validator->validate($uploadedFile, $error)) {
            $result = [
                'state' => $error,
            ];
        } else {
            if ($this->onComplete && ($url = call_user_func($this->onComplete, $uploadedFile, $params)) != false) {
                $result = [
                    "originalName" => $uploadedFile->name,
                    "name" => basename($url),
                    "url" => $url,
                    "size" => $uploadedFile->size,
                    "type" => '.' . $uploadedFile->extension,
                    "state" => 'SUCCESS'
                ];
            } else {
                $result = [
                    "state" => Yii::t('app', 'File save failed'),
                ];
            }
        }
        if (is_null($callback)) {
            echo Json::encode($result);
        } else {
            echo '<script>' . $callback . '(' . Json::encode($result) . ')</script>';
        }
    }
}