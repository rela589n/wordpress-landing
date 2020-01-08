<?php
// constant

define('GWT_STYLE_DIR', get_stylesheet_uri());
define('GWT_DIR', dirname(GWT_STYLE_DIR));
define('GWT_CSS_DIR', GWT_DIR . '/assets/css');
define('GWT_IMG_DIR', GWT_DIR . '/assets/img');
define('GWT_JS_DIR', GWT_DIR . '/assets/js');

add_filter('show_admin_bar', '__return_false');
add_filter('jpeg_quality', function ($arg) {return 100;});


add_action('wp_head', function () {
    $ajax = get_admin_url() . 'admin-ajax.php';
    echo '<script>window.wp_ajax =' . json_encode($ajax) . '; </script>';
});
add_action('wp_ajax_form', 'my_ajax_func');
add_action('wp_ajax_nopriv_form', 'my_ajax_func');
function my_ajax_func()
{
    function clean($value = "")
    {
        $value = trim($value);
        $value = stripslashes($value);
        $value = strip_tags($value);
        $value = htmlspecialchars($value);
        return $value;
    }

    $err = '';
    $name = clean($_POST['name']);
    $email = clean($_POST['email']);
    $subject = clean($_POST['subject']);
    $message = clean($_POST['message']);
    if (mb_strlen($name) == 0) {
        $err .= 'Поле "Имя" не должно быть пустым' . "\r\n";
    } else
        if (!preg_match("/^[A-Za-zА-Яа-яЁёЇїІіЄє\s]*$/", $name)) {
            $err .= 'В поле "Имя" только буквы!' . "\r\n";
        }
    if (mb_strlen($email) == 0) {
        $err .= 'E-mail не должен быть пустым' . "\r\n";
    } else
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $err .= 'Ошибка валидации email-адресса! Проверьте его ещё раз.' . "\r\n";
        }
    if (mb_strlen($subject) < 5) {
        $err .= 'Введите тему сообщения' . "\r\n";
    }
    if (mb_strlen($message) < 5) {
        $err .= 'Введите сообщение' . "\r\n";
    }

    if ($err) {
        echo json_encode([
            'err' => $err,
            'res' => 0
        ]);
    } else {
        $to = get_option('contact_email');
        $message = wordwrap($message, 70, "\r\n");
		$url = str_replace('http://', '', get_site_url(null, '', 'http'));
		$headers = 'From: ' . get_bloginfo('name') . ' <' . $url . '>' . "\r\n";
        if(wp_mail($to, $subject, $message, $headers)) {
            echo json_encode([
                'res' => 1,
                'data' => get_option('contact_success_text')
            ]);
			wp_mail($email, 'Спасибо за сообщение - ' . get_bloginfo('name'), 'Спасибо за обращение, мы скоро вам ответим!');
        } else {
            echo json_encode([
                'res' => 0,
                'data' => get_option('contact_sending_error')
            ]);
        }
    }
    /* without wp_die will not work  */
    wp_die();
}
add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('custom-logo');
    register_nav_menu('top_menu', 'Верхнее меню');
});
add_action("wp_enqueue_scripts", function () {
    wp_register_style('main-template-fonts', 'https://fonts.googleapis.com/css?family=Montserrat:400,700|Roboto+Slab&amp;subset=cyrillic-ext', array(), null);
    wp_enqueue_style('main_template_style', GWT_STYLE_DIR, array('main-template-fonts'), null);
    wp_deregister_script('jquery');
    wp_register_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js', array(), null, 1);
    wp_register_script('cubic-bezier-script', GWT_JS_DIR . '/jquery.bez.min.js', array('jquery'), null, 1);
    wp_enqueue_script('main-template-script', GWT_JS_DIR . '/script.js', array('cubic-bezier-script'), null, 1);

});

//кастомные стили для админки
function my_stylesheet1()
{
    wp_enqueue_style("social-style", GWT_CSS_DIR . '/style-admin.css');
}

add_action('admin_head', 'my_stylesheet1');

