<?php
// 设置内容区域宽度，默认为1680px
if (!isset($content_width)) {
    $content_width = 1680;
}

/**
 * 添加主题支持的功能
 */
function theme_support() {
    // 添加页面标题标签
    add_action('wp_head', '_wp_render_title_tag', 1);

    // 启用文章缩略图、自动feed链接和页面标题标签支持
    add_theme_support('post-thumbnails');
    add_theme_support('automatic-feed-links');
    add_theme_support('title-tag');

    // 添加自定义头部支持
    add_theme_support('custom-header', apply_filters('my_custom_header_args', [
        'default-image' => '',
        'header-text'   => false,
        'width'         => 2000,
        'height'        => 1200,
        'flex-height'   => true,
        'video'         => false,
    ]));
}

// 在主题设置完成后添加theme_support函数
add_action('after_setup_theme', 'theme_support');

/**
 * 获取当前文章的缩略图URL
 * 
 * @return string 缩略图的URL
 */
function my_post_thumbnail() {
    return wp_get_attachment_url(get_post_thumbnail_id());
}

/**
 * 一个不做任何处理的过滤器函数，直接返回传入的字符串
 * 
 * @param string $str 输入的字符串
 * @return string 返回输入的字符串
 */
function do_nothing($str) {
    return $str;
}

function my_customize_register($wp_customize) {
    // 移除默认的设置部分
    $sections_to_remove = ['title_tagline', 'static_front_page', 'custom_css'];
    foreach ($sections_to_remove as $section) {
        $wp_customize->remove_section($section);
    }

    // 添加新的设置区域
    add_custom_sections($wp_customize);

    // 添加新的设置项
    add_custom_settings($wp_customize);

    // 添加新的控件
    add_custom_controls($wp_customize);
}

/**
 * 添加自定义的设置区域
 */
function add_custom_sections($wp_customize) {
    $wp_customize->add_section('header_setting', [
        'title'    => __('Header Setting', 'default'),
        'priority' => 10,
    ]);

    $wp_customize->add_section('more_js', [
        'title'       => __('Additional JS', 'default'),
        'description' => __('You can paste your additional js code here, such as Google Adsense or Google Analytics code.<br /><b>Theses code should follow the AMP Spec.</b>', 'default'),
    ]);

    $wp_customize->add_section('more_css', [
        'title'       => __('Additional CSS', 'default'),
        'description' => __('You can paste your additional css code here.', 'default'),
    ]);

    $wp_customize->add_section('auto_featured_image', [
        'title'       => __('Auto featured image ', 'default'),
        'description' => __('Auto-generation featured image for blog posts', 'default'),
    ]);
}

/**
 * 添加自定义的设置项
 */
function add_custom_settings($wp_customize) {
    $settings = [
        'blog_title'       => ['default' => get_bloginfo('name'), 'transport' => 'refresh', 'sanitize_callback' => 'do_nothing'],
        'main_tagline'     => ['default' => 'Free the Internet', 'transport' => 'refresh', 'sanitize_callback' => 'do_nothing'],
        'sub_tagline'      => ['default' => 'Across the Great Wall we can reach every corner in the world', 'transport' => 'refresh', 'sanitize_callback' => 'do_nothing'],
        'favicon'          => ['default' => '', 'transport' => 'refresh', 'sanitize_callback' => 'absint'],
        'header_js'        => ['default' => '', 'transport' => 'refresh', 'sanitize_callback' => 'do_nothing'],
        'body_js'          => ['default' => '', 'transport' => 'refresh', 'sanitize_callback' => 'do_nothing'],
        'header_css'       => ['default' => '', 'transport' => 'refresh', 'sanitize_callback' => 'do_nothing'],
        'rapidapi_translator' => ['default' => '', 'transport' => 'refresh', 'sanitize_callback' => 'do_nothing'],
        'pixabay_apikey'   => ['default' => '', 'transport' => 'refresh', 'sanitize_callback' => 'do_nothing']
    ];

    foreach ($settings as $key => $args) {
        $wp_customize->add_setting($key, $args);
    }
}

/**
 * 添加自定义的控件
 */
