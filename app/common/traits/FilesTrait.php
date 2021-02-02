<?php
// +----------------------------------------------------------------------
// | CoolCms [ DEVELOPMENT IS SO SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2019 http://www.coolcms.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +---------------------------------------------------------------------
// | Author: Alan <alanstars@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2020/9/4 14:46
// +----------------------------------------------------------------------
// | Desc: 文件上传公共接口
// +----------------------------------------------------------------------

namespace app\common\traits;


use app\common\base\ErrorCode;
use think\exception\ValidateException;

trait FilesTrait
{
    private static $config;

    /**
     * 文件上传公共方法
     * @dec:
     * @author: Alan <alanstars@qq.com>
     * @param $files        上传的文件，如需多张，请使用数组
     * @param $fileType     上传文件的类型，固定值：image为图片；video为视频；file为文件
     * @param string $dirName      要保存文件的路径或者文件夹名
     * @return array
     */
    public static function uploadFilesManager($files,$fileType,$dirName=''){
        if(empty($files) || empty($fileType) || empty($dirName)){
            return ['code' => 707];
        }
        $fileConfig = self::fileConfig();
        try{
            $valide = validate($fileConfig['rule'],$fileConfig['message'])->batch(false)->check([$fileType=>$files]);
//            if(true !== $valide){
//                dump($valide);
//            }
            if(is_array($files)){
                foreach ($files as $file){
                    $uploadFiles = \think\facade\Filesystem::disk('public')->putFile($dirName,$file);
                    //是否开启OSS上传
                    if(self::aliyunOssIsUse()){
                        $result = AliCloudOssTrait::uploadAliyunOss($uploadFiles);
                        if($result['code'] == 200) {
                            $saveName[] = $result['data'];
                        }
                        else{
                            return $result;
                        }
                    }else{
                        $saveName[] = transformSystemSlash('/uploads'.DIRECTORY_SEPARATOR.$files);
                    }
                }
            }else{
                $uploadFiles = \think\facade\Filesystem::disk('public')->putFile($dirName,$files);
                //是否开启OSS上传
                if(self::aliyunOssIsUse()){
                    $result = AliCloudOssTrait::uploadAliyunOss($uploadFiles);
                    if($result['code'] == 200) {
                        $saveName[] = $result['data'];
                    }
                    else{

                        return $result;
                    }
                }else{
                    $saveName[] = transformSystemSlash('/uploads'.DIRECTORY_SEPARATOR.$files);
                }
            }
            return ['code'=>200,'data' => $saveName];

        }catch (ValidateException $exceValide){
            return ['code' => (int)$exceValide->getError()];
        }
    }

    /**
     * 删除本地或阿里云OSS文件
     * @dec:
     * @author: Alan <alanstars@qq.com>
     * @param $files        要删除文件绝对路径，如：/uploads/alan/20200908/22b845172ad241e73495db696aeb2cf3.png
     *                      批量删除，请以数组的形式入参['/uploads/e73495db696aeb2cf3.png','/uploads/e73495db696aeb2cf3.png']
     * @return array
     */
    public static function delFileManager($files){
        if(self::aliyunOssIsUse()){
            AliCloudOssTrait::delAliyunOss($files);
        }
//        die;
        if(is_array($files)){
            foreach ($files as $file){
                $filePath = PUBLIC_PATH.$file;
                //1.判断文件是否存在
                if(file_exists($filePath)){
                    if(!@unlink($filePath)){
                       $error[] =  $file;
                    }
                }
            }
            if(!empty($error)){
                return ['code' => 710,'data'=>$error];
            }else{
                return ['code' => 200,'data'=>'ok'];
            }
        }else{
            $filePath = PUBLIC_PATH.$files;
            //1.判断文件是否存在
            if(file_exists($filePath)){
                if(!@unlink($filePath)){
                    return ['code' => 710,'data'=>$files];
                }else{
                    return ['code' => 200,'data'=>'ok'];
                }
            }else
            {
                return ['code'=>ErrorCode::PARAM_ERROR,'文件不存在'];
            }
        }

    }

    private static function fileConfig(){
        $configInfo = cache('configInfo');
        if(isset($configInfo[1]) && !empty($configInfo[1])){
            return [
                'rule'  =>  [
                    'image' => [
                        'fileSize'  =>  $configInfo[1]['imageSize'] * 1024 * 1024,
                        'fileExt'   =>  $configInfo[1]['imageExt']
                    ],
                    'video' =>  [
                        'fileSize'  =>  $configInfo[1]['videoSize'] * 1024 * 1024,
                        'fileExt'   =>  $configInfo[1]['videoExt']
                    ],
                    'file'  =>  [
                        'fileSize'  =>  $configInfo[1]['fileSize'] * 1024 * 1024,
                        'fileExt'   =>  $configInfo[1]['fileExt']
                    ]
                ],
                'message'   =>  [
                    'image.fileSize'    =>  (string)ErrorCode::FILE_IMAGE_SIZE_ERROR,
                    'image.fileExt'    =>  (string)ErrorCode::FILE_IMAGE_EXT_ERROR,
                    'video.fileSize'    =>  (string)ErrorCode::FILE_VIDEO_SIZE_ERROR,
                    'video.fileExt'    =>  (string)ErrorCode::FILE_VIDEO_EXT_ERROR,
                    'file.fileSize'    =>  (string)ErrorCode::FILE_FILE_SIZE_ERROR,
                    'file.fileExt'    =>  (string)ErrorCode::FILE_FILE_EXT_ERROR,
                ]
            ];
        }else{
            return ['code'=>ErrorCode::NO_DATA_CONFIG_SITE_CODE];
        }
    }

    /**
     * 判断是否开启阿里云OSS
     * @dec:
     * @author: Alan <alanstars@qq.com>
     * @return bool
     */
    private static function aliyunOssIsUse():bool {
        if(isset(cache('configInfo')[5]) && !empty(cache('configInfo')[5]) && cache('configInfo')[5]['is_oss'] == 1) {
            return true;
        }else{
            return false;
        }
    }
}