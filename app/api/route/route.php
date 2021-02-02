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
Route::any(":controller/:function",'api/'.$version.'.:controller/:function');

Route::resource(':controller', 'api/' . $version . '.:controller');
//不带版本（普通动态路由）
//Route::get(":controller/:function","api/:controller/:function");

//带版本（资源动态路由）
//Route::rule(':controller/:function', 'api/' . $version . '.:controller/:function');

//不带版本（资源动态路由）
//Route::resource(':controller', 'admin/'.$version.'.:controller');
//dump($version);
// 只允许index read edit update 四个操作
//Route::rule(':controller/:function','api/' . $version . '.:controller/:function');

//Route::resource(':controller/', 'api /' . $version . '.:controller/');
//    ->only(['index', 'read', 'edit', 'update']);
//Route::rest([
//    'save'   => ['POST', '', 'store'],
//    'update' => ['PUT', '/:id', 'save'],
//    'delete' => ['DELETE', '/:id', 'destory'],
//]);