function add_custom_controls($wp_customize) {
    $wp_customize->add_control('input_blog_title', [
        'label'    => __('Blog Title', 'default'),
        'section'  => 'header_setting',
        'settings' => 'blog_title',
        'type'     => 'text',
    ]);

    $wp_customize->add_control('input_main_tagline', [
        'label'    => __('Main tagline', 'default'),
        'section'  => 'header_setting',
        'settings' => 'main_tagline',
        'type'     => 'text',
    ]);

    $wp_customize->add_control('input_sub_tagline', [
        'label'    => __('Sub tagline', 'default'),
        'section'  => 'header_setting',
        'settings' => 'sub_tagline',
        'type'     => 'textarea',
    ]);

    $wp_customize->add_control(new WP_Customize_Site_Icon_Control($wp_customize, 'set_favicon', [
        'label'       => __('Favicon', 'default'),
        'description' => __('Favicon is what you see in <strong>browser tabs</strong>, bookmark bars', 'default'),
        'section'     => 'header_setting',
        'settings'    => 'favicon',
        'width'       => 32,
        'height'      => 32,
    ]));

    $wp_customize->add_control('input_header_js', [
        'label'    => __('Insert to Header', 'default'),
        'section'  => 'more_js',
        'settings' => 'header_js',
        'type'     => 'textarea',
    ]);

    $wp_customize->add_control('input_body_js', [
        'label'    => __('Insert to Body', 'default'),
        'section'  => 'more_js',
        'settings' => 'body_js',
        'type'     => 'textarea',
    ]);

    $wp_customize->add_control('input_header_css', [
        'label'    => __('Insert to Header', 'default'),
        'section'  => 'more_css',
        'settings' => 'header_css',
        'type'     => 'textarea',
    ]);

    $wp_customize->add_control('input_rapidapi_translator', [
        'label'       => __('RapidAPI', 'default'),
        'description' => __('<a href="https://rapidapi.com/microsoft-azure-org-microsoft-cognitive-services/api/microsoft-translator-text/" target="_blank">Get Microsoft Translator API Key</a><br><a href="https://rapidapi.com/ai-box-ai-box-default/api/text-analysis10/" target="_blank">Get Text Analysis API Key</a>'),
        'section'     => 'auto_featured_image',
        'settings'    => 'rapidapi_translator',
        'type'        => 'text',
    ]);

    $wp_customize->add_control('input_pixabay_apikey', [
        'label'    => __('Pixabay apikey', 'default'),
        'section'  => 'auto_featured_image',
        'settings' => 'pixabay_apikey',
        'type'     => 'text',
    ]);
};

// 注册自定义自定义功能
add_action('customize_register', 'my_customize_register');

/**
 * 注册菜单位置
 */
function my_menus() {
    $locations = [
        'primary' => __('Top Header Menu', 'default'),
        'footer'  => __('Footer Menu', 'default'),
    ];
    register_nav_menus($locations);
}

// 初始化时注册菜单
add_action('init', 'my_menus');

/**
 * 自定义摘要末尾的内容
 */
add_filter('excerpt_more', function () {
    return '...';
});

/**
 * 检查是否是爬虫访问
 * 
 * @return bool 如果是爬虫则返回true，否则返回false
 */
function is_spider() {
    $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    if (!empty($agent)) {
        $spiders = [
            'Googlebot', 'Baiduspider', 'ia_archiver',
            'R6_FeedFetcher', 'NetcraftSurveyAgent',
            'Sogou web spider', 'bingbot', 'Yahoo! Slurp',
            'facebookexternalhit', 'PrintfulBot', 'msnbot',
            'Twitterbot', 'UnwindFetchor', 'urlresolver'
        ];
        foreach ($spiders as $spider) {
            if (strpos($agent, strtolower($spider)) !== false) {
                return true;
            }
        }
    }
    return false;
}

/**
 * 设置文章浏览量
 */
function set_post_views() {
    if (is_singular() && !is_spider()) {
        $post_id = get_the_ID();
        if ($post_id) {
            $post_views = (int) get_post_meta($post_id, 'views', true);
            update_post_meta($post_id, 'views', ($post_views + 1));
        }
    }
}

// 文章被加载时增加浏览量
add_action('the_post', 'set_post_views');

/**
 * 增加文章点赞数
 */
