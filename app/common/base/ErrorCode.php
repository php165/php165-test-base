<?php
/**
 * Created by PhpStorm.
 * User: Bear
 * Date: 2020/8/31 0031
 * Time: 15:34
 *
 * @use [状态码库]
 */

namespace app\common\base;


class ErrorCode
{

    const SUCCESS_CODE = 200;
    const ERROR_CODE = 400;
    const ERROR_404 = 404;
    const API_ERR = 500;

    //login - 2002
    const LOGIN_ERROR = 2002; //登录

    //database - (421 -- 440)
    const  DB_CONNECT_ERROR = 421; //数据库连接失败
    const  DB_OPERATION_ERROR = 422; //数据操作失败
    const  DB_ADD_ERR = 423; //数据添加失败
    const  DB_RECORD_NOT_EXIST = 424; //数据记录不存在


    //noData  - 4001
    const NO_DATA_CODE = 4001; //暂无数据
    const NO_DATA_CHANGE_CODE = 4002; //数据未发生变化
    const NO_DATA_CONFIG_SITE_CODE = 4003; //没有站点配置信息
    const NO_DATA_IS_NEW_VERSION = 4006; //已是最新版本

    //Param - (521 -- 540)
    const PARAM_ERROR = 521; //参数错误


    //Operation - (541 -- 580) 操作
    const DELETE_MENU_NOTICE_CODE = 541;
    const NAME_IS_EXIT = 542;
    const DELETE_ORGAN_NOTICE_CODE = 543;
    const DELETE_ROLE_CODE = 544;
    const DEVICE_VERSION_COMPARE_CODE = 545; //设备版本比较
    const DEVICE_VERSION_UPDATE_CODE = 546; //设备最新版本

    //Exception - (601)
    const EXCEPTION_ERROR = 601;//异常提示
    const ADVERTISING_NOT_FIND = 6001;//广告未找到
    const ADVERTISING_POSITION_NOT_FIND = 6002;//广告位未找到
    const ADVERTISING_MARK_NOT_FIND = 6003;//广告标识未找到
    const ENABLE_NOT_DELETE = 6004;//已启用，不能删除
    const USE_NOT_DELETE = 6005;//使用中，不能删除
    const SUCCESSFULLY_DELETE = 6006;//删除成功
    const FAIL_TO_DELETE = 6007;//删除失败
    const ADVERTISING_MARK_ALREADY_EXIST = 6008;//广告标识已经存在
    const ADVERTISING_POSITION_ALREADY_EXIST = 6009;//广告位已经存在
    const ADVERTISING_ALREADY_EXIST = 6010;//广告已经存在
    const SUCCESSFULLY_ADDED  = 6011;//添加成功
    const FAIL_TO_ADD  = 6012;//添加失败
    const SUCCESSFULLY_EDIT  = 6013;//编辑成功
    const FAIL_TO_EDIT  = 6014;//编辑失败
    const OPERATE_SUCCESSFULLY   = 6015;//操作成功
    const OPERATION_FAILURE  = 6016;//操作失败
    const ABNORMAL_STATE  = 6017;//状态异常
    const VALUE_OUT_OF_RANGE  = 6018;//取值不在范围内
    const ADVERTISING_IS_RUNNING_AT_WRONG_TIME  = 6019;//广告播放时间错误

    //FileException (701 -- 720)

    const FILE_IMAGE_SIZE_ERROR = 701;//超过图片允许最大上传质量
    const FILE_VIDEO_SIZE_ERROR = 702;//超过视频允许最大上传质量
    const FILE_FILE_SIZE_ERROR = 703;//超过文件允许最大上传质量

    const FILE_IMAGE_EXT_ERROR = 704;//图片上传格式不正确
    const FILE_VIDEO_EXT_ERROR = 705;//视频上传格式不正确
    const FILE_FILE_EXT_ERROR = 706;//文件上传格式不正确
    const FILE_NO_PARAM_ERROR = 707;//文件上传格式不正确

    const FILE_ALIYUN_OSS_CONFIG_FAIL = 708;//上传到阿里云OSS配置文件错误
    const FILE_ALIYUN_OSS_FAIL = 709;//上传到阿里云OSS错误

    const FILE_DELETE_ERROR = 710;//删除本地文件失败

    //FileException
    const MENU_IS_NOT_NEW = 1000;
    const LOGIN_IN = 1001;
    const PHONE_IS_EXIT = 1002;
    const INVALID_USER_INFO = 1003;
    const INVALID_HEADER_INFO = 1004;
    const INVALID_PASSWORD_INFO = 1005;
    const USER_IS_DISABLED = 1006;
    const LOGIN_SUCCESS_IN = 1007;
    const LOGIN_ERROR_IN = 1008;
    const LOGIN_OUT_INFO = 1009;
    const PASSWORD_IS_EMPTY = 1010;
    const MOBILE_IS_EMPTY = 1011;
    const NOT_VISIT_ACCESS = 1012;
    const INVALID_SECRET_KEY = 1013;
    const SUCCESS_SECRET_KEY = 1014;

