<?php
declare (strict_types=1);

namespace app\http\middleware;

use think\Response;

/**
 * 全局跨域请求处理
 * Class CrossDomain
 * @package app\middleware
 */
class CrossDomain
{
    public function handle($request, \Closure $next)
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Max-Age: 1800');
        header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE');
        header('Access-Control-Allow-Headers: Authorization, Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-CSRF-TOKEN, X-Requested-With, Token ,apiAuth,ts,sign,Cache-Control,Refere,User-Agent');
        if (strtoupper($request->method()) == "OPTIONS") {
            return Response::create()->send();
        }

        return $next($request);
    }
}