function set_post_likes() {
    if (isset($_POST['action']) && $_POST['action'] === 'likes' && isset($_POST['post_id'])) {
        $id = (int) $_POST['post_id'];
        $raters = (int) get_post_meta($id, 'likes', true);
        if (!isset($_COOKIE['likes_' . $id])) {
            $raters += 1;
            setcookie('likes_' . $id, $id, time() + 99999999, '/', false, false);
            update_post_meta($id, 'likes', $raters);
            wp_send_json(['likes' => $raters, 'class' => 'likes-button-active likes-button']);
        }
    }
    wp_die();
}

// 对未登录和登录用户的点赞操作都进行处理
add_action('wp_ajax_nopriv_likes', 'set_post_likes');
add_action('wp_ajax_likes', 'set_post_likes');

/**
 * 替换URL中的协议部分，用于保持与页面协议一致
 * 
 * @param string $url URL地址
 * @return string 替换协议后的URL
 */
function follow_scheme_replace($url) {
    return preg_replace('/^(http|https):\/\//', '//', $url, 1);
}

/**
 * 自定义搜索小工具
 */
class my_search extends WP_Widget {
    public function __construct() {
        $widget_ops = array(
            'classname'                   => 'widget_search',
            'description'                 => __('A search form for your site.', 'default'),
            'customize_selective_refresh' => true,
        );
        parent::__construct('search', _x('Search', 'Search widget', 'default'), $widget_ops);
    }

    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $title = apply_filters('widget_title', $title, $instance, $this->id_base);

        echo $args['before_widget'];
        if ($title) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        echo '<form target="_top" role="search" method="get" class="search-form" action="' . trailingslashit(follow_scheme_replace(get_site_url())) . '">
            <input required type="text" class="search-field" placeholder="' . esc_attr_x('Search &hellip;', 'placeholder', 'default') . '" value="' . get_search_query() . '" name="s" />
            <button type="submit" class="search-submit"></button>
        </form>';
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        echo '<p><label for="' . $this->get_field_id('title') . '">' . __('Title:', 'default') . ' <input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . esc_attr($title) . '" /></label></p>';
    }

    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = sanitize_text_field($new_instance['title']);
        return $instance;
    }
}

/**
 * 注册侧边栏并取消注册一些默认小工具
 */
