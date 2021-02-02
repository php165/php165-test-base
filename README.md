ThinkPHP 6.0
===============

> 运行环境要求PHP7.1+。

[官方应用服务市场](https://www.thinkphp.cn/service) | [`ThinkPHP`开发者扶持计划](https://sites.thinkphp.cn/1782366)

ThinkPHPV6.0版本由[亿速云](https://www.yisu.com/)独家赞助发布。

## 主要新特性

* 采用`PHP7`强类型（严格模式）
* 支持更多的`PSR`规范
* 原生多应用支持
* 更强大和易用的查询
* 全新的事件系统
* 模型事件和数据库事件统一纳入事件系统
* 模板引擎分离出核心
* 内部功能中间件化
* SESSION/Cookie机制改进
* 对Swoole以及协程支持改进
* 对IDE更加友好
* 统一和精简大量用法

## 前端页面
CoolCms是一个前后端完全分离的项目，前端采用Vue构建，如需要可视化配置的请移步：

**项目构成**

- ThinkPHP v6.*
- Vue 2.0
- ...

**功能简介**

 1. 系统设置
 2. 设备统计分析
 3. 系统设置管理
 4. 设备管理
 5. 广告管理
 6. 本地二次开发友好
 7. ...
 
 ```
  CoolCms（PHP部分）
  ├─ 系统设置
  |  ├─ 菜单管理 - 编辑访客权限，处理菜单父子关系，被权限系统依赖（极为重要）
  |  ├─ 用户管理 - 添加新用户，封号，删号以及给账号分配权限组
  |  ├─ 角色管理 - 权限组管理，给权限组添加权限，将用户提出权限组
  |  ├─ 软件升级 - 控制app版本，便于app升级
  |  └─ 操作日志 - 记录管理员的操作，用于追责，回溯和备案
  ├─ 设备管理
  |  ├─ 设备列表 - 手持机，主机管理，屏幕显示的参数配置，单设备推送升级，TVOC,湿度，PM2.5...等数据统计
  |  ├─ 属性管理 - 所属人群（设备适用的人群，例如：老人，孩子...等等），场所属性（设备所属场合，学校，公司...等等），机构列表（设备所属的机构）
  |  ├─ 设备配置 - 用于动态设置设备的状态，例如，数据限制，数据上传，屏幕配置，设备参数，维修等等
  |  └─ 编号管理 - 编号列表（识别码管理，及生成规则） 批次列表（用于管理设备生产数量）
  ├─ 广告管理
  |  ├─ 广告列表 - 动态添加图片/视频广告内容，全量发布到对应的设备并展示，以及编辑，撤销，删除等
  |  ├─ 广告类型列表 - 添加广告时需要对应的广告类型 例如图片，视频
  |  └─ 广告公司列表 - 广告投放的信息对应的公司
  |  ...
  ```
 
## 文档

[完全开发手册](https://www.kancloud.cn/manual/thinkphp6_0/content)

## 参与开发

请参阅 [ThinkPHP 核心框架包](https://github.com/top-think/framework)。

## 版权信息

ThinkPHP遵循Apache2开源协议发布，并提供免费使用。

本项目包含的第三方源码和二进制文件之版权信息另行标注。

版权所有Copyright © 2006-2020 by ThinkPHP (http://thinkphp.cn)

All rights reserved。

ThinkPHP® 商标和著作权所有者为上海顶想信息科技有限公司。

更多细节参阅 [LICENSE.txt](LICENSE.txt)