// регистрируем свои типы постов
add_action('init', function () {
    add_theme_support('post-thumbnails', ['post', 'service', 'work', 'development', 'worker', 'partner']);
    register_post_type('service', array(
        'labels' => array(
            'name' => 'Сервисы',
            'singular_name' => 'Сервис',
            'add_new' => 'Добавить новый',
            'add_new_item' => 'Добавить новый сервис',
            'edit_item' => 'Редактировать сервис',
            'new_item' => 'Новый сервис',
            'view_item' => 'Посмотреть сервис',
            'search_items' => 'Найти сервис',
            'not_found' => 'Сервисов не найдено',
            'not_found_in_trash' => 'В корзине сервисов не найдено',
            'parent_item_colon' => '',
            'menu_name' => 'Сервисы'

        ),
        'public' => true,
        // не делать страницу под запись
        'publicly_queryable' => false,
        'query_var' => false,
        //
        'show_ui' => true,
        'show_in_menu' => true,
        'exclude_from_search' => true,
        'rewrite' => false,
        'menu_icon' => 'dashicons-image-filter',
        'capability_type' => 'post',
        'has_archive' => false,
        'hierarchical' => false,
        'menu_position' => 5.1,
        'supports' => array('title', 'editor', 'thumbnail')
        //array('title','editor','author','thumbnail','excerpt','comments')
    ));
    register_post_type('work', array(
        'labels' => array(
            'name' => 'Работы',
            'singular_name' => 'Пример работы',
            'add_new' => 'Добавить работу',
            'add_new_item' => 'Добавить пример работы',
            'edit_item' => 'Редактировать пример',
            'new_item' => 'Новая работа',
            'view_item' => 'Посмотреть работу',
            'search_items' => 'Найти пример работы',
            'not_found' => 'Примеров работ не найдено',
            'not_found_in_trash' => 'В корзине работ не найдено',
            'parent_item_colon' => '',
            'menu_name' => 'Портфолио'

        ),
        'public' => true,
        // не делать страницу под запись
        'publicly_queryable' => false,
        'query_var' => false,
        //
        'show_ui' => true,
        'show_in_menu' => true,
        'exclude_from_search' => true,
        'rewrite' => false,
        'menu_icon' => 'dashicons-image-filter',
        'capability_type' => 'post',
        'has_archive' => false,
        'hierarchical' => false,
        'menu_position' => 5.2,
        'supports' => array('title', 'editor', 'thumbnail')
    ));
    register_post_type('development', array(
        'labels' => array(
            'name' => 'События',
            'singular_name' => 'Пример событий',
            'add_new' => 'Добавить событие',
            'add_new_item' => 'Добавить новое событие',
            'edit_item' => 'Редактировать пример',
            'new_item' => 'Новое событие',
            'view_item' => 'Посмотреть событие',
            'search_items' => 'Найти событие',
            'not_found' => 'Примеров событий не найдено',
            'not_found_in_trash' => 'В корзине событий не найдено',
            'parent_item_colon' => '',
            'menu_name' => 'О нас'

        ),
        'public' => true,
        // не делать страницу под запись
        'publicly_queryable' => false,
        'query_var' => false,
        //
        'show_ui' => true,
        'show_in_menu' => true,
        'exclude_from_search' => true,
        'rewrite' => false,
        'menu_icon' => 'dashicons-image-filter',
        'capability_type' => 'post',
        'has_archive' => false,
        'hierarchical' => false,
        'menu_position' => 5.3,
        'supports' => array('title', 'editor', 'thumbnail')
    ));
    register_post_type('worker', array(
        'labels' => array(
            'name' => 'Сотрудники',
            'singular_name' => 'Сотрудник',
            'add_new' => 'Добавить сотрудника',
            'add_new_item' => 'Добавить нового сотрудника',
            'edit_item' => 'Редактировать информацию об сотруднике',
            'new_item' => 'Новый сотрудник',
            'view_item' => 'Посмотреть информацию про сотрудника',
            'search_items' => 'Найти сотрудника',
            'not_found' => 'Примеров событий не найдено',
            'not_found_in_trash' => 'В корзине сотрудников не найдено',
            'parent_item_colon' => '',
            'menu_name' => 'Наша команда'

        ),
        'public' => true,
        // не делать страницу под запись
        'publicly_queryable' => false,
        'query_var' => false,
        //
        'show_ui' => true,
        'show_in_menu' => true,
        'exclude_from_search' => true,
        'rewrite' => false,
        'menu_icon' => 'dashicons-image-filter',
        'capability_type' => 'post',
        'has_archive' => false,
        'hierarchical' => false,
        'menu_position' => 5.4,
        'supports' => array('title', 'editor', 'thumbnail')
    ));
    register_post_type('partner', array(
        'labels' => array(
            'name' => 'Партнеры',
            'singular_name' => 'Партнер',
            'add_new' => 'Добавить нового',
            'add_new_item' => 'Добавить партнера',
            'edit_item' => 'Редактировать',
            'new_item' => 'Новый партнер',
            'view_item' => 'Посмотреть партнера',
            'search_items' => 'Найти партнера',
            'not_found' => 'Партнеров не найдено',
            'not_found_in_trash' => 'В корзине партнеров не найдено',
            'parent_item_colon' => '',
            'menu_name' => 'Партнеры'

        ),
        'public' => true,
        // не делать страницу под запись
        'publicly_queryable' => false,
        'query_var' => false,
        //
        'show_ui' => true,
        'show_in_menu' => true,
        'exclude_from_search' => true,
        'rewrite' => false,
        'menu_icon' => 'dashicons-image-filter',
        'capability_type' => 'post',
        'has_archive' => false,
        'hierarchical' => false,
        'menu_position' => 5.5,
        'supports' => array('title', 'thumbnail')
        //array('title','editor','author','thumbnail','excerpt','comments')
    ));
    register_taxonomy('modal_category', array('work'), array(
        'label'                 => 'Категории',
        'labels'                => array(
            'name'              => 'Категории',
            'singular_name'     => 'Категория',
            'search_items'      => 'Искать категорию',
            'all_items'         => 'Все категории',
            'view_item '        => 'Посмотреть категорию',
            'edit_item'         => 'Редактировать категорию',
            'update_item'       => 'Обновить категорию',
            'add_new_item'      => 'Добавить новую',
            'new_item_name'     => 'Новая',
            'menu_name'         => 'Категории',
        ),
        'rewrite'       => false,
        'show_ui'       => true,
        'query_var'     => false,
        'show_in_menu'  => false,
        //'rewrite'       => array( 'slug' => 'the_genre' ), // свой слаг в URL
    ));
});
add_filter('the_content', 'do_shortcode', 11);
function filter_ptags_on_images($content)
{
//функция preg replace, которая убивает тег p
    return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
}

