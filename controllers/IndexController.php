<?php
/**
 * Created by PhpStorm.
 * User: liyang
 * Date: 2017/7/23
 * Time: 18:40
 */

namespace Kaopur\yii2_doc_online\Controllers;


use yii\web\Controller;

class IndexController extends Controller
{
    /**
     * @var bool 是否检测基础控制器
     */
    public $appControllers = true;
    /**
     * @var string 接口前缀
     */
    public $prefix = '';
    /**
     * @var string 接口后缀
     */
    public $suffix = '';
    /**
     * @var array 希望生成文档的模块
     */
    public $modules = [];

    public function actionIndex()
    {
        if ($service = \Yii::$app->request->get('service')) {
            $api = new \Kaopur\yii2_doc_online\ApiDesc();
            $api->appControllers = $this->module->appControllers;
            $api->suffix = $this->module->suffix;
            $api->prefix = $this->module->prefix;
            $api->render();
        } else {
            $api = new \Kaopur\yii2_doc_online\ApiList();
            $api->appControllers = $this->module->appControllers;
            $api->suffix = $this->module->suffix;
            $api->prefix = $this->module->prefix;
            $api->modules = $this->module->modules;
            $api->render($api->modules);
        }
    }
}