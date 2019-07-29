Thinkadx-v2
===============

它可以快速让你搭建起一个通用的后台

V2版本比V1版本改进了不少,V1版本应该不会再去维护了

## 注意

1. ThinkPHP5.1的运行环境要求PHP5.6以上。

2. MySql >= 5.5.0

3. 运行目录是public

## 技术栈(后端)
1. php5.6

2. mysql

3. thinkphp5.1

## 技术栈(前端)

> 由使用的模板决定

## 安装

使用git安装

~~~
1、git clone https://gitee.com/first_pig/thinkadx-v2.git
拉取thinkphp核心框架,当然你也可以按照你或者tp文档的方式进行安装

2、git clone v5.1.37 https://gitee.com/liu21st/framework.git thinkphp
~~~

初始化MySql
[apache、nginx配置手册](https://www.kancloud.cn/manual/thinkphp5_1/353955)

~~~
1、打开根目录的/config/database.php配置文件把自己的数据库(字符集utf8)信息填进去,然后在根目录打开命令行(能运行到根目录的think就行)

2、php think thinkadx --init mysql
这条命令意思是导入数据到数据库并创建资源文件(图片),返回 "init success!!!"表示数据导入完成

3、到这里基本上已经搭建起来了,把apache或者nginx配置一下就行,默认是已经配置好apache的了,不会的话看官方文档
~~~

搭建模板(目前只有这一个模板,后期可能会增加更多样的模板)
+ [搭建模板教程](https://gitee.com/first_pig/thinkadx-template)

## 社交
- 企鹅群 801456419


