<?php
/**
 * Created by PhpStorm.
 * User: Bear
 * Date: 2020/8/31 0031
 * Time: 16:25
 */

use think\facade\Route;
use think\facade\Request;
$version = Request::header('version', 'v1');
//带版本（普通动态路由）
Route::any(":controller/:function",'open/'.$version.'.:controller/:function');

Route::resource(':controller', 'open/' . $version . '.:controller');