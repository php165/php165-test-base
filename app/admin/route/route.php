<?php
/**
 * Created by PhpStorm.
 * User: Bear
 * Date: 2020/8/31 0031
 * Time: 16:25
 */
use think\facade\Route;
//自定义GET中的lists方法，获取列表
Route::get(':controller/lists','admin/:controller/lists');
Route::resource(':controller', 'admin/:controller');

