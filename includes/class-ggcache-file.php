<?php

/**
 * 文件存储api
 */
class Ggcache_File
{
    public $cache_dir;
    private static $obj;

    private function __construct()
    {
        $this->cache_dir = GGCACHE_PLUGIN_DIR . 'cache';
    }

    /**
     * 获取静态实例
     * @return Ggcache_File
     */
    public static function get_instance()
    {
        if (is_null(self::$obj)) {
            self::$obj = new self();
        }
        return self::$obj;
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
        $file_name = md5($key);
        $cache_dir = $this->cache_dir . '/' . substr($file_name, 0, 2) . '/' . substr($file_name, 2, 2);
        if (!is_dir($cache_dir)) {
            mkdir($cache_dir, 0777, true);
        }
        if (file_put_contents($cache_dir . '/.' . $file_name, serialize(array(
            'value' => $value,
            'expire_time' => time() + $expire
        )))) {
            return true;
        }
        return false;
    }

    /**
     * 获取文件缓存
     * @param string $key 键
     * @param mixed $def_value 默认值
     * @return mixed
     */
    public function get($key, $def_value = '')
    {
        $file_name = md5($key);
        $cache_file = $this->cache_dir . '/' . substr($file_name, 0, 2) . '/' . substr($file_name, 2, 2) . '/.' . $file_name;
        if (!file_exists($cache_file)) {
            return $def_value;
        }
        $data = file_get_contents($cache_file);
        if (empty($data)) {
            return $def_value;
        }
        $data = unserialize($data);
        if (empty($data) || !isset($data['value']) || !isset($data['expire_time'])) {
            return $def_value;
        }
        if ($data['expire_time'] < time()) {
            return $def_value;
        }
        return $data['value'];
    }

    /**
     * 清空缓存
     * @param string $dir 缓存文件夹
     * @return bool
     */
    public function flush($dir = '')
    {
        if (empty($dir)) {
            $dir = $this->cache_dir;
        }
        if (!is_dir($dir)) {
            return false;
        }
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            $f = $dir . '/' . $file;
            if (is_dir($f)) {
                $this->flush($f);
            } else {
                @unlink($f);
            }
        }
        return true;
    }
}