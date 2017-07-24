## Home page ##
[http://www.liyangweb.com](http://www.liyangweb.com)

```
How to install?

composer require kaopur/yii2-doc-online
```

### How to use? ###
1. Install the library.
2. Create a new module config to web.php like this:
    ```
    'modules' => [
        'doconline' => [
            'class' => 'Kaopur\yii2_doc_online\Module',
            'defaultRoute' => 'index', //默认控制器
            'appControllers' => true, //是否检测app\controllers命名空间下的控制器
            'suffix' => '', //api后缀
            'prefix' => '', //api前缀
            'modules' => [  //需要生成文档的模块
                'the_module_name'
            ],
        ],
    ```
3. Open the url from you browser. `http://url.com?r=doconline`