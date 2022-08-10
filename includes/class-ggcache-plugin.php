<?php

/**
 * 基础类
 */
class Ggcache_Plugin
{
    // 启用插件
    public static function plugin_activation()
    {
        // 创建默认配置
        add_option('ggcache_options', array(
            'salt' => mt_rand(100000, 999999),
            'timeout' => 3600
        ));
    }

    // 删除插件执行的代码
    public static function plugin_uninstall()
    {
        // 删除配置
        delete_option('ggcache_options');
    }

    /**
     * 表单输入框回调
     *
     * @param array $args 这数据就是add_settings_field方法中第6个参数（$args）的数据
     */
    public static function field_callback($args)
    {
        // 表单的id或name字段
        $id = $args['label_for'];
        // 表单的名称
        $input_name = 'ggcache_options[' . $id . ']';
        // 获取表单选项中的值
        global $ggcache_options;
        // 表单的值
        $input_value = isset($ggcache_options[$id]) ? $ggcache_options[$id] : '';
        // 表单的类型
        $form_type = isset($args['form_type']) ? $args['form_type'] : 'input';
        // 输入表单说明
        $form_desc = isset($args['form_desc']) ? $args['form_desc'] : '';
        // 输入表单type
        $type = isset($args['type']) ? $args['type'] : 'text';
        // 输入表单placeholder
        $form_placeholder = isset($args['form_placeholder']) ? $args['form_placeholder'] : '';
        // 下拉框等选项值
        $form_data = isset($args['form_data']) ? $args['form_data'] : array();
        // 扩展form表单属性
        $form_extend = isset($args['form_extend']) ? $args['form_extend'] : array();
        switch ($form_type) {
            case 'input':
                self::generate_input(
                    array_merge(
                        array(
                            'id' => $id,
                            'type' => $type,
                            'placeholder' => $form_placeholder,
                            'name' => $input_name,
                            'value' => $input_value,
                            'class' => 'regular-text',
                        ),
                        $form_extend
                    ));
                break;
            case 'select':
                self::generate_select(
                    array_merge(
                        array(
                            'id' => $id,
                            'placeholder' => $form_placeholder,
                            'name' => $input_name
                        ),
                        $form_extend
                    ),
                    $form_data,
                    $input_value
                );
                break;
            case 'checkbox':
                self::generate_checkbox(
                    array_merge(
                        array(
                            'name' => $input_name . '[]'
                        ),
                        $form_extend
                    ),
                    $form_data,
                    $input_value
                );
                break;
            case 'textarea':
                self::generate_textarea(
                    array_merge(
                        array(
                            'id' => $id,
                            'placeholder' => $form_placeholder,
                            'name' => $input_name,
                            'class' => 'large-text code',
                            'rows' => 5,
                        ),
                        $form_extend
                    ),
                    $input_value
                );
                break;
        }
        if (!empty($form_desc)) {
            ?>
            <p class="description"><?php echo esc_html($form_desc); ?></p>
            <?php
        }
    }

    /**
     * 生成textarea表单
     * @param array $form_data 标签上的属性数组
     * @param string $value 默认值
     * @return void
     */
    public static function generate_textarea($form_data, $value = '')
    {
        ?><textarea <?php
        foreach ($form_data as $k => $v) {
            echo esc_attr($k); ?>="<?php echo esc_attr($v); ?>" <?php
        } ?>><?php echo esc_textarea($value); ?></textarea>
        <?php
    }

    /**
     * 生成checkbox表单
     * @param array $form_data 标签上的属性数组
     * @param array $checkboxs 下拉列表数据
     * @param string|array $value 选中值，单个选中字符串，多个选中数组
     * @return void
     */
    public static function generate_checkbox($form_data, $checkboxs, $value = '')
    {
        ?>
        <fieldset><p>
                <?php
                $len = count($checkboxs);
                foreach ($checkboxs as $k => $checkbox) {
                    $checked = '';
                    if (!empty($value)) {
                        if (is_array($value)) {
                            if (in_array($checkbox['value'], $value)) {
                                $checked = 'checked';
                            }
                        } else {
                            if ($checkbox['value'] == $value) {
                                $checked = 'checked';
                            }
                        }
                    }
                    ?>
                    <label>
                        <input type="checkbox" <?php checked($checked, 'checked'); ?><?php
                        foreach ($form_data as $k2 => $v2) {
                            echo esc_attr($k2); ?>="<?php echo esc_attr($v2); ?>" <?php
                        } ?> value="<?php echo esc_attr($checkbox['value']); ?>"
                        ><?php echo esc_html($checkbox['title']); ?>
                    </label>
                    <?php
                    if ($k < ($len - 1)) {
                        ?>
                        <br>
                        <?php
                    }
                }
                ?>
            </p></fieldset>
        <?php
    }