add_image_size('gwt-service-image', 72, 72, array('center', 'center'));
add_image_size('gwt-portfolio-example-image', 360, 370, array('center', 'center'));
add_image_size('gwt-about-image', 198, 198, array('center', 'center'));
add_image_size('gwt-team-image', 236, 236, array('center', 'center'));
add_image_size('gwt-team-image', 236, 236, array('center', 'center'));
add_image_size('gwt-partner-image', 185, 50, array('center', 'center'));

//register settings
function gwt_labels_callback($v)
{
    return htmlspecialchars(trim($v));
}
// ------------------------------------------------------------------
// Callback функции выводящие HTML код опций
// ------------------------------------------------------------------
//
function social_callback($inc)
{ ?>
    <input
            type="<?php echo ($inc['type']) ? $inc['type'] : 'text'; ?>"
            name="<?php echo $inc['option_name']; ?>"
            id="<?php echo $inc['option_name']; ?>"
            value="<?php echo esc_attr(get_option($inc['option_name'])); ?>"
        <?php echo $inc['attr']; ?>
    >
    <?php
}

function social_callback_textarea($inc)
{ ?>
    <textarea
            name="<?php echo $inc['option_name']; ?>"
            id="<?php echo $inc['option_name']; ?>"
        <?php echo $inc['attr']; ?>
    ><?php echo esc_attr(get_option($inc['option_name'])); ?></textarea>
    <?php
}
function social_callback_radio ($inc) {
    $checked = esc_attr(get_option($inc['option_name']));
    foreach($inc['values'] as $val => $description) {
        ?>
        <input
                type="radio"
                name="<?php echo $inc['option_name']; ?>"
                id="<?php echo $val;?>"
                value="<?php echo '#' . $val; ?>"
            <?php
            if($checked == '#' . $val) echo 'checked="checked"';
            echo $inc['attr']; ?>
        >
        <label style="font-size: 1.2em; line-height: 1.4;" for="<?php echo $val;?>"><?php echo $description; ?></label><br>
        <?php
    }
}

