<?php
namespace Webman;
/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

class Middleware
{
    /**
     * @var array
     */
    protected static $_instances = [];

    /**
     * @param $all_middlewares
     */
    public static function load($all_middlewares)
    {
        foreach ($all_middlewares as $app_name => $middlewares) {
            foreach ($middlewares as $class_name) {
                if (\method_exists($class_name, 'process')) {
                    static::$_instances[$app_name][] = [\singleton($class_name), 'process'];
                } else {
                    // @todo Log
                    echo "middleware $class_name::process not exsits\n";
                }
            }
        }
    }


    public static function getMiddleware($app_name)
    {
        $global_middleware = isset(static::$_instances['']) ? static::$_instances[''] : [];
        if ($app_name === '') {
            return \array_reverse($global_middleware);
        }
        $app_middleware = isset(static::$_instances[$app_name]) ? static::$_instances[$app_name] : [];
        return \array_reverse($global_middleware + $app_middleware);
    }

    /**
     * @param $app_name
     * @return bool
     */
    public static function hasMiddleware($app_name)
    {
        return isset(static::$_instances[$app_name]);
    }
}