function my_sidebar_registration() {
    // 取消注册默认的小工具
    $widgets_to_unregister = [
        'WP_Widget_Media_Audio', 'WP_Widget_Media_Video',
        'WP_Widget_Media_Image', 'WP_Widget_Media_Gallery',
        'WP_Widget_Calendar', 'WP_Widget_Nav_Menu', 
        'WP_Widget_Pages', 'WP_Widget_RSS', 
        'WP_Widget_Text', 'WP_Widget_Tag_Cloud', 'WP_Widget_Search'
    ];

    foreach ($widgets_to_unregister as $widget) {
        unregister_widget($widget);
    }

    register_widget('my_search');

    register_sidebar([
        'name'          => __('Sidebar', 'default'),
        'id'            => 'my-sidebar',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget'  => '</aside>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>'
    ]);

    global $wp_widget_factory;
    remove_action('wp_head', [$wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style']);
}

add_action('widgets_init', 'my_sidebar_registration');

/**
 * 注销评论表单中的HTML未过滤的HTML提交
 */
remove_action('comment_form', 'wp_comment_form_unfiltered_html_nonce');

/**
 * 替换字符串中首次出现的指定子串
 * 
 * @param string $search 要查找的子串
 * @param string $replace 要替换的子串
 * @param string $subject 原始字符串
 * @return string 替换后的字符串
 */
function str_replace_first($search, $replace, $subject) {
    $pos = strpos($subject, $search);
    if ($pos !== false) {
        return substr_replace($subject, $replace, $pos, strlen($search));
    }
    return $subject;
}

/**
 * 自定义评论表单
 */
function my_comment_form() {
    ob_start(); // 开始输出缓冲
    $commenter = wp_get_current_commenter(); // 获取当前评论者的信息

    // 定义表单字段
    $fields = array(
        'author' => sprintf(
            '<p class="comment-form-author"><input id="author" name="author" type="text" value="%s" size="30" maxlength="245" placeholder="%s" /></p>',
            esc_attr($commenter['comment_author']),
            __('Name', 'default') . '*'
        ),
        'email'  => sprintf(
            '<p class="comment-form-email"><input id="email" name="email" type="email" value="%s" size="30" maxlength="100" aria-describedby="email-notes" placeholder="%s" /></p>',
            esc_attr($commenter['comment_author_email']),
            __('Email', 'default') . '*'
        ),
        'url'    => sprintf(
            '<p class="comment-form-url"><input id="url" name="url" type="url" value="%s" size="30" maxlength="200" placeholder="%s" /></p>',
            esc_attr($commenter['comment_author_url']),
            __('Website', 'default')
        )
    );

    // 定义表单其他设置
    $args = array(
        'format' => 'html5',
        'fields' => $fields,
        'comment_notes_before' => '',
        'comment_field' => '<div class="comment-error" submit-error><template type="amp-mustache">{{{msg}}}</template></div><p class="comment-form-comment"><textarea id="comment" class="comment-content" name="comment" maxlength="65525" placeholder="' . __('Comment Content', 'default') . '*"></textarea></p>',
        'action' => follow_scheme_replace(get_site_url(null, '/wp-admin/admin-ajax.php?action=amp_comment_submit')),
    );

    comment_form($args); // 输出评论表单
    $comment_form = ob_get_clean(); // 获取并清除输出缓冲

    // 修改表单的action属性，以适应AMP页面需求
    $comment_form = str_replace_first('<form action', '<form on="submit-success:AMP.navigateTo(url=event.response.url)" action-xhr', $comment_form);
    echo $comment_form; // 输出修改后的表单
}

/**
 * AJAX评论提交处理函数
 */
function amp_comment_submit() {
    $comment = wp_handle_comment_submission(wp_unslash($_POST)); // 处理提交的评论数据

    if (is_wp_error($comment)) {
        $data = intval($comment->get_error_data());
        if (!empty($data)) {
            status_header(500);
            wp_send_json(array('msg' => $comment->get_error_message(), 'response' => $data));
        }
    } else {
        $location = get_comment_link($comment->comment_ID); // 获取评论链接
        if ('unapproved' === wp_get_comment_status($comment) && !empty($comment->comment_author_email)) {
            $location = add_query_arg(array('unapproved' => $comment->comment_ID, 'moderation-hash' => wp_hash($comment->comment_date_gmt)), $location); // 添加未审核评论的查询参数
        }

        $location = add_query_arg(array('rand' => rand()), $location); // 添加随机数，防止缓存
        do_action('set_comment_cookies', $comment, wp_get_current_user(), $_POST['wp-comment-cookies-consent']); // 设置评论cookie

        wp_send_json(array('success' => true, 'url' => follow_scheme_replace($location))); // 返回成功和重定向URL
    }
}
add_action('wp_ajax_amp_comment_submit', 'amp_comment_submit'); // 注册AJAX动作（已登录用户）
add_action('wp_ajax_nopriv_amp_comment_submit', 'amp_comment_submit'); // 注册AJAX动作（未登录用户）


/**
 * 加载最小化的CSS
 */
function load_css($slug, $name = null) {
    get_template_part($slug, $name);
}

/**
 * 密码保护文章的表单
 */
function post_password() {
    $post = get_post();
    $label = 'pwbox-' . (empty($post->ID) ? rand() : $post->ID);
    $output = '<form on="submit-success:AMP.navigateTo(url=event.response.url)" action-xhr="' . follow_scheme_replace(get_site_url(null, '/wp-admin/admin-ajax.php?action=amp_post_password')) . '" class="post-password-form" method="post">
        <p>' . __('This content is password protected. To view it please enter your password below:', 'default') . '</p>
        <p><label for="' . $label . '">' . __('Password:', 'default') . ' <input name="post_password" id="' . $label . '" type="password" size="20" /></label> <input type="submit" name="Submit" value="' . esc_attr_x('Enter', 'post password form', 'default') . '" /></p>
    </form>';
    
    return apply_filters('the_password_form', $output);
}

/**
 * AJAX处理密码保护文章提交
 */
function amp_post_password() {
    require_once ABSPATH . WPINC . '/class-phpass.php';
    $hasher = new PasswordHash(8, true);

    $expire = apply_filters('post_password_expires', time() + 10 * DAY_IN_SECONDS);
    $referer = wp_get_referer();
    $secure = $referer && ('https' === parse_url($referer, PHP_URL_SCHEME));

    setcookie('wp-postpass_' . COOKIEHASH, $hasher->HashPassword(wp_unslash($_POST['post_password'])), $expire, COOKIEPATH, COOKIE_DOMAIN, $secure);

    wp_send_json(array(
        'success' => true, 
        'url' => follow_scheme_replace($referer)
    ));
}
add_action('wp_ajax_amp_post_password', 'amp_post_password');
add_action('wp_ajax_nopriv_amp_post_password', 'amp_post_password');

/**
 * 重设摘要长度
 */
function reset_excerpt_length($length) {
    return 150; // 设置默认摘要长度为150个字符
}
add_filter('excerpt_length', 'reset_excerpt_length');

/**
 * 自定义Gravatar头像的服务器地址
 */
function my_get_avatar($avatar) {
    $replace_domains = array(
        "secure.gravatar.com/avatar",
        "www.gravatar.com/avatar",
        "0.gravatar.com/avatar",
        "1.gravatar.com/avatar",
        "2.gravatar.com/avatar"
    );
    return str_replace($replace_domains, "cravatar.cn/avatar", $avatar);
}
add_filter('get_avatar', 'my_get_avatar');

/**
 * 为编辑器添加样式
 */
function editor_styles() {
    add_editor_style("style-editor.css");
}
add_action("init", "editor_styles");

/**
 * 获取翻译文本
 */
function get_translate($text) {
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://microsoft-translator-text.p.rapidapi.com/translate?to%5B0%5D=en&api-version=3.0&profanityAction=NoAction&textType=plain",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode([['Text' => $text]]),
        CURLOPT_HTTPHEADER => [
            "X-RapidAPI-Host: microsoft-translator-text.p.rapidapi.com",
            "X-RapidAPI-Key: " . get_theme_mod('rapidapi_translator', ''),
            "Content-Type: application/json"
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if (!$err) {
        $response = json_decode($response, true);
        return $response[0]['translations'][0]["text"];
    } else {
        return '';
    }
}


/**
 * 对文本进行分析，获取词汇分析结果
 */
function get_word_analytic($text) {
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://text-analysis10.p.rapidapi.com/text_analysis",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "text=" . urlencode($text) . "&lang=en",
        CURLOPT_HTTPHEADER => [
            "X-RapidAPI-Host: text-analysis10.p.rapidapi.com",
            "X-RapidAPI-Key: " . get_theme_mod('rapidapi_translator', ''),
            "Content-Type: application/x-www-form-urlencoded"
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        return [];
    } else {
        $response_data = json_decode($response, true);
        return $response_data['tokens'] ?? [];
    }
}

/**
 * 根据指定词汇搜索并获取图片URL
 */
function get_image_pixabay($word) {
    $url = "https://pixabay.com/api/?key=" . get_theme_mod('pixabay_apikey', '') . "&q=" . urlencode($word) . "&image_type=photo&orientation=horizontal&safesearch=true&per_page=10";
    $response = file_get_contents($url);
    $search_result = json_decode($response, true);
    if (!empty($search_result['hits'])) {
        return $search_result['hits'][rand(0, count($search_result['hits']) - 1)]['webformatURL'];
    }
    return '';
}

/**
 * 执行Cron作业
 */
function do_cron() {
    $microtime = sprintf('%.22F', microtime(true));
    $cron_url = site_url("wp-cron.php?doing_wp_cron={$microtime}");
    set_transient('doing_cron', $microtime);

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => $cron_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "",
        CURLOPT_NOSIGNAL => 1,
        CURLOPT_TIMEOUT_MS => 100,
    ]);

    curl_exec($curl);
    curl_close($curl);
}

/**
 * 在文章插入后自动处理特色图像
 */
add_action('wp_insert_post', 'my_insert_post', 10, 3);
function my_insert_post($post_ID, $post, $update) {
    $check = $update && in_array($post->post_status, ['publish', 'future']) && empty(get_the_post_thumbnail($post_ID)) && $post->post_type == 'post' && !empty(get_theme_mod('pixabay_apikey', '')) && !empty(get_theme_mod('rapidapi_translator', ''));

    if ($check) {
        if (defined('XMLRPC_REQUEST')) {
            if (false === wp_get_schedule('insert_featured_image', [$post_ID, $post, $update])) {
                wp_schedule_single_event(time() - 1, 'insert_featured_image', [$post_ID, $post, $update]);
                do_cron();
            }
        } else {
            auto_featured_image($post_ID, $post, $update);
        }
    }
}

/**
 * 自动设置文章的特色图像
 */
add_action('insert_featured_image', 'auto_featured_image', 10, 3);
/**
 * 当文章被创建或更新时自动生成特色图像
 */
function auto_featured_image($post_ID, $post, $update) {
    $translate = get_translate($post->post_title);
    $keywords = get_word_analytic($translate);
    if (!empty($keywords)) {
        $search_words = select_keyword($keywords);
        $final_image = get_image_pixabay($search_words);
        if ($final_image) {
            set_featured_image($post_ID, $final_image);
        }
    }
}

/**
 * 选择关键词
 */
function select_keyword($keywords) {
    $priorities = ['PROPN', 'NOUN'];
    foreach ($priorities as $priority) {
        if (!empty($keywords[$priority])) {
            return $keywords[$priority][array_rand($keywords[$priority])];
        }
    }
    $rand_key = array_rand($keywords);
    return $keywords[$rand_key][array_rand($keywords[$rand_key])];
}

/**
 * 设置特色图像
 */
function set_featured_image($post_ID, $image_url) {
    $upload_dir = wp_upload_dir();
    $image_name = md5($image_url);
    $filename = $upload_dir['path'] . '/' . $image_name . '.jpg';

    if ($image_data = file_get_contents($image_url)) {
        file_put_contents($filename, $image_data);
        $attachment = [
            'guid'           => $upload_dir['url'] . '/' . $image_name . '.jpg', 
            'post_mime_type' => 'image/jpeg',
            'post_title'     => $image_name,
            'post_content'   => '',
            'post_status'    => 'inherit'
        ];
        $attach_id = wp_insert_attachment($attachment, $filename, $post_ID);
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        wp_update_attachment_metadata($attach_id, wp_generate_attachment_metadata($attach_id, $filename));
        set_post_thumbnail($post_ID, $attach_id);
    }
}

/**
 * 删除HTML内容中图片的宽度和高度属性
 */
function ludou_remove_width_height_attribute($content) {
    return preg_replace('/<img\s+[^>]*?(width|height)="[^"]*"\s*[^>]*>/i', '<img ', $content);
}

// /**
//  * 若为移动设备，移除文章内容中img的width和height属性
//  */
// if (wp_is_mobile()) {
//     add_filter('the_content', 'ludou_remove_width_height_attribute', 99);
// }

/**
 * 禁用内容转换格式功能
 */
remove_filter('the_content', 'wptexturize');

/**
 * 注册自定义器设置和控制 --- 2024.04.16
 * @param WP_Customize_Manager $wp_customize Customizer对象，用于添加设置和控制
 */
function theme_customize_register($wp_customize) {
    // 向WordPress自定义器添加一个新的部分（区域），用于后续的自定义选项
    $wp_customize->add_section('theme_sort_options', array(
        'title'    => __('Category Widget Sorting', 'your-theme-domain'),  // 标题：分类排序选项
        'priority' => 30,  // 该部分在自定义器中的优先级
    ));

    // 获取所有已注册的菜单
    $menus = wp_get_nav_menus();  // 调用WordPress函数获取所有菜单
    $menu_options = array();  // 初始化菜单选项数组
    foreach ($menus as $menu) {  // 遍历每个菜单
        $menu_options[$menu->term_id] = $menu->name;  // 将菜单ID与菜单名称作为键值对存入数组
    }

    // 向自定义器添加一个设置选项，用于选择菜单
    $wp_customize->add_setting('menu_choice', array(
        'default'   => '',  // 默认值为空
        'transport' => 'refresh',  // 当选项变更时的页面刷新方式
    ));

    // 为上面的设置添加一个控制器，允许用户通过下拉菜单选择一个菜单
    $wp_customize->add_control('menu_choice_control', array(
        'label'    => __('Choose Menu for Sorting', 'your-theme-domain'),  // 标签：选择用于排序的菜单
        'section'  => 'theme_sort_options',  // 控制器所属的部分
        'settings' => 'menu_choice',  // 绑定的设置项
        'type'     => 'select',  // 控制器类型：下拉菜单
        'choices'  => $menu_options,  // 下拉菜单的选项，即之前获取的菜单
    ));

    // 添加一个设置，用于选择分类的排序方式
    $wp_customize->add_setting('sort_order', array(
        'default'   => 'menu_order',  // 默认排序方式为菜单顺序
        'transport' => 'refresh',  // 当选项变更时的页面刷新方式
    ));

    // 为排序方式添加一个控制器，允许用户选择排序方式
    $wp_customize->add_control('sort_order_control', array(
        'label'    => __('Select Category Sort Order', 'your-theme-domain'),  // 标签：选择分类排序方式
        'section'  => 'theme_sort_options',  // 控制器所属的部分
        'settings' => 'sort_order',  // 绑定的设置项
        'type'     => 'select',  // 控制器类型：下拉菜单
        'choices'  => array(
            'menu_order' => __('Menu Order', 'your-theme-domain'),  // 菜单顺序
            'name'       => __('Name', 'your-theme-domain'),  // 按名称排序
            'count'      => __('Count', 'your-theme-domain'),  // 按数量排序
        ),
    ));

    // 添加一个设置，用于选择排序方向（升序或降序）
    $wp_customize->add_setting('sort_direction', array(
        'default'   => 'ASC',  // 默认为升序
        'transport' => 'refresh',  // 当选项变更时的页面刷新方式
    ));

    // 为排序方向添加一个控制器，允许用户选择升序或降序
    $wp_customize->add_control('sort_direction_control', array(
        'label'    => __('Select Sort Direction', 'your-theme-domain'),  // 标签：选择排序方向
        'section'  => 'theme_sort_options',  // 控制器所属的部分
        'settings' => 'sort_direction',  // 绑定的设置项
        'type'     => 'select',  // 控制器类型：下拉菜单
        'choices'  => array(
            'ASC'  => __('Ascending', 'your-theme-domain'),  // 升序
            'DESC' => __('Descending', 'your-theme-domain'),  // 降序
        ),
    ));
}

add_action('customize_register', 'theme_customize_register');  // 将上述函数挂载到'customize_register'动作上

/**
 * 自定义分类排序功能
 */
function customize_category_order_by_menu($args) {
    // 获取用户设置的菜单ID、排序方式和排序方向
    $menu_id = get_theme_mod('menu_choice', '');
    $sort_by = get_theme_mod('sort_order', 'menu_order');
    $sort_direction = get_theme_mod('sort_direction', 'ASC');

    if (!empty($menu_id)) {  // 如果用户已选择一个菜单
        $menu_items = wp_get_nav_menu_items($menu_id);  // 获取该菜单的所有菜单项
        $order = array();  // 初始化排序数组

        foreach ($menu_items as $item) {  // 遍历菜单项
            if ($item->object == 'category') {  // 如果菜单项是分类
                $order[] = $item->object_id;  // 将分类ID添加到排序数组中
            }
        }

        // 根据用户选择的排序方式应用排序参数
        switch ($sort_by) {
            case 'menu_order':  // 如果是按菜单顺序排序
                if (!empty($order)) {
                    $args['include'] = implode(',', $order);  // 将排序数组转换为字符串，设置为包含参数
                    $args['orderby'] = 'include';  // 设置排序字段为包含
                }
                break;
            case 'name':  // 如果是按名称排序
                $args['orderby'] = 'name';  // 设置排序字段为名称
                $args['order'] = $sort_direction;  // 设置排序方向
                break;
            case 'count':  // 如果是按数量排序
                $args['orderby'] = 'count';  // 设置排序字段为数量
                $args['order'] = $sort_direction;  // 设置排序方向
                break;
        }
    }

    return $args;  // 返回修改后的参数
}

add_filter('widget_categories_args', 'customize_category_order_by_menu');  // 将上述函数挂载到'widget_categories_args'过滤器上

function my_theme_scripts() {
    // 假设您的JavaScript文件放在主题的js目录下
    wp_enqueue_script('my-custom-script', get_template_directory_uri() . '/js/custom-script.js', array(), '1.0', true);
}
add_action('wp_enqueue_scripts', 'my_theme_scripts');