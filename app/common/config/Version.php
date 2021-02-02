<?php
/**
 * Created by PhpStorm.
 * User: Bear
 * Date: 2020/9/22 0022
 * Time: 16:46
 */

namespace app\common\config;


class Version
{
    /**
     * 返回参与加密的签名key
     * @param string $version   API版本号
     * @return mixed
     */
    public static function versionKey($version = "v1")
    {

        $key = [
            "v1" => "Tgmyg6p4ZtXqe9KeoMCPFqDO1I8sJw8k",
            "v2" => "9784F60A96E7B19BAFE410BD3AF129FD",
            "v3" => "5RmPkmRl5hVBdbfDiJQQFIPeD81lHgju",
            "v4" => "B0A004C4E166F1BDF37DFAA61A604709",
        ];

        return $key[$version];
    }

    /**
     * 返回参与加密的签名key
     * @param string $version   API版本号
     * @return mixed
     */
    public static function versionOpenApiKey($version = "v1")
    {

        $key = [
            "v1" => "Abm7jop9ZtXqjkleoMCPFqDO1I8sJ13B",
        ];

        return $key[$version];
    }
}