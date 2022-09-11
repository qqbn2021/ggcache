<?php

/**
 * Redis存储api
 */
class Ggcache_Redis
{
    private static $obj;
    public $redis_host;
    public $redis_port;
    public $redis_timeout;
    public $redis_password;
    public $redis_db;
    /**
     * @var Redis
     */
    public $redis_obj;

    private function __construct($redis_host = '127.0.0.1', $redis_port = 6379, $redis_password = '', $redis_db = 0, $redis_timeout = 1)
    {
        $this->redis_host = $redis_host;
        $this->redis_port = $redis_port;
        $this->redis_timeout = $redis_timeout;
        $this->redis_password = $redis_password;
        $this->redis_db = $redis_db;
    }

    /**
     * 获取静态实例
     * @return Ggcache_Redis
     */
    public static function get_instance($redis_host = '127.0.0.1', $redis_port = 6379, $redis_password = '', $redis_db = 0, $redis_timeout = 1)
    {
        if (is_null(self::$obj)) {
            self::$obj = new self($redis_host, $redis_port, $redis_password, $redis_db, $redis_timeout);
        }
        return self::$obj;
    }

    /**
     * 连接Redis
     * @return false|Redis
     */
    public function connect()
    {
        if (!class_exists('Redis') || empty($this->redis_host)) {
            return false;
        }
        if (is_null($this->redis_obj)) {
            try {
                $redis = new Redis();
                if (!empty($this->redis_port)) {
                    $result = $redis->connect($this->redis_host, $this->redis_port, $this->redis_timeout);
                } else {
                    $result = $redis->connect($this->redis_host);
                }
                if (!$result) {
                    return false;
                }
                // 设置密码
                if (!empty($this->redis_password)) {
                    $result = $redis->auth($this->redis_password);
                    if (!$result) {
                        return false;
                    }
                }
                // 选择数据库
                if (is_numeric($this->redis_db)) {
                    $result = $redis->select($this->redis_db);
                    if (!$result) {
                        return false;
                    }
                }
                $this->redis_obj = $redis;
                return $redis;
            } catch (Exception $e) {
                return false;
            }
        }
        return $this->redis_obj;
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
        return $this->redis_obj->setEx(md5($key), $expire, serialize($value));
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
        $value = $this->redis_obj->get(md5($key));
        if (empty($value)) {
            return $def_value;
        }
        $unserialize_data = unserialize($value);
        if (empty($unserialize_data)) {
            return $def_value;
        }
        return $unserialize_data;
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
        return $this->redis_obj->flushDB();
    }
}