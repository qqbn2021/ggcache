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
        // 开启了缓存
        Ggcache_Advanced::copy_advanced_cache_file();
    }

    // 删除插件执行的代码
    public static function plugin_uninstall()
    {
        // 删除advanced-cache.php 文件
        Ggcache_Advanced::remove_advanced_cache_file();
        // 删除配置
        delete_option('ggcache_options');
    }

    // 禁用插件执行的代码
    public static function plugin_deactivation()
    {
        // 插件已禁用，删除advanced-cache.php 文件
        Ggcache_Advanced::remove_advanced_cache_file();
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
     * 引入sweetalert2弹窗组件
     * @return void
     */
    public static function wp_enqueue_scripts()
    {
        if (!empty($_GET['page']) && 'ggcache-setting' === $_GET['page']) {
            // 添加静态文件
            // 添加同步文章js文件
            wp_enqueue_script(
                'ggcache',
                plugins_url('/js/sweetalert2.min.js', GGCACHE_PLUGIN_FILE),
                array(),
                '0.0.2',
                true
            );
            wp_enqueue_script(
                'ggcache-clear-cache',
                plugins_url('/js/clear-cache.min.js', GGCACHE_PLUGIN_FILE),
                array('jquery'),
                '0.0.2',
                true
            );
            wp_localize_script(
                'ggcache-clear-cache',
                'ggcache_obj',
                array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'nonce' => wp_create_nonce('ggcache'),
                )
            );
        }
    }

    /**
     * 清除缓存
     * @return void
     */
    public static function wp_ajax_clear_cache()
    {
        check_ajax_referer('ggcache');
        $result = Ggcache_Advanced::clear_cache();
        if ($result) {
            $status = 1;
        } else {
            $status = 0;
        }
        wp_send_json(array('status' => $status));
    }
}