// Регистрируем поля для меню под шапку
add_action('admin_init', function () {
    add_settings_section(
        'header_labels', // id of section. We will use it to attach fields to the section
        '', // title of section
        function () {
        }, //Функция заполняет секцию описанием. Вызывается перед выводом полей.
        'gwt_header_settings' /*Страница на которой выводить секцию.
                                  * Должен совпадать с параметром $page в
                                  * do_setting_sections( $page );
                                  */
    );
    add_settings_field(
        'header_first_title',
        'Первый заголовок',
        'social_callback',
        'gwt_header_settings',
        'header_labels',
        [
            'option_name' => 'header_first_title'
        ]
    );
    add_settings_field(
        'header_second_title',
        'Второй заголовок',
        'social_callback',
        'gwt_header_settings',
        'header_labels',
        [
            'option_name' => 'header_second_title'
        ]
    );
    add_settings_field(
        'header_link_text',
        'Текст кнопки',
        'social_callback',
        'gwt_header_settings',
        'header_labels',
        [
            'option_name' => 'header_link_text'
        ]
    );
    add_settings_field(
        'header_btn_link',
        'Направление кнопки:',
        'social_callback_radio',
        'gwt_header_settings',
        'header_labels',
        [
            'option_name' => 'header_btn_link',
            'values' => [
                'services' => '#services',
                'portfolio' => '#portfolio',
                'about' =>'#about',
                'team' =>'#team',
                'contact' =>'#contact'
            ],
            'attr' => 'style="margin-top:5px;"'
        ]
    );
    register_setting('header_labels', 'header_first_title');
    register_setting('header_labels', 'header_second_title');
    register_setting('header_labels', 'header_link_text');
    register_setting('header_labels', 'header_btn_link');
});

