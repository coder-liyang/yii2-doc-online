<?php
namespace Kaopur\yii2_doc_online;

defined('D_S') || define('D_S', DIRECTORY_SEPARATOR);
define('API_ROOT', '.');

class ApiList {
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
     * 需要生成文档的模块名
     * @param array $modules
     */
    public function render($modules = []) {
        $allApiS = [];
        $projectName = 'yii-doc-online'; //todo
        // 主题风格，fold = 折叠，expand = 展开
        $theme = \Yii::$app->request->get('type', 'fold');
        if (!in_array($theme, array('fold', 'expand'))) {
            $theme = 'fold';
        }
        $classesName = [];
        $apiDirName = $this->appControllers?'../controllers':'';
        // 处理最外层的控制器 \app\controllers
        if ($this->appControllers) {
            $files = listDir(API_ROOT . D_S . $apiDirName);
            $classesName = array_map(function($file){
                $item = \Yii::$app->modules['doconline']->item;
                $classNameTemp = "/$item" . rtrim(strstr($file, '/controllers/'), '.php');
                $className = str_replace('/', '\\', $classNameTemp);
                return $className;
            }, $files);
        }
        $modulesClassesNameTemp = array_map(function($module){
            //遍历module下的所有控制器
            $moduleDirName = '../modules/' . $module . '/controllers';
            $moduleFiles = listDir(API_ROOT . D_S . $moduleDirName);
            return array_map(function($moduleFile) use ($module) {
                $item = \Yii::$app->modules['doconline']->item;
                $namespace = "\\$item\\modules\\%s\\controllers\\%s";
                $className = rtrim(substr($moduleFile, strrpos($moduleFile, D_S) + 1), '.php');
                return sprintf($namespace, $module, $className);
            }, $moduleFiles);
        }, $modules);
        $modulesClassesName = [];
        foreach ($modulesClassesNameTemp as $moduleClassName) {
            $modulesClassesName = array_merge($modulesClassesName, $moduleClassName);
        }

        $classesName = array_merge($classesName, $modulesClassesName);
        foreach ($classesName as $k=>$v) {
            if (substr($v, -10) != 'Controller') {
                unset($classesName[$k]);
            }
        }
        foreach ($classesName as $className) {
            $explodeClassName = explode('\\', trim($className, '\\'));
            switch (count($explodeClassName)) {
                case 3:
                    $apiServerShortName = $explodeClassName[2];
                    $service_1 = strtolower(substr($apiServerShortName, 0, -10));
                    break;
                case 5:
                    $apiServerShortName = $explodeClassName[2] . '.' . $explodeClassName[4];
                    $service_1 = strtolower(substr($apiServerShortName, 0, -10));
                    break;
            }
            $apiServer = $className;
            if (!class_exists($className)) {
                continue;
            }
            //  左菜单的标题
            $ref        = new \ReflectionClass($apiServer);
            $title      = "//请检测接口服务注释($apiServerShortName)";
            $desc       = '//请使用@desc 注释';
            $docComment = $ref->getDocComment();
            if ($docComment !== false) {
                $docCommentArr = explode("\n", $docComment);
                $comment       = trim($docCommentArr[1]);
                $title         = trim(substr($comment, strpos($comment, '*') + 1));
                foreach ($docCommentArr as $comment) {
                    $pos = stripos($comment, '@desc');
                    if ($pos !== false) {
                        $desc = substr($comment, $pos + 5);
                    }
                }
            }
            $allApiS[$apiServerShortName]['title'] = $title;
            $allApiS[$apiServerShortName]['desc']  = $desc;
            $allApiS[$apiServerShortName]['methods'] = [];
            // 待排除的方法
            $allYiiMethods = get_class_methods('yii\web\Controller');
            $method = array_diff(get_class_methods($apiServer), $allYiiMethods);
            sort($method);
            foreach ($method as $mValue) {
                $service = [];
                $service[] = $service_1;
                $rMethod = new \Reflectionmethod($apiServer, $mValue);
                if (!$rMethod->isPublic() || strpos($mValue, '__') === 0) {
                    continue;
                }
                //筛选接口方法,1.以action开头 [2.以设置的接口前缀开头 3.以设置的接口后缀结尾]
                $prefix = 'action' . ucfirst($this->prefix);
                if (strpos($mValue, $prefix) !== 0) {
                    continue;
                }
                if ($this->suffix !== '') {
                    if (strrev(substr(strrev($mValue), 0, strlen($this->suffix))) != $this->suffix) {
                        continue;
                    }
                }
                $title      = '//请检测函数注释';
                $desc       = '//请使用@desc 注释';
                $docComment = $rMethod->getDocComment();
                if ($docComment !== false) {
                    $docCommentArr = explode("\n", $docComment);
                    $comment       = trim($docCommentArr[1]);
                    $title         = trim(substr($comment, strpos($comment, '*') + 1));

                    foreach ($docCommentArr as $comment) {
                        $pos = stripos($comment, '@desc');
                        if ($pos !== false) {
                            $desc = substr($comment, $pos + 5);
                        }
                    }
                }
                //兼容模块里的控制器
                $service[] = strtolower(substr($mValue, 6));
                $service_string = implode('/', $service);
                $allApiS[$apiServerShortName]['methods'][$service_string] = array(
                    'service' => $service_string,
                    'title'   => $title,
                    'desc'    => $desc,
                );
            }

            $webRoot = '';


            //echo json_encode($allApiS) ;
            //字典排列
            ksort($allApiS);

        }
        foreach ($allApiS as $key=>$allApi) {
            if (count($allApi['methods']) == 0) {
                unset($allApiS[$key]);
            }
        }
        include dirname(__FILE__) . '/../tpl/api_list_tpl.php';
    }
}

function listDir($dir) {
    $dir .= substr($dir, -1) == D_S ? '' : D_S;
    $dirInfo = array();
    foreach (glob($dir . '*') as $v) {
        if (is_dir($v)) {
            $dirInfo = array_merge($dirInfo, listDir($v));
        } else {
            $dirInfo[] = $v;
        }
    }
    return $dirInfo;
}

function saveHtml($webRoot, $name, $string){
    $dir = $webRoot . D_S . 'doc';
    if (!is_dir ( $dir)){
        mkdir ( $dir);
    }
    $handle = fopen ( $dir . DIRECTORY_SEPARATOR . $name . '.html', 'wb');
    fwrite ( $handle, $string);
    fclose ( $handle);
}

