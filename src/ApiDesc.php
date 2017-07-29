<?php
namespace Kaopur\yii2_doc_online;

/**
 * PhalApi_Helper_ApiDesc - 在线接口描述查看 - 辅助类
 *
 * @package     PhalApi\Helper
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2015-05-30
 */

class ApiDesc {
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

    public function render() {
        $projectName = 'ApiDesc';

        $service = \Yii::$app->request->get('service');
        $rules = array();
        $returns = array();
        $description = '';
        $descComment = '//请使用@desc 注释';
        $exceptions = array();

        $exploade_service = explode('/', $service);
        switch (count($exploade_service)) {
            case 2:
                $classNameTpl = '\\app\\controllers\\%sController';
                $className = sprintf($classNameTpl, ucfirst($exploade_service[0]));
                $methodName = 'action' .ucfirst($exploade_service[1]);
                break;
            case 3:
                $classNameTpl = '\\app\\modules\\%s\\controllers\\%sController';
                $className = sprintf($classNameTpl, $exploade_service[0], ucfirst($exploade_service[1]));
                $methodName = 'action' .ucfirst($exploade_service[2]);
                break;
        }

        // 整合需要的类注释，包括父类注释
        $rClass = new \ReflectionClass($className);
        $classDocComment = $rClass->getDocComment();
        $needClassDocComment = '';
        foreach (explode("\n", $classDocComment) as $comment) {
            if (stripos($comment, '@exception') !== FALSE
                || stripos($comment, '@return') !== FALSE) {
                $needClassDocComment .=  "\n" . $comment;
            }
        }

        // 方法注释
        $rMethod = new \ReflectionMethod($className, $methodName);
        $docCommentArr = explode("\n", $needClassDocComment . "\n" . $rMethod->getDocComment());

        foreach ($docCommentArr as $comment) {
            $comment = trim($comment);

            //标题描述
            if (empty($description) && strpos($comment, '@') === FALSE && strpos($comment, '/') === FALSE) {
                $description = substr($comment, strpos($comment, '*') + 1);
                continue;
            }

            //@param注释
            $pos = stripos($comment, '@param');
            if ($pos !== FALSE) {
                $paramArr = explode(' ', trim(substr($comment, $pos + 7)), 3);
                $rules[$paramArr[0]] = $paramArr;
                continue;
            }

            //@desc注释
            $pos = stripos($comment, '@desc');
            if ($pos !== FALSE) {
                $descComment = substr($comment, $pos + 5);
                continue;
            }

            //@exception注释
            $pos = stripos($comment, '@exception');
            if ($pos !== FALSE) {
                $exArr = explode(' ', trim(substr($comment, $pos + 10)));
                $exceptions[$exArr[0]] = $exArr;
                continue;
            }

            //@return注释
            $pos = stripos($comment, '@return');
            if ($pos === FALSE) {
                continue;
            }

            $returnCommentArr = explode(' ', substr($comment, $pos + 8));
            //将数组中的空值过滤掉，同时将需要展示的值返回
            $returnCommentArr = array_values(array_filter($returnCommentArr));
            if (count($returnCommentArr) < 2) {
                continue;
            }
            if (!isset($returnCommentArr[2])) {
                $returnCommentArr[2] = '';	//可选的字段说明
            } else {
                //兼容处理有空格的注释
                $returnCommentArr[2] = implode(' ', array_slice($returnCommentArr, 2));
            }

            //以返回字段为key，保证覆盖
            $returns[$returnCommentArr[1]] = $returnCommentArr;
        }


        include dirname(__FILE__) . '/../tpl/api_desc_tpl.php';
    }
}
