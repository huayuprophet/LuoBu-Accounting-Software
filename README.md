# 罗布会计软件

### 简述

罗布会计软件，是一个采用php+mysql环境开发的**免费开源**的会计软件。

#### 注意：

1. 适用于部署到windows平台，linux等其他平台部署后可能有未知问题。
2. 参阅下文**一键安装**指南，否则必须自行部署php+mysql环境。

#### 软件特点：

- 本软件为免费网络共享软件，适用于中小企业会计核算。
- 软件提供了标准的财务会计序时账簿、总账、明细账、辅助项目、存货核算等功能。
- 软件框架提供了对数据库的管理，用户角色权限的设置，数据的批量导出等功能。
- PHP版采用了javascript + ajax，jquery，mysql存储过程及函数，用户体验极佳。

#### 优势：

- 安全、高效、自由
- 开源、免费，软件运作机制公开透明，有强大的开源社区技术支撑。
- B/S架构，轻松实现远程多端多用户登录操作。
- 代码条理清晰、模块化强大。用户可根据自身需求二次开发。

### 软件架构

- 软件采用B/S架构，既可以本地运行，也可以多端协作。不但可以企业内网私有化部署，也可以公开部署到云服务器（不推荐）。
- 路由代码结构模块化，方便二次开发。
- 前后端合一，避免重复请求，资源负载低。

### 安装教程

#### 自动安装：

##### [去官网下载一键安装包——罗布会计软件+集成环境](http://www.accsoft2008.com/ "点这里一键安装Windows罗布会计软件+PHP+Mysql集成环境")

#### 手动安装：

1. 安装好php+mysql环境，并且将项目git clone到站点根目录，需要Windows7 x64以上、推荐PHP7.0以上、Mysql5.7.17以上环境。
2. 修改数据库信息，路径`\config.inc.php`的`[pwb]`字段是数据库密码配置，默认密码为`12345678`）
3. 然后浏览器访问`http://localhost/index.html`链接即可。需用域名访问则在web服务器配置上host name。
4. 可将phpMyAdmin部署到`\phpMyAdmin`目录,或手动修改`\phpMyAdmin.php`的路径配置，若不需要软件内管理数据库可略过此步。
5. 本软件不需要配置伪静态。
6. 自行安装的集成环境，可能需要手动解禁exec函数。以宝塔面板为例：进入PHP设置->禁用函数->删除`exec`。并且IP地址注册为主机名、尽量关闭跨站攻击防御。
7. 如果只能从非Windows平台安装本软件，则推荐Linux-x86_64平台，稍作调整即可直接使用：调整数据临时目录为相对路径，打开配置文件`\config\conf.php`修改`fileName0`变量即可，建议改为`$fileName0='../AccSoft_Data/';`。若需要非Windows/Linux平台安装，则需要将对应平台Mysql5.7.17的`\bin\mysql`和`\bin\mysqldump`文件复制替换到项目`\config`目录。[Mysql历史版本下载页](https://downloads.mysql.com/archives/community/)

- 注意：本软件权限管理比较开放，增删账套时不需要后端初始化database密码，而是用户键入数据库密码登录，容易受中间人攻击技术劫持。所以用户角色权限管理和SSL证书高级加密尤为重要，且企业生产场景下不建议公开部署，以防会计信息泄露、丢失、注入；或者用户可以自行对以下路径文件进行二次开发，覆写敏感内容。

```
\config.inc.php
\config\database.php
\config\databaseCreateSubmit.php
\config\database[*].php
......
```

### 使用说明

Documentation使用文档暂缺，见[readme.pdf]

新建的账套，初始账号1001，初始密码123456

### 参与贡献

1. Fork 本仓库
2. 新建 Feat_xxx 分支
3. 提交代码
4. 新建 Pull Request
5. 星标本项目

### 软件技巧

暂略
