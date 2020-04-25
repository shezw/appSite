### 接口开发使用说明  

接口使用ASRoute进行路由解析。
解析格式为: `api/namespace/class/id`

接口类继承并实现ASAPI类,主函数为run()
每一个实例化类自动获取请求中的参数(_GET,_POST,Payload)，整合到$this-params中。

#### 文件组织结构:
接口类文件由 default,custom 两个文件夹组成。分别存放引擎默认提供的接口以及自定义接口文件。
```
/api
    /default
        {$namespace}
            {$class}.php
    /custom
```
API入口页会优先扫描custom文件夹内对应接口类文件。
即 /custom/a/b.php 会替换 /default/a/b.php 中的实现。

#### 接口类格式:
接口类推荐使用 namespace/class.php的方式来创建,需要继承ASAPI类，运行主函数为run()。
开发接口类时，只需要实现run()函数即可。
接口类分为 public, system两种，system接口无法从外部入口处调用。
```
# 参考 custom/sample/secret.php
```
> run()方法需要返回ASResult结果，系统会自动输出到请求来源。


#### 示例
请求接口: `https://yourhost/api/user/regist?username=test&password=pass`
会进入到API功能，使用user文件夹下的regist类.
接口中使用 `$this->params` 可以获取到数组格式参数:
```php
['username'=>'test','password'=>'pass'];
```
