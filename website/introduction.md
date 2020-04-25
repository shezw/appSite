### 网站部分介绍 Introduction of website part
网站部分用于建立供用户访问的网站前台、以及共享部分通用的网站静态组件（资源），这些静态资源也可以部署在云存储中已提高响应速率。

The website part is to establish the front end of the website for users, and to share common static web components/resources. The static resources can also be deployed in cloud storage to improve the website QPS.


Nginx Rewrite:
```
location /favicon.ico {break;}
location /website/static { break; }
location /website/theme { break; }
location /manager/theme { break; }
location /api {
	rewrite ^(/api.*)*$ /api/?path=$1 break;
}
location /manager {
	rewrite ^(/manager.*)*$ /manager/?path=$1 break;
}
location /website {
	rewrite ^(/website.*)*$ /website/?path=$1 break;
}
location / {
	rewrite ^(/.*)*$ /website/?path=$1 break;
}
```
如果希望隐藏自己的后台系统，只需要先在后台的修改根目录配置，之后将管理后台部分的重写机制按照如下修改即可。

If you want hide your management system pages, modify the rootPath setting and rewrite like follow config.

```
location /manager321 {
	rewrite ^(/manager321.*)*$ /manager/?path=$1 break;
}
location /manager {
	rewrite ^(/manager.*)*$ / break;
}
```