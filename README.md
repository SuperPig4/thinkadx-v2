Thinkadx-v2
===============

## 为了加速迭代v1.0.2-RC版本后将不向后兼容


它可以快速让你搭建起一个通用的后台

V2版本比V1版本改进了不少,V1版本不会再去维护了

## 注意

1. ThinkPHP5.1的运行环境要求PHP7.2以上。

2. MySql >= 5.5.0

3. 运行目录是public

## 技术栈(后端)
1. | >= php7.2 

2. mysql

3. thinkphp5.1

## 技术栈(前端)

> 由使用的模板决定

## 安装

使用git安装

~~~
1、git clone https://gitee.com/first_pig/thinkadx-v2.git
拉取thinkphp核心框架,当然你也可以按照你或者tp文档的方式进行安装

2、git clone -b v5.1.* https://gitee.com/liu21st/framework.git thinkphp
~~~

初始化
[apache、nginx配置手册](https://www.kancloud.cn/manual/thinkphp5_1/353955)

~~~
1、创建.env并将.example.env内容copy到.env中,根据注释配置.

2、执行命令(项目根目录)
    1、 composer install 安装依赖
    2、 php think migrate:run 运行数据库迁移
    3、 php think thinkadx --init mysql-data 创建数据和相关资源文件

~~~

+ [开发文档](https://gitee.com/first_pig/thinkadx-v2/wikis/Thinkadx-Base)

搭建模板(目前只有这一个模板,后期可能会增加更多样的模板)
+ [搭建模板教程](https://gitee.com/first_pig/thinkadx-template)


## 长期内目标
- 解耦 AdxToken和LoadData并支持数据库
- 增加 定时器
- 完善 容器化(docker)
- 完善 Oauth2扩展授权(至少完成 一种模式)
- 完善 Captcha扩展(至少完成 一种验证码种类)
- 完善 RuleAuth扩展


