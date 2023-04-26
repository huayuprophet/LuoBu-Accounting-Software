# 通用财务系统软件👍🚀️🚀️

### 简述

曾用名：罗布会计软件。本软件是一个采用php+mysql环境开发的**免费开源**的中小会计账务软件。

喜欢项目请点击右上角的“star”以为星标。

|[官方网站](http://www.accsoft2008.com/)|[官方Q群](https://shang.qq.com/wpa/qunwpa?idkey=e42a8a107e989ef014be2938e815b420fd3dc64c47d3cda351190b0227129e5b)|[官方文档](http://www.accsoft2008.com/readme.htm)|
|:--:|:--:|:--:|

![截图](src/a37b895a498b870bb99ca1fb292d056d.png)

提交反馈：点击顶部`issue`导航按钮、或入Q群反馈。

申请进群时请填写你的来源：如“来自gitee开源仓库”。

#### 注意：

1. 适用于部署到windows平台，linux等其他平台部署后可能有未知问题。
2. 参阅下文**Windows自动安装**指南，否则必须自行部署php+mysql环境。

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

#### Windows自动安装：

##### [去官网下载一键安装包——罗布会计软件+集成环境](http://www.accsoft2008.com/ "点这里一键安装Windows罗布会计软件+PHP+Mysql集成环境")

***

#### 手动安装：

1. 安装好php+mysql环境，并且将项目git clone到站点根目录，**环境要求：需要Windows8 x64以上、PHP7.4+、Mysql8+环境**。
2. 修改数据库信息，路径`\config.inc.php`的`[pwb]`字段是数据库密码配置，默认密码为`12345678`）
3. 然后浏览器访问`http://localhost/index.html`链接即可。需用域名访问则在web服务器配置上host name。
4. 可将phpMyAdmin部署到`\phpMyAdmin`目录,或手动修改`\phpMyAdmin.php`的路径配置，若不需要软件内管理数据库可略过此步。
5. 本软件不需要配置伪静态。
6. 自行安装的集成环境，可能需要手动解禁exec函数。以宝塔面板为例：进入PHP设置->禁用函数->删除`exec`。并且IP地址注册为主机名、尽量关闭跨站攻击防御。
7. 非Windows平台仅测试了Linux-x86_64平台，稍作调整即可直接使用：调整数据临时目录为相对路径，打开配置文件`\config\conf.php`修改`fileName0`变量即可，建议改为`$fileName0='../AccSoft_Data/';`，如果新建账套时出现错误，则尝试安装Mysql Community Client程序（Client版本尽量在5.7.17及以上）[Mysql历史版本下载页](https://downloads.mysql.com/archives/community/)~~，安装无效可尝试将项目`\config`目录下的`mysql`和`mysqldump`文件拷贝到`/usr/bin/`并重启服务器。安装的Mysql Community Client程序必须适用于您的系统平台。~~
8. 非Windows平台下，账套或其他文件的导出或有问题，但未做测试。有测试的朋友可帮忙将错误提示发送到issue页面反馈给我谢谢。

- 注意：本软件权限管理比较开放，增删账套时不需要后端初始化database密码，而是用户键入数据库密码登录，容易受中间人攻击技术劫持。所以用户角色权限管理和SSL证书高级加密尤为重要，且企业生产场景下不建议公开部署，以防会计信息泄露、丢失、注入；或者用户可以自行对以下路径文件进行二次开发，覆写敏感内容。

```
\config.inc.php
\config\database.php
\config\databaseCreateSubmit.php
\config\database[*].php
......
```

### 使用说明

Documentation使用文档暂缺，见仓库中[readme.pdf](https://gitee.com/hua_yutong/LuoBu-Accounting-Software/raw/master/readme.pdf)文件。

新建的账套，初始账号1001，初始密码123456。

删除重建的账套可能提示“文件夹已存在”错误，删除临时文件夹`D:/AccSoft_Data/`即可解决。

### 更新日志

打钩项目->已解决，未打钩项目->已发现但待解决。

- [x] 为火狐浏览器提供兼容-修复布局错误    2023/03/25
- [x] 为Linux提供初步兼容，需根据**手动安装**教程手动适配    2023/03/21
- [ ] 修复当数据库非本地回环时，出现建删账套错误的bug
- [ ] 弃用mysql/mysqldump shell方案，使用php执行sql语句方法平替、

其他暂未发现的问题，请进qq群或issue提交到管理员谢谢。

### 参与贡献

1. Fork 本仓库
2. 新建 Feat_xxx 分支
3. 提交代码
4. 新建 Pull Request
5. 星标本仓库

### 软件技巧

暂略
