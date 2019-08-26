<?php

namespace app\modules\admin;

use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

/**
 * admin module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $layout = '/admin';

    public $controllerNamespace = 'app\modules\admin\controllers';

    /**
     * {@inheritdoc}
     */

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'denyCallback' => static function($rule, $action)
                {
                    throw new NotFoundHttpException();
                },
                'rules' => [
                    [
                        'allow' => true,
                        'matchCallback' => static function($rule, $action)
                        {
                            return Yii::$app->user->identity->isAdmin;
                        }
                    ]
                ]
            ]
        ];
    }


    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