// регистрируем настройки футера
add_action('admin_init', function () {
    // Добавляем поля опций. Указываем название, описание,
    // функцию выводящую html код поля опции.
    add_settings_field(
        'copy',
        'Copyright',
        'social_callback',
        'footer_settings', // страница
        'copyright_sect', // секция
        [
            'option_name' => 'copy'
        ]
    );
    add_settings_field(
        'btn_up_text',
        'Текст кнопки "Вверх"',
        'social_callback',
        'footer_settings', // страница
        'copyright_sect', // секция
        [
            'option_name' => 'btn_up_text'
        ]
    );

    add_settings_field(
        'tw',
        'Twitter',
        'social_callback',
        'footer_settings', // страница
        'social', // секция
        [
            'option_name' => 'tw'
        ]
    );
    add_settings_field(
        'fb',
        'Facebook',
        'social_callback',
        'footer_settings', // страница
        'social', // секция
        [
            'option_name' => 'fb'
        ]
    );
    add_settings_field(
        'ps',
        'Pinterest',
        'social_callback',
        'footer_settings', // страница
        'social', // секция
        [
            'option_name' => 'ps'
        ]
    );
    add_settings_field(
        'gp',
        'Google plus',
        'social_callback',
        'footer_settings', // страница
        'social', // секция
        [
            'option_name' => 'gp'
        ]
    );

    // Добавляем блок опций на страницу footer_settings
    add_settings_section(
        'copyright_sect', // секция
        'Копирайт & кнопка "Вверх"',
        '',
        'footer_settings' // страница
    );


    add_settings_section(
        'social', // секция
        'Соц. сети',
        '',
        'footer_settings' // страница
    );

    // Регистрируем опции, чтобы они сохранялись при отправке
    // $_POST параметров и чтобы callback функции опций выводили их значение.
    register_setting('footer_settings', 'copy', ['sanitize_callback' => 'gwt_labels_callback']);
    register_setting('footer_settings', 'btn_up_text', ['sanitize_callback' => 'gwt_labels_callback']);
    register_setting('footer_settings', 'tw', ['sanitize_callback' => 'gwt_labels_callback']);
    register_setting('footer_settings', 'fb', ['sanitize_callback' => 'gwt_labels_callback']);
    register_setting('footer_settings', 'ps', ['sanitize_callback' => 'gwt_labels_callback']);
    register_setting('footer_settings', 'gp', ['sanitize_callback' => 'gwt_labels_callback']);


    add_settings_field(
        'contact_name_field',
        'Внутри поля имени:',
        'social_callback',
        'GWT_contact', // страница
        'contact_fields', // секция
        [
            'option_name' => 'contact_name_field'
        ]
    );
    add_settings_field(
        'contact_email_field',
        'Внутри поля e-mail:',
        'social_callback',
        'GWT_contact', // страница
        'contact_fields', // секция
        [
            'option_name' => 'contact_email_field'
        ]
    );
    add_settings_field(
        'contact_subject_field',
        'Внутри поля темы сообщения:',
        'social_callback',
        'GWT_contact', // страница
        'contact_fields', // секция
        [
            'option_name' => 'contact_subject_field'
        ]
    );
    add_settings_field(
        'contact_message_field',
        'Внутри поля сообщения:',
        'social_callback',
        'GWT_contact', // страница
        'contact_fields', // секция
        [
            'option_name' => 'contact_message_field'
        ]
    );
    add_settings_field(
        'contact_btn_text',
        'Текст внутри кнопки:',
        'social_callback',
        'GWT_contact', // страница
        'contact_fields', // секция
        [
            'option_name' => 'contact_btn_text'
        ]
    );
    add_settings_field(
        'contact_success_text',
        'Сообщение при успешной отправке:',
        'social_callback_textarea',
        'GWT_contact', // страница
        'contact_sending', // секция
        [
            'option_name' => 'contact_success_text',
            'attr'        => 'class="regular-text"'
        ]
    );
    add_settings_field(
        'contact_sending_error',
        'Текст при ошибке отправки',
        'social_callback_textarea',
        'GWT_contact', // страница
        'contact_sending', // секция
        [
            'option_name' => 'contact_sending_error',
            'attr'        => 'class="regular-text"'
        ]
    );
    add_settings_field(
        'contact_email',
        'Почта куда будут приходить сообщения',
        'social_callback',
        'GWT_contact', // страница
        'contact_sending', // секция
        [
            'option_name' => 'contact_email',
            'type'        => 'email'
        ]
    );

    add_settings_section('contact_fields', 'Подсказки', '', 'GWT_contact');
    add_settings_section('contact_sending', 'Отправка', '', 'GWT_contact');
    register_setting('contact_fields', 'contact_name_field', ['sanitize_callback' => 'gwt_labels_callback']);
    register_setting('contact_fields', 'contact_email_field', ['sanitize_callback' => 'gwt_labels_callback']);
    register_setting('contact_fields', 'contact_subject_field', ['sanitize_callback' => 'gwt_labels_callback']);
    register_setting('contact_fields', 'contact_message_field', ['sanitize_callback' => 'gwt_labels_callback']);
    register_setting('contact_fields', 'contact_btn_text', ['sanitize_callback' => 'gwt_labels_callback']);
    register_setting('contact_fields', 'contact_success_text', ['sanitize_callback' => 'gwt_labels_callback']);
    register_setting('contact_fields', 'contact_sending_error', ['sanitize_callback' => 'not_empty_field']);
    register_setting('contact_fields', 'contact_email', ['sanitize_callback' => 'not_empty_field_email']);


});
function not_empty_field($v)
{
    $v = htmlspecialchars(trim($v));
    if(mb_strlen($v) < 1)
        return get_option('contact_sending_error');
        else
            return $v;

}
function not_empty_field_email($v)
{
    $v = htmlspecialchars(trim($v));
    if(mb_strlen($v) < 1)
        return get_option('contact_email');
    else
        return $v;

}

