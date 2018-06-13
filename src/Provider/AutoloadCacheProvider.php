<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/6/13
 * Time: 14:44
 */
namespace Suyain\Provider;

use Illuminate\Support\ServiceProvider;

class AutoloadCacheProvider extends ServiceProvider
{
    public function register()
    {
        // 加载配置文件
//        $this->mergeConfigFrom(dirname(__DIR__ . '/Config/cache.php'), 'cache');

        $this->mergeConfigFrom(dirname(__DIR__) . '/Config/common.php', 'alc.common');

        $this->mergeConfigFrom(dirname(__DIR__) . '/Config/swoole.php', 'alc.swoole');
    }
}