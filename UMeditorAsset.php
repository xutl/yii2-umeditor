<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace xutl\umeditor;

use Yii;
use yii\web\AssetBundle;

/**
 * Class MarkdownEditorAsset
 * @package xutl\editormd
 */
class UMeditorAsset extends AssetBundle
{
    public $sourcePath = '@vendor/xutl/yii2-umeditor-widget/assets';

    public $css = [
        'themes/default/css/umeditor.css',
    ];

    public $js = [
        'umeditor.config.js',
        'umeditor.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}