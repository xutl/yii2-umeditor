<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace xutl\umeditor;

use Yii;
use yii\helpers\Json;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\web\JsExpression;
use yii\widgets\InputWidget;

/**
 * Class UMeditor
 * @package xutl\editormd
 */
class UMeditor extends InputWidget
{

    /**
     * @var array the options for the Bootstrap date time picker JS plugin.
     * Please refer to the Bootstrap date time picker plugin Web page for possible options.
     * @see https://github.com/fex-team/umeditor
     */
    public $clientOptions = [];

    /**
     * {@inheritDoc}
     * @see \yii\base\Object::init()
     */
    public function init()
    {
        parent::init();
        if (!isset ($this->options ['id'])) {
            $this->options ['id'] = $this->getId();
        }
        $this->clientOptions = array_merge([
            'autoHeightEnabled' => true,
            'initialFrameWidth' => '100%',
            'initialFrameHeight' => '300',
            'emotionLocalization' => true,
            'imagePath' => '',
            'lang' => Yii::$app->language == 'zh-CN' ? 'zh-cn' : 'en',
        ], $this->clientOptions);
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->hasModel()) {
            echo Html::activeTextArea($this->model, $this->attribute, $this->options);
        } else {
            echo Html::textArea($this->name, $this->value, $this->options);
        }
        UMeditorAsset::register($this->view);
        $options = empty ($this->clientOptions) ? '' : Json::htmlEncode($this->clientOptions);
        $this->view->registerJs("var {$this->id} = UM.getEditor(\"{$this->options['id']}\", {$options});");

    }
}