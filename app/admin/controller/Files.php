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
// | DateTime: 2020/9/4 14:40
// +----------------------------------------------------------------------
// | Desc: 
// +----------------------------------------------------------------------

namespace app\admin\controller;


use app\common\base\AdminController;
use app\common\base\ErrorCode;
use app\common\traits\FilesTrait;
use think\App;

class Files extends AdminController
{
    protected $middleware = ['auth','log'];
    
    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    /**
     * 公共上传图片接口
     * @dec:
     * @param $type  1系统文件目录，2用户文件目录，3后台文件目录，4广告文件目录
     * @param $files        上传的文件，如需多张，请使用数组
     * @author: Alan <alanstars@qq.com>
     * @return \think\response\Json
     */
    public function uploadImages(){
        $type = $this->request->param('type');
        $dirName = $this->getFileDirectoryName($type);
        $files = $this->request->file('files');
//        $file = transformSystemSlash(PUBLIC_PATH.'');
//        dump($file);
//        dump(FilesTrait::delFileManager(['/uploads/alan/20200908/5be20a19670caba0f79ab7f175eebb6d.png','/uploads/alan/20200908/0ee80ad86218b5e1d47de81937f301d4.png']));

        $result = FilesTrait::uploadFilesManager($files,'image',$dirName);
        if($result['code'] == 200){
            return $this->returnJson($result['code'],'上传成功',$result['data']);
        }else{
            return $this->returnJson($result['code']);
        }    }

    /**
     * 公共上传图片接口
     * @dec:
     * @param $type  1系统文件目录，2用户文件目录，3后台文件目录，4广告文件目录
     * @param $files        上传的文件，如需多张，请使用数组
     * @author: Alan <alanstars@qq.com>
     * @return \think\response\Json
     */
    public function uploadVideo(){
        $type = $this->request->param('type');
        $dirName = $this->getFileDirectoryName($type);
        $files = $this->request->file('files');
        $result = FilesTrait::uploadFilesManager($files,'video',$dirName);
        if($result['code'] == 200){
            return $this->returnJson($result['code'],'上传成功',$result['data']);
        }else{
            return $this->returnJson($result['code']);
        }
    }

    /**
     * 公共上传图片接口
     * @dec:
     * @param $type  1系统文件目录，2用户文件目录，3后台文件目录，4广告文件目录
     * @param $files        上传的文件，如需多张，请使用数组
     * @author: Alan <alanstars@qq.com>
     * @return \think\response\Json
     */
    public function uploadFiles(){
        $type = $this->request->param('type');
        $dirName = $this->getFileDirectoryName($type);
        $files = $this->request->file('files');
        $result = FilesTrait::uploadFilesManager($files,'file',$dirName);
        if($result['code'] == 200){
            return $this->returnJson($result['code'],'上传成功',$result['data']);
        }else{
            return $this->returnJson($result['code']);
        }
    }

    /**
     * 删除文件
     * @dec:
     * @author: Alan <alanstars@qq.com>
     */
    public function delFiles(){
        $files = $this->request->param('files');
        $filesName = json_decode($files,true);
        $result = FilesTrait::delFileManager($filesName);

        return $this->returnJson($result['code']);
    }

    /**
     * 公共上传方法
     * 接口地址 admin/files/uploadFilesAllType
     * 请求方式 POST
     * @param $type  1系统文件目录，2用户文件目录，3后台文件目录，4广告文件目录
     * @param $files        上传的文件，如需多张，请使用数组
     * @param $filesType   上传的文件类型 1图片，2视频，3文件
     */
    public function uploadFilesAllType()
    {
        $param = $this->param;
        if(!isset($param['type']))
        {
            return $this->returnJson(ErrorCode::PARAM_ERROR,'文件夹必传');
        }
        if(!isset($param['files_type']))
        {
            return $this->returnJson(ErrorCode::PARAM_ERROR,'文件类型必传');
        }
//        $type = $this->request->param('type');
        $dirName = $this->getFileDirectoryName($param['type']);
        $files = $this->request->file('files');
        $filesType = $this->getFilesType($param['files_type']);
        $result = FilesTrait::uploadFilesManager($files,$filesType,$dirName);
        if($result['code'] == 200){
            return $this->returnJson($result['code'],'上传成功',$result['data']);
        }else{
            return $this->returnJson($result['code']);
        }
    }
}