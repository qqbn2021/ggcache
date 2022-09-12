<?php

/**
 * Memcached存储api
 */
class Ggcache_Memcache
{
    private static $obj;
    public $memcache_host;
    public $memcache_port;
    public $memcache_timeout;
    public $is_memcache = false;
    /**
     * @var Memcache|Memcached
     */
    public $memcache_obj;

    private function __construct($memcache_host = '127.0.0.1', $memcache_port = 11211, $memcache_timeout = 1)
    {
        $this->memcache_host = $memcache_host;
        $this->memcache_port = $memcache_port;
        $this->memcache_timeout = $memcache_timeout;
    }

    /**
     * 获取静态实例
     * @return Ggcache_Memcache
     */
    public static function get_instance($memcache_host = '127.0.0.1', $memcache_port = 11211, $memcache_timeout = 1)
    {
        if (is_null(self::$obj)) {
            self::$obj = new self($memcache_host, $memcache_port, $memcache_timeout);
        }
        return self::$obj;
    }

    /**
     * 连接Redis
     * @return false|Memcache|Memcached
     */
    public function connect()
    {
        if ((!class_exists('Memcache') && !class_exists('Memcached')) || empty($this->memcache_host) || empty($this->memcache_port)) {
            return false;
        }
        if (is_null($this->memcache_obj)) {
            try {
                if (class_exists('Memcache')) {
                    $this->is_memcache = true;
                    $memcache = new Memcache();
                    $result = @$memcache->connect($this->memcache_host, $this->memcache_port, $this->memcache_timeout);
                    if (!$result) {
                        return false;
                    }
                } else if (class_exists('Memcached')) {
                    $memcache = new Memcached();
                    $result = @$memcache->addServer($this->memcache_host, $this->memcache_port);
                    if (!$result) {
                        return false;
                    }
                } else {
                    return false;
                }
                $this->memcache_obj = $memcache;
                return $memcache;
            } catch (Exception $e) {
                return false;
            }
        }
        return $this->memcache_obj;
    }

    /**
     * 缓存一个值
     * @param string $key 键
     * @param mixed $value 值
     * @param int $expire 过期时间
     * @return bool
     */
    public function set($key, $value, $expire = 3600)
    {
        if (!$this->connect()) {
            return false;
        }
        if ($this->is_memcache) {
            return $this->memcache_obj->set(md5($key), $value, MEMCACHE_COMPRESSED, $expire);
        }
        return $this->memcache_obj->set(md5($key), $value, $expire);
    }

    /**
     * 获取文件缓存
     * @param string $key 键
     * @param mixed $def_value 默认值
     * @return mixed
     */
    public function get($key, $def_value = '')
    {
        if (!$this->connect()) {
            return $def_value;
        }
        $value = $this->memcache_obj->get(md5($key));
        if (empty($value)) {
            return $def_value;
        }
        return $value;
    }

    /**
     * 清空缓存
     * @return bool
     */
    public function flush()
    {
        if (!$this->connect()) {
            return false;
        }
        return $this->memcache_obj->flush();
    }
}