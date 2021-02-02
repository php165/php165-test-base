<?php
/**
 * Created by PhpStorm.
 * User: Bear
 * Date: 2020/9/29 0029
 * Time: 16:54
 */

namespace app\common\base;

use app\common\config\Version;
use think\App;

class OpenApiController extends Controller
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

        $version = 'v1';
        if(isset($this->header['version']) && !empty($this->header['version']))
        {
            $version = $this->header['version'];
        }

        $key = Version::versionOpenApiKey($version);
        if (empty($key))
        {
            echo json_encode(['code' => 400, 'msg' => "参数错误"]);
            exit();
        }

        $result = checkSign($this->param, $key);
        if ($result['code'] != 200)
        {
            echo json_encode(['code' => $result['code'], 'msg' => $result['msg']]);
            exit();
        }
    }
}