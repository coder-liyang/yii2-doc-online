## Home page ##
[http://www.liyangweb.com/php/311.html](http://www.liyangweb.com/php/311.html)
### How to install? ###
```
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
            'modules' => [  //需要生成文档的模块命名空间
                'app\modules\admin\Module',
            ],
        ],
    ],
    ```
3. Open the url from you browser. `http://url.com?r=doconline`

### Example ###
#### The code like this: ####
```
/**
 * 这是一个测试的Api
 * @desc 列举所有的注释格式
 * @param string $user_type |用户类型|yes|其他说明|
 * @param int $sex |性别|no|0:不限 1:男 2:女|
 * @return int status 操作码，0表示成功
 * @return array list 用户列表
 * @return int list[].id 用户ID
 * @return string list[].name 用户名字
 * @return string msg 提示信息
 * @exception 400 参数传递错误
 * @exception 500 服务器内部错误
 */
public function actionDemoapi($user_type, $sex)
{
    $result = [
        'status' => 0,
        'list' => [
            'id' => 1,
            'name' => 'kaopur'
        ],
        'msg' => 'OK'
    ];
    return \yii\helpers\Json::encode($result);
}
```
#### Show ####
![image](https://raw.githubusercontent.com/kaopur/yii2-doc-online/master/imgs/desc_page.png)