    /**
     * 生成input表单
     * @param array $form_data 标签上的属性数组
     * @return void
     */
    public static function generate_input($form_data)
    {
        ?><input <?php
        foreach ($form_data as $k => $v) {
            echo esc_attr($k); ?>="<?php echo esc_attr($v); ?>" <?php
        } ?>><?php
    }

    /**
     * 生成select表单
     * @param array $form_data 标签上的属性数组
     * @param array $selects 下拉列表数据
     * @param string|array $value 选中值，单个选中字符串，多个选中数组
     * @return void
     */
    public static function generate_select($form_data, $selects, $value = '')
    {
        ?><select <?php
        foreach ($form_data as $k => $v) {
            echo esc_attr($k); ?>="<?php echo esc_attr($v); ?>" <?php
        } ?>><?php
        foreach ($selects as $select) {
            $selected = '';
            if (!empty($value)) {
                if (is_array($value)) {
                    if (in_array($select['value'], $value)) {
                        $selected = 'selected';
                    }
                } else {
                    if ($select['value'] == $value) {
                        $selected = 'selected';
                    }
                }
            }
            ?>
            <option <?php selected($selected, 'selected'); ?>
                    value="<?php echo esc_attr($select['value']); ?>"><?php echo esc_html($select['title']); ?></option>
            <?php
        }
        ?>
        </select>
        <?php
    }

    // 初始化
    public static function admin_init()
    {
        // 注册设置页面
        Ggcache_Page::init_page();
    }

    // 添加菜单
    public static function admin_menu()
    {
        // 设置页面
        add_options_page(
            '果果加速',
            '果果加速',
            'manage_options',
            'ggcache-setting',
            array('Ggcache_Plugin', 'show_page')
        );
    }

