<?php

namespace Kaopur\yii2_doc_online;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'Kaopur\yii2_doc_online\Controllers';
    public $appControllers = true;
    public $suffix = '';
    public $prefix = '';
    public $modules = [];
    public function init()
    {
        parent::init();
        // custom initialization code goes here
    }
}
