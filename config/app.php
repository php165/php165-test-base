<?php
// +----------------------------------------------------------------------
// | 应用设置
// +----------------------------------------------------------------------

return [
    // 应用地址
    'app_host' => env('app.host', ''),
    // 应用的命名空间
    'app_namespace' => '',
    // 是否启用路由
    'with_route' => true,
    // 开启应用快速访问
    'app_express' => true,
    // 默认应用
    'default_app' => 'index',
    // 默认时区
    'default_timezone' => 'Asia/Shanghai',

    // 应用映射（自动多应用模式有效）
    'app_map' => [],
    // 域名绑定（自动多应用模式有效）
    'domain_bind' => [],
    // 禁止URL访问的应用列表（自动多应用模式有效）
    'deny_app_list' => [],

    // 异常页面的模板文件
    'exception_tmpl' => app()->getThinkPath() . 'tpl/think_exception.tpl',

    // 错误显示信息,非调试模式有效
    'error_message' => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg' => true,

    //用户登陆缓存在线时长
    'on_line' => 7200,

    //阿里云系列
    'aliyun' => [
        // 阿里云物联网设置（中创环保）
        'iot' => [
            'accessKeyId' => 'LTAI4G98z5KCwoWUpgWFgAnN',
            'accessKeySecret' => '2NT8gVMzQcZfkJ5EHBOMp2hnHx4eDK',
            'IotInstanceId' => 'iot-cn-nif1ti6r203',
            'MasterProductKey' => 'g0ohfabDdt4',    //主机设备
            'HandInProductKey' => 'g0ohZpzRyYS',    //手持设备
            'RemoteMasterProductKey' => 'g0ohQOkQwiY',    //主机设备（遥控器）
            'MasterDevicePrefix' => 'ZCZJ_',  //主机设备前缀
            'HandInDevicePrefix' => 'ZCSC_',  //手持设备前缀
            'RemoteMasterDevicePrefix' => 'ZCYK_',  //主机设备（遥控器）前缀
            'DevicePwd' => '000000',  //设备初始密码
        ],
        'topic' => [
            'reset_host_lora' => '/user/reset_host_lora',//重置主机lora信道和绑定关系专用
            'lora_setting' => '/user/lora_setting',//lora 信道设置专用
            'setting_list' => '/user/setting_list',//使用rrpc下发消息时调用的topic专门用来下发，配置修改消息，消息里是配置列表，包含config_id,config_name,config_value
            'single_device_version_update' => '/user/single_device_version_update',//单一设备版本更新
            'reset_device_code' => '/user/reset_device_code',//重置主机识别码
            'publish_advertisement' => '/user/publish_advertisement',//向单台设备发布广告
        ],
        //oss存储
        'oss' => [
            'url' => env('oss.url','http://image.zcpurifier.com/'),
            'accessKeyId' => 'LTAI4G98z5KCwoWUpgWFgAnN',//中创服务器的
            'accessKeySecret' => '2NT8gVMzQcZfkJ5EHBOMp2hnHx4eDK',
            'endpoint' => 'oss-cn-beijing.aliyuncs.com',
            'bucket' => env('oss.bucket' , 'zcxj-oss'),
        ],

    ],
    //天气
    'weather' => [
        //815081410@qq.com注册的账户 https://www.tianqiapi.com
        'appid' => '63322489',
        'appsecret' => '8DXWXNrW',
    ],
    //异步任务地址
    'asyncTasks' => [
        //广告相关异步任务
        'advertising' => [
            'send' => env('advertising.send', 'http://server.zcpurifier.com/AdManager/send'),
            'revoke' => env('advertising.revoke', 'http://server.zcpurifier.com/AdManager/revoke'),
        ],
        //软件版本相关异步任务
        'version' => [],
    ],
];
