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
// | DateTime: 2020/8/26 16:57
// +----------------------------------------------------------------------
// | Desc: 
// +----------------------------------------------------------------------

namespace app\common\base;

use think\App;
use app\common\config\Version;
use think\facade\Validate;

class ApiController extends Controller
{
    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
        //判断签名是否正确
        if (isset($this->param['controller'])) {
            unset($this->param['controller']);
        }
        if (isset($this->param['function'])) {
            unset($this->param['function']);
        }
        $rule = [
            'phone_type' => 'require',
            'phone_model' => 'require',
            'phone_version' => 'require',
            'app_version' => 'require',
            'fromSource' => 'require',
        ];
        $msg = [
            'phone_type.require' => '公共参数不能为空1',
            'phone_model.require' => '公共参数不能为空2',
            'phone_version.mobile' => '公共参数不能为空3',
            'app_version.require' => '公共参数不能为空4',
            'fromSource.require' => '公共参数不能为空5',
        ];

        $validate = Validate::rule($rule, $msg);
        if (!$validate->check($this->param)) {
            echo json_encode(['code' => 400, 'msg' => $validate->getError()]);
            exit();
        }

        if (!isset($this->param['fromSource'])) {
            echo json_encode(['code' => 400, 'msg' => "参数错误"]);
            exit();
        }
        $version = 'v1';
        if(isset($this->header['version']) && !empty($this->header['version']))
        {
            $version = $this->header['version'];
        }
        $key = Version::versionKey($version);
        if (empty($key)) {
            echo json_encode(['code' => 400, 'msg' => "参数错误"]);
            exit();
        }
        $result = checkSign($this->param, $key);
        if ($result['code'] != 200) {

            echo json_encode(['code' => $result['code'], 'msg' => $result['msg']]);
            exit();
        }
    }
}