    //成功提示语
    protected static $successMsg = [
        self::SUCCESS_CODE => '请求成功',
    ];
    //失败提示语
    protected static $errorMsg = [
        self::ERROR_CODE => '失败',
        self::LOGIN_ERROR => '设备已注册，请勿重复注册',
        self::NO_DATA_CHANGE_CODE => '数据未发生变化',
        self::NO_DATA_CODE => '暂无数据',
        self::NO_DATA_CONFIG_SITE_CODE => '没有站点配置信息',
        self::DELETE_MENU_NOTICE_CODE => '请先删除子菜单',
        self::FILE_NO_PARAM_ERROR => '缺少上传必传参数',
        self::FILE_IMAGE_SIZE_ERROR => '超过图片允许最大上传质量',
        self::FILE_VIDEO_SIZE_ERROR => '超过视频允许最大上传质量',
        self::FILE_FILE_SIZE_ERROR => '超过文件允许最大上传质量',
        self::FILE_IMAGE_EXT_ERROR => '图片上传格式不正确',
        self::FILE_VIDEO_EXT_ERROR => '视频上传格式不正确',
        self::FILE_FILE_EXT_ERROR => '文件上传格式不正确',
        self::FILE_ALIYUN_OSS_CONFIG_FAIL => '上传到阿里云OSS配置文件错误',
        self::FILE_ALIYUN_OSS_FAIL => '上传到阿里云OSS错误',
        self::FILE_DELETE_ERROR => '删除本地文件失败',
        self::PHONE_IS_EXIT => '手机号已存在',
        self::INVALID_USER_INFO => '无效的信息',
        self::INVALID_HEADER_INFO => '无效的header信息',
        self::INVALID_PASSWORD_INFO => '用户名密码不正确',
        self::USER_IS_DISABLED => '用户已被封禁，请联系管理员',
        self::PASSWORD_IS_EMPTY => '密码不能为空',
        self::MOBILE_IS_EMPTY => '账户不能为空',
        self::NAME_IS_EXIT => '名称已存在',
        self::NOT_VISIT_ACCESS => '没有访问的权限',
        self::DELETE_ORGAN_NOTICE_CODE => '请先删除子级',
        self::MENU_IS_NOT_NEW => '菜单目录已发生变化，点击加载更新',
        self::NO_DATA_IS_NEW_VERSION => '已是最新版本',
        self::DELETE_ROLE_CODE => '该角色已授权管理员，请先删除管理员',
        self::LOGIN_IN => '已失效，请重新登陆',
        self::DEVICE_VERSION_COMPARE_CODE => '更新的版本不能小于当前设备的版本',
        self::DEVICE_VERSION_UPDATE_CODE => '当前设备已是最新版本',
        self::INVALID_SECRET_KEY => '密钥错误，请重新输入,如有问题，请联系后台管理人员',
        self::SUCCESS_SECRET_KEY => '成功',
        self::ADVERTISING_NOT_FIND => '广告未找到',
        self::ADVERTISING_POSITION_NOT_FIND => '广告位未找到',
        self::ADVERTISING_MARK_NOT_FIND => '广告标识未找到',
        self::ENABLE_NOT_DELETE => '已启用，不能删除',
        self::USE_NOT_DELETE => '使用中，不能删除',
        self::SUCCESSFULLY_DELETE => '删除成功',
        self::FAIL_TO_DELETE => '删除失败',
        self::ADVERTISING_MARK_ALREADY_EXIST => '广告标识已经存在',
        self::ADVERTISING_POSITION_ALREADY_EXIST => '广告位已经存在',
        self::ADVERTISING_ALREADY_EXIST => '广告已经存在',
        self::SUCCESSFULLY_ADDED => '添加成功',
        self::FAIL_TO_ADD => '添加失败',
        self::SUCCESSFULLY_EDIT => '编辑成功',
        self::FAIL_TO_EDIT => '编辑失败',
        self::OPERATE_SUCCESSFULLY => '操作成功',
        self::OPERATION_FAILURE => '操作失败',
        self::ABNORMAL_STATE => '状态异常',
        self::VALUE_OUT_OF_RANGE => '取值不在范围内',
        self::ADVERTISING_IS_RUNNING_AT_WRONG_TIME => '广告播放时间错误',
    ];


    /**
     * 根据错误码获取错误信息
     *
     * @param $code
     * @param $ident
     * @return mixed
     * @author jiangjiaxiong
     *
     */
    public static function getErrorCodeMsg($code, $msg = null)
    {
        if (!is_int($code)) {
            return '错误码只能是整数';
        }
        switch ($code) {
            //成功
            case self::SUCCESS_CODE:
                if (empty($msg)) {
                    return self::$successMsg[$code];
                }
                return $msg;
                break;
            //参数错误
            case self::PARAM_ERROR:
                if (empty($msg)) {
                    $msg = '参数错误';
                }
                return $msg;
                break;
            //逻辑错误
            case self::EXCEPTION_ERROR:
                if (empty($msg)) {
                    $msg = '系统错误';
                }
                return $msg;
                break;
            //其他
            default:
                if (!isset(self::$errorMsg[$code])) {
                    return 'API出现致命的非预期错误';
                }
                return self::$errorMsg[$code];
                break;
        }
    }
}