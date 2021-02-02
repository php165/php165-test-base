<?php
/**
 * Created by PhpStorm.
 * User: Bear
 * Date: 2020/8/31 0031
 * Time: 16:03
 */
use think\facade\Request;

$version = Request::header('version', 'v1');
switch ($version)
{
    case 'v1':
        return [
            //版本表
            'version' => \app\open\model\v1\Version::class,
        ];

        break;
}



