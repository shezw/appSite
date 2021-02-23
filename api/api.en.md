### Introduction of API container development 

[简体中文](api.cn.md) | English

API container user ASRoute to parse requests。
The default route format is: `api/namespace/class/id`

API class need inherit from ASAPI class and implement the run() method.
Every object will merge all the requesting parameters ( _GET,_POST,Payload ) in to `$this->params`.

#### Folder struct:
There are two folders to retain the API files developed by appsite and yours.

```
/api
    /default
        {$namespace}
            {$class}.php
    /custom
```
API container will execute the files in the custom folder first.
Thus, /custom/a/b.php will be executed instead of /default/a/b.php。

#### Write a API class:
namespace/class.php is the recommended way.
The crucial mission is to complete the run() method, and give it a returning data as ASResult type.
The api class is designed in two scope public and private( use system )
You can call the system api in any other code but not in networking requests.

See public API sample [custom/sample/test](custom/sample/test.php)

See private API sample [custom/sample/secret](custom/sample/secret.php)

> Please confirm that your run method will return an ASResult Object, that API container will output the result automatically.

#### How to use

See completed sample [account/regist](default/account/regist.php)

URL: `https://yourhost/api/account/regist?username=test&password=pass`

This request is willing to execute the run() in user/regist class.
The requesting params is sustained at `$this->params` like that:
```php
['username'=>'test','password'=>'pass'];
```