add_action('admin_menu', function () {
    // Удалим из меню пункт "Записи"
    remove_menu_page('edit.php');
    // Добавим в меню пункт "Настройки шапки":
    add_menu_page('Настройки шапки', 'Настройки шапки', '6', 'gwt_header_settings',
        function () { ?>
            <div class="wrap">
                <?php echo '<h2>Базовые настройки верхней части сайта</h2>'; ?>
                <form action="options.php" method="POST">
                    <?php settings_fields('header_labels'); ?>
                    <?php do_settings_sections('gwt_header_settings'); ?>
                    <?php submit_button(); ?>
                </form>
            </div>
            <?php
        }, '', 5
    );
    add_submenu_page(
        'options-general.php',
        'Базовые настройки футера',
        'Настройки футера',
        '6',
        'footer_settings',
        'footer_options'
    );
    function footer_options()
    { ?>
        <div class="wrap">
            <form action="options.php" method="POST">
                <h1>Настройки футера сайта и кнопки "Вверх"</h1>
                <?php
                    settings_fields('footer_settings');
                    do_settings_sections('footer_settings');
                    submit_button(); ?>
            </form>
        </div>
        <?php
    }

    add_menu_page('Настройки контактной формы', 'Контактная форма', '6', 'GWT_contact', 'GWT_contact_callback', 'dashicons-format-aside', 33);
    function GWT_contact_callback() { ?>
        <div class="wrap">
            <form action="options.php" method="POST">
                <h1>Настройки контактной формы</h1>
                <?php
                settings_fields('contact_fields');
                do_settings_sections('GWT_contact');
                submit_button(); ?>
            </form>
        </div>
        <?php
    }
    add_submenu_page(
        'GWT_contact',
        'Настройки заголовков',
        'Заглавия',
        '6',
        'title_settings_contact', //menu_slug
        'title_settings_contact' //callback
    );
});
function title_settings_contact()
{
    ?>
    <div class="wrap">
        <form action="options.php" method="POST">
            <h1>Настройки Заголовков</h1>
            <?php
            settings_fields('contact_title_fields');
            do_settings_sections('title_settings_contact');
            submit_button(); ?>
        </form>
    </div>
    <?php
}

function title_settings_service()
{
    ?>
    <div class="wrap">
        <form action="options.php" method="POST">
            <h1>Настройки Заголовков</h1>
            <?php
            settings_fields('service_title_fields');
            do_settings_sections('title_settings_service');
            submit_button(); ?>
        </form>
    </div>
    <?php
}
function title_settings_work()
{
    ?>
    <div class="wrap">
        <form action="options.php" method="POST">
            <h1>Настройки Заголовков</h1>
            <?php
            settings_fields('work_title_fields');
            do_settings_sections('title_settings_work');
            submit_button(); ?>
        </form>
    </div>
    <?php
}
function title_settings_development()
{
    ?>
    <div class="wrap">
        <form action="options.php" method="POST">
            <h1>Настройки Заголовков</h1>
            <?php
            settings_fields('development_title_fields');
            do_settings_sections('title_settings_development');
            submit_button(); ?>
        </form>
    </div>
    <?php
}
function title_settings_worker()
{
    ?>
    <div class="wrap">
        <form action="options.php" method="POST">
            <h1>Настройки Заголовков</h1>
            <?php
            settings_fields('worker_title_fields');
            do_settings_sections('title_settings_worker');
            submit_button(); ?>
        </form>
    </div>
    <?php
}
add_action('admin_menu', function () {
    $GWT_titles = ['service', 'work', 'development', 'worker'];
    for($i = 0;  $i < count($GWT_titles); $i++) {
        add_submenu_page(
            'edit.php?post_type=' . $GWT_titles[$i],
            'Настройки заголовков',
            'Заглавия',
            '6',
            'title_settings_' . $GWT_titles[$i], // menu_slug
            'title_settings_' . $GWT_titles[$i] // callback
        );
    }
});
add_action('admin_init', function () {
    $GWT_titles = ['service', 'work', 'development', 'worker', 'contact'];
    for($i = 0;  $i < count($GWT_titles); $i++) {
        $page = 'title_settings_' . $GWT_titles[$i];
        $section = $GWT_titles[$i] . '_title_fields';
        $first_title = 'first_title_' . $GWT_titles[$i];
        $second_title = 'second_title_' . $GWT_titles[$i];
        add_settings_section($section, '', '', $page);
        add_settings_field(
            $first_title,
            'Первый заголовок: ',
            'social_callback',
            $page, // страница
            $section, // секция
            [
                'option_name' => $first_title
            ]
        );
        add_settings_field(
            $second_title,
            'Ворой заголовок: ',
            'social_callback',
            $page, // страница
            $section, // секция
            [
                'option_name' => $second_title
            ]
        );
        register_setting($section, $first_title, ['sanitize_callback' => 'gwt_labels_callback']);
        register_setting($section, $second_title, ['sanitize_callback' => 'gwt_labels_callback']);
    }
});