    // 显示设置页面
    public static function show_page()
    {
        // 检查用户权限
        if (!current_user_can('manage_options')) {
            return;
        }
        if (!empty($_GET['clear_cache'])) {
            self::clear_cache();
            add_settings_error('ggcache', 'ggcache_message', '清除缓存成功。', 'updated');
            settings_errors('ggcache');
            echo '<script>location.href="' . esc_url($_SERVER['HTTP_REFERER']) . '";</script>';
            exit();
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post" enctype="multipart/form-data">
                <?php
                // 输出表单
                settings_fields('ggcache_page');
                do_settings_sections('ggcache_page');
                // 输出保存设置按钮
                submit_button('保存更改');
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * 添加设置链接
     * @param array $links
     * @return array
     */
    public static function link_setting($links)
    {
        $business_link = '<a href="https://www.ggdoc.cn/plugin/5.html" target="_blank">商业版</a>';
        array_unshift($links, $business_link);

        $settings_link = '<a href="options-general.php?page=ggcache-setting">设置</a>';
        array_unshift($links, $settings_link);

        return $links;
    }

    /**
     * 获取redis连接对象
     * @return false|Redis
     */
    public static function get_redis()
    {
        global $ggcache_options;
        if (!class_exists('Redis') || empty($ggcache_options['redis_host'])) {
            return false;
        }
        static $redis;
        if (empty($redis)) {
            try {
                $redis = new Redis();
                $redis_timeout = 1;
                if (!empty($ggcache_options['redis_timeout'])) {
                    $redis_timeout = intval($ggcache_options['redis_timeout']);
                }
                if (!empty($ggcache_options['redis_port'])) {
                    $result = $redis->connect($ggcache_options['redis_host'], $ggcache_options['redis_port'], $redis_timeout);
                } else {
                    $result = $redis->connect($ggcache_options['redis_host']);
                }
                if (!$result) {
                    return false;
                }
                // 设置密码
                if (!empty($ggcache_options['redis_password'])) {
                    $result = $redis->auth($ggcache_options['redis_password']);
                    if (!$result) {
                        return false;
                    }
                }
                // 选择数据库
                if (isset($ggcache_options['redis_db']) && is_numeric($ggcache_options['redis_db'])) {
                    $result = $redis->select($ggcache_options['redis_db']);
                    if (!$result) {
                        return false;
                    }
                }
                return $redis;
            } catch (Exception $e) {
                return false;
            }
        }
        return $redis;
    }

    /**
     * memcache连接
     * @return bool|Memcache
     */
    public static function get_memcache()
    {
        global $ggcache_options;
        if (!function_exists('memcache_connect') || empty($ggcache_options['memcache_host'])) {
            return false;
        }
        static $memcache;
        if (empty($memcache)) {
            $memcache_port = 0;
            if (!empty($ggcache_options['memcache_port'])) {
                $memcache_port = intval($ggcache_options['memcache_port']);
            }
            $memcache_timeout = 1;
            if (!empty($ggcache_options['memcache_timeout'])) {
                $memcache_timeout = intval($ggcache_options['memcache_timeout']);
            }
            try {
                $memcache = @memcache_connect($ggcache_options['memcache_host'], $memcache_port, $memcache_timeout);
                if (empty($memcache)) {
                    return false;
                }
                return $memcache;
            } catch (Exception $e) {
                return false;
            }
        }
        return $memcache;
    }

    /**
     * 显示缓存
     * @return void
     */
    public static function show_cache()
    {
        global $ggcache_options;
        global $ggcache_cache_key;
        switch ($ggcache_options['type']) {
            case 1:
                // 从文件获取
                if (file_exists(GGCACHE_PLUGIN_DIR . 'cache/.' . $ggcache_cache_key)) {
                    $data = file_get_contents(GGCACHE_PLUGIN_DIR . 'cache/.' . $ggcache_cache_key);
                    $unserialize_data = unserialize($data);
                    if (empty($unserialize_data)) {
                        return;
                    }
                    if (empty($unserialize_data['html']) || empty($unserialize_data['create_time'])) {
                        return;
                    }
                    $timeout = intval($ggcache_options['timeout']);
                    if (($unserialize_data['create_time'] + $timeout) > time()) {
                        ob_end_clean();
                        // 显示缓存
                        echo $unserialize_data['html'];
                        exit();
                    }
                }
                break;
            case 2:
                // 从Redis获取
                $redis = self::get_redis();
                if (!empty($redis)) {
                    $data = $redis->get($ggcache_cache_key);
                    if (empty($data)) {
                        return;
                    }
                    $unserialize_data = unserialize($data);
                    if (empty($unserialize_data)) {
                        return;
                    }
                    ob_end_clean();
                    // 显示缓存
                    echo $unserialize_data;
                    exit();
                }
                break;
            case 3:
                // 从memcache获取
                $memcache = self::get_memcache();
                if (!empty($memcache)) {
                    $data = $memcache->get($ggcache_cache_key);
                    if (empty($data)) {
                        return;
                    }
                    $unserialize_data = unserialize($data);
                    if (empty($unserialize_data)) {
                        return;
                    }
                    ob_end_clean();
                    // 显示缓存
                    echo $unserialize_data;
                    exit();
                }
                break;
        }
    }

    /**
     * 保存缓存
     * @return void
     */
    public static function save_cache()
    {
        // 用户登录了，不缓存
        if (is_user_logged_in()) {
            return;
        }
        global $ggcache_options;
        global $ggcache_cache_key;
        $html = ob_get_clean();
        if (!empty($html)) {
            // 兼容低版本php
            $can_save = true;
            if (function_exists('http_response_code') && 200 != http_response_code()) {
                $can_save = false;
            }
            if ($can_save) {
                switch ($ggcache_options['type']) {
                    case 1:
                        if (!is_dir(GGCACHE_PLUGIN_DIR . 'cache')) {
                            mkdir(GGCACHE_PLUGIN_DIR . 'cache', 0777, true);
                        }
                        $data = serialize(array(
                            'html' => $html,
                            'create_time' => time()
                        ));
                        // 保存到文件
                        file_put_contents(GGCACHE_PLUGIN_DIR . 'cache/.' . $ggcache_cache_key, $data);
                        break;
                    case 2:
                        // 缓存到redis
                        $redis = self::get_redis();
                        if (!empty($redis)) {
                            $timeout = intval($ggcache_options['timeout']);
                            $redis->setEx($ggcache_cache_key, $timeout, serialize($html));
                        }
                        break;
                    case 3:
                        // 缓存到memcache
                        $memcache = self::get_memcache();
                        if (!empty($memcache)) {
                            $timeout = intval($ggcache_options['timeout']);
                            $memcache->set($ggcache_cache_key, serialize($html), MEMCACHE_COMPRESSED, $timeout);
                        }
                        break;
                }
            }
            echo $html;
        }
    }

    /**
     * 清除缓存
     * @return void
     */
    public static function clear_cache()
    {
        global $ggcache_options;
        switch ($ggcache_options['type']) {
            case 1:
                $files = scandir(GGCACHE_PLUGIN_DIR . 'cache');
                foreach ($files as $file) {
                    if ($file == '.' || $file == '..') {
                        continue;
                    }
                    $f = GGCACHE_PLUGIN_DIR . 'cache/' . $file;
                    @unlink($f);
                }
                break;
            case 2:
                $redis = self::get_redis();
                if (!empty($redis)) {
                    $redis->flushDB();
                }
                break;
            case 3:
                $memcache = self::get_memcache();
                if (!empty($memcache)) {
                    $memcache->flush();
                }
                break;
        }
    }
}