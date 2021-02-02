<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/9/2 0002
 * Time: 上午 9:51
 */
namespace app\open\controller\v1;

use app\common\base\ErrorCode;
use think\App;
use think\response\Json;
use think\facade\Validate;
use app\common\base\OpenApiController;

class Version extends OpenApiController
{
    /**
     * [查询最新版本信息]
     *
     * @return Json
     */
    public function checkNewestVersion()
    {
        //查询当前最新版本
        $field = 'version_id,type,version_name,version_num,title,desc,status,least_num,update_url';
        $versionData = app('version')->field($field)->where(['is_use'=>1])->order('create_time desc')->find();
        if (empty($versionData))
        {
            return $this->returnJson(ErrorCode::NO_DATA_IS_NEW_VERSION);
        }

        return $this->returnJson(ErrorCode::SUCCESS_CODE,'SUCCESS',$versionData);
    }
}