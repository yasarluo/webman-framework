<?php
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
namespace Webman\Http;

use Webman\Http\UploadFile;
use Webman\App;

/**
 * Class Request
 * @package Webman\Http
 */
class Request extends \Workerman\Protocols\Http\Request
{
    /**
     * @var string
     */
    public $app = null;

    /**
     * @var string
     */
    public $controller = null;

    /**
     * @var string
     */
    public $action = null;

    /**
     * @return mixed|null
     */
    public function all()
    {
        return $this->get() + $this->post();
    }

    /**
     * @param $name
     * @param null $default
     * @return null
     */
    public function input($name, $default = null)
    {
        $post = $this->post();
        if (isset($post[$name])) {
            return $post[$name];
        }
        $get = $this->get();
        return isset($get[$name]) ? $get[$name] : $default;
    }

    /**
     * @param array $keys
     */
    public function only(array $keys)
    {
        $post = $this->post();
        $get = $this->get();
        $result = [];
        foreach ($keys as $key) {
            if (isset($post[$key])) {
                $result[$key] = $post[$key];
                continue;
            }
            $result[$key] = isset($get[$key]) ? $get[$key] : null;
        }
    }

    /**
     * @param array $keys
     * @return mixed|null
     */
    public function except(array $keys)
    {
        $all = $this->all();
        foreach ($keys as $key) {
            unset($all[$key]);
        }
        return $all;
    }

    /**
     * @param null $name
     * @return null| UploadFile
     */
    public function file($name = null)
    {
        $file = $this->file($name);
        if (null === $file) {
            return null;
        }
        return new UploadFile($file['tmp_name'], $file['name'], $file['type'], $file['error']);
    }

    /**
     * @return string
     */
    public function getRemoteIp()
    {
        return App::connection()->getRemoteIp();
    }

    /**
     * @return int
     */
    public function getRemotePort()
    {
        return App::connection()->getRemotePort();
    }

    /**
     * @return string
     */
    public function getLocalIp()
    {
        return App::connection()->getLocalIp();
    }

    /**
     * @return int
     */
    public function getLocalPort()
    {
        return App::connection()->getLocalPort();
    }

    /**
     * @return string
     */
    public function url()
    {
        return '//' . $this->host() . '/' . $this->path();
    }

    /**
     * @return string
     */
    public function fullUrl()
    {
        return '//' . $this->host() . '/' . $this->uri();
    }

    /**
     * @return bool
     */
    public function isAjax()
    {
        return $this->header('X-Requested-With') === 'XMLHttpRequest';
    }

    /**
     * @return bool
     */
    public function isPjax()
    {
        return (bool)$this->header('X-PJAX');
    }

    /**
     * @return bool
     */
    public function expectsJson()
    {
        return ($this->isAjax() && !$this->isPjax()) || $this->acceptJson();
    }

    /**
     * @return bool
     */
    public function acceptJson()
    {
        return false !== strpos($this->header('accept'), 'json');
    }

}