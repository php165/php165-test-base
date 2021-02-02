<?php
// +----------------------------------------------------------------------
// | CoolCms [ DEVELOPMENT IS SO SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2019 http://www.coolcms.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +---------------------------------------------------------------------
// | Author: Alan <251956250@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2020/6/22 16:35
// +----------------------------------------------------------------------
// | Desc: 阿里云OSS封装，如果需要外部调用，务必使用静态方法
// +----------------------------------------------------------------------
namespace app\common\traits;

use OSS\Core\OssException;

/**
 * 阿里云OSS上传公共类
 * Trait AliCloudOssTrait
 * @dec:如使用该类，请先 composer require aliyuncs/oss-sdk-php
 * @author: Alan <alanstars@qq.com>
 * @package app\common\traits
 */
trait AliCloudOssTrait
{
    private static function aliCloudOssConfig(){
//        $config = cache('configInfo');
//        if(isset($config[5]) && !empty($config[5])){
//            return $config[5];
//        }else{
//            return false;
//        }
        return config('app.aliyun.oss');
    }

    /**
     * 上传文件到阿里云OSS
     * @dec:
     * @author: Alan <alanstars@qq.com>
     * @param $file     所上传文件的本地路径
     * @return array
     */
    public static function uploadAliyunOss($files){
        $config = self::aliCloudOssConfig();
        if(!$config){
            return ['code'=>708,'data'=>'暂无第三方配置，无法上传阿里云OSS'];
        }
        if (!isset($config['accessKeyId']) || !isset($config['accessKeySecret']) || !isset($config['endpoint']) || !isset($config['bucket'])) {
            return ['code' => 708, 'data' => '配置文件错误'];
        } else {
            try {
                $oss = new \OSS\OssClient($config['accessKeyId'], $config['accessKeySecret'], $config['endpoint']);
                $filePath = transformSystemSlash(config('filesystem.disks.public.root').DIRECTORY_SEPARATOR.$files);
                $object = transformSystemSlash('uploads'.DIRECTORY_SEPARATOR.$files);
                $result = $oss->uploadFile($config['bucket'], $object, $filePath);

                if ($result['info']['http_code'] == 200) {
                    return ['code'=>200,'data'=>'/'.$object];
                } else {
                    return ['code' => 709, 'data' => '上传阿里云OSS失败'];
                }
            } catch (OssException $exception) {
                return ['code' => 709, 'data' => $exception->getErrorMessage()];
            }
        }
    }

    /**
     * 删除阿里云OSS文件
     * @dec:
     * @author: Alan <alanstars@qq.com>
     * @param $files
     */
    public static function delAliyunOss($files){
        $config = self::aliCloudOssConfig();
        try {
            $oss = new \OSS\OssClient($config['accessKeyId'], $config['accessKeySecret'], $config['endpoint']);
            if(is_array($files)){
                foreach ($files as $key=>$file){
                    if(substr($file,0,1) == '/'){
                        $files[$key] = substr($file,1);
                    }
                }
                $result = $oss->deleteObjects($config['bucket'],$files);

            }else{
                if(substr($files,0,1) == '/'){
                    $files = substr($files,1);
                }
                $result = $oss->deleteObject($config['bucket'],$files);
            }
            return $result;
        } catch (OssException $e) {
//            dump($e);
//            dump($e->getCode());
//            dump($e->getErrorMessage());
        }
    }
}