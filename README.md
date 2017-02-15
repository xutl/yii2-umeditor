# yii2-umeditor-widget
适用于Yii2的UMeditor


使用
----

```php
<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

class MyController extends Controller
{
    public function actions()
    {
        return [
            'um-upload' => [
                'class' => 'xutl\umeditor\UMeditorAction',
                'onComplete' => function ($filename, $params) {
                    // Do something with file
                    //返回图像的Url地址
                }
            ],
        ];
    }
}
````
widget:

```php
use yii\helpers\Url;
use xutl\umeditor\UMeditor;

<?= $form->field($model, 'description')->widget(UMeditor::className(), [
    'clientOptions'=>[
        'imageUrl'=> Url::to(['um-upload'])
    ]
]);?>
````