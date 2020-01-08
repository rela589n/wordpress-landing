<?php
/*
Template Name: Главная
*/
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="description" content="<?php bloginfo('description'); ?>">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<header id="home">
    <div class="wrapper">
        <div class="js-fixed-header-menu">
            <div class="clearfix">
                <?php if (has_custom_logo()):
                    $logo_img = '';
                    if ($custom_logo_id = get_theme_mod('custom_logo')) {
                        $logo_img = wp_get_attachment_image($custom_logo_id, 'full', false, array(
                            'class' => 'custom-logo',
                            'itemprop' => 'logo',
                        ));
                    }
                    echo $logo_img;
                else:?>
                    <div style="color: #fed136; font-size: 24px; font-family: 'DroidSerif Italic', sans-serif;"
                         class="custom-logo" itemprop="logo">
                        <?php bloginfo('name'); ?>
                    </div>
                <?php endif; ?>
                <div class="menu">
                    <div class="menu__icon">
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                    <?php
                    if (has_nav_menu('top_menu')) {
                        wp_nav_menu(array(
                            'theme_location' => 'top_menu',
                            'container' => '',
                            'items_wrap' => '<ul class="menu_links">%3$s</ul>',
                        ));
                    }
                    ?>

                </div>
            </div>
        </div>
        <div class="titles animated fadeInDown">
            <?php
            $default_hft = 'Welcome to our studio!';
            $default_hst = 'It’s nice to meet you';
            $default_hlt = 'Tell me more';
            $hft = get_option('header_first_title');
            $hst = get_option('header_second_title');
            $hlt = get_option('header_link_text'); ?>
            <h3><?php echo $hft ? $hft : $default_hft; ?></h3>
            <h1><?php echo $hst ? $hst : $default_hst; ?></h1>
        </div>
        <a href="<?php echo get_option('header_btn_link');?>" class="button more"><?php echo $hlt ? $hlt : $default_hlt; ?></a>
    </div>
</header>
<?php
$services = new WP_Query([
    'post_type' => 'service',
    'order' => 'DESC',
    'posts_per_page' => -1
]);
if ($services->have_posts()):?>
    <section id="services" class="attendance">
        <div class="wrapper">
            <h2><?php $title = get_option('first_title_service'); echo $title ? $title : 'Services'; ?></h2>
            <h4><?php $title = get_option('second_title_service'); echo $title ? $title : ''; ?></h4>
            <div class="services">
                <?php while ($services->have_posts()) :
                    $services->the_post(); ?>
                    <div class="service">
                        <div class="image_border">
                            <?php if (has_post_thumbnail()) {
                                the_post_thumbnail('gwt-service-image');
                            } ?>
                        </div>
                        <h5><?php the_title(); ?></h5>
                        <?php the_content(); ?>
                    </div>
                    <?php
                endwhile; ?>
            </div>
        </div>
    </section>
<?php endif;
wp_reset_postdata();

$works = new WP_Query([
    'post_type' => 'work',
    'posts_per_page' => -1
]);
//var_dump($works);
if ($works->have_posts()) :
    $iteration = 0;
    $modals = [];?>
    <section id="portfolio" class="portfolio">
        <div class="wrapper">
            <h2><?php $title = get_option('first_title_work'); echo $title ? $title : 'Our portfolio'; ?></h2>
            <h4><?php $title = get_option('second_title_work'); echo $title ? $title : ''; ?></h4>
            <div class="examples_of_work">
                <?php
                while ($works->have_posts()):
                    $works->the_post();
                    $fields = get_fields();
                    $terms = get_the_terms($post->ID, 'modal_category');
                    ?>
                    <div class="example">
                        <div class="portfolio_thumbnail modal_open" data-modal-id="<?php echo $iteration; ?>">
                            <div class="thumbnail_hover">
                                <i class="icon-plus-solid"></i>
                            </div>
                            <?php if (has_post_thumbnail()) {
                                the_post_thumbnail('gwt-portfolio-example-image');
                            } ?>
                        </div>
                        <div class="portfolioCaptions">
                            <h5><?php the_title(); ?></h5>
                            <p><?php
                                $categories = '';
                                if($terms) {
                                    for($i = 0; $i < count($terms) - 1; $i++) {
                                        $categories .= $terms[$i]->name . ', ';
                                    }
                                    $categories .= $terms[count($terms) - 1]->name;
                                }
                                echo $categories;
                                ?></p>
                        </div>
                    </div>
                    <?php
                    $modals[] =
                        '
                        <div class="modal_roof animated" data-modal-id="' . $iteration . '">
                            <div class="modal_overlay">
                                <div class="modal">
                                    <h2>' . get_the_title() . '</h2>
                                    <h4>' . $fields['modal_title'] . '</h4>
                                    ' . get_the_post_thumbnail($post->ID,'full') . '
                                       <div class="content">' . get_the_content() . '</div>                                  
                                    <div class="info">
                                        <p>Date: ' . get_the_date('F Y') . '</p>
                                        <p>Client: ' . get_the_author() . '</p>
                                        <p>Category: ' . $categories . '</p>
                                    </div>
                                    <button class="button close">close project</button>
                                </div>
                                <div class="close">
                                    <div></div>
                                    <div></div>
                                </div>
                            </div>
                        </div>
                        ';
                    $iteration++;
                endwhile; ?>
            </div>
        </div>
    </section>
<?php
for($i = 0; $i < count($modals); $i++) {
    echo $modals[$i];
}
endif;
wp_reset_postdata();
$developments = new WP_Query([
    'post_type' => 'development',
    'order' => 'ASC',
    'posts_per_page' => -1
]);
if ($developments->have_posts()) :
    $iteration = 0; ?>
    <section id="about" class="about">
        <div class="wrapper">
            <h2><?php $title = get_option('first_title_development'); echo $title ? $title : 'About us'; ?></h2>
            <h4><?php $title = get_option('second_title_development'); echo $title ? $title : ''; ?></h4>
            <div class="inscriptions">
                <?php
                while ($developments->have_posts()):
                    $developments->the_post(); ?>
                    <div class="inscription">
                        <?php if (has_post_thumbnail()): ?>
                            <div class="image wow fadeInUp" data-wow-duration=".5s">
                                <div class="image_border">
                                    <?php the_post_thumbnail('gwt-about-image'); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="text">
                            <div class="h5"><?php echo get_the_date('F Y'); ?></div>
                            <h5><?php the_title(); ?></h5>
                            <?php the_content(); ?>
                        </div>
                    </div>
                    <?php
                    $iteration++;
                endwhile; ?>
                <div class="continue">
                    <div class="image_border">
                        <img src="<?php echo GWT_IMG_DIR . '/About_Image_Continues.jpg'; ?>" alt="Our story continues">
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php endif;
wp_reset_postdata();
//
$workers = new WP_Query([
    'post_type' => 'worker',
    'order' => 'ASC',
    'posts_per_page' => -1
]);
if ($workers->have_posts()) :
    $iteration = 0; ?>
    <section id="team" class="team">
        <div class="wrapper">
            <h2><?php $title = get_option('first_title_worker'); echo $title ? $title : 'Our amazing team'; ?></h2>
            <h4><?php $title = get_option('second_title_worker'); echo $title ? $title : ''; ?></h4>
            <div class="workers">
                <?php
                while ($workers->have_posts()):
                    $workers->the_post();
                    $social = get_fields();
                    ?>
                    <div class="worker">
                        <div class="image_border">
                            <?php if (has_post_thumbnail()):
                                the_post_thumbnail('gwt-team-image');
                            else:?>
                                <i class="icon-human"></i>
                            <?php endif; ?>
                        </div>
                        <h5><?php the_title(); ?></h5>
                        <?php the_content(); ?>
                        <div class="social_networks">
                            <?php
                            if ($social) {
                                $links = [
                                    'person_tw' => '" class="networks twitter" target="_blank"><i class="ico icon-twitter-brands"></i></a>',
                                    'person_fb' => '" class="networks facebook" target="_blank"><i class="ico ico icon-facebook-f-brands"></i></a>',
                                    'person_ps' => '" class="networks pinterest" target="_blank"><i class="ico ico icon-pinterest-p-brands"></i></a>',
                                    'person_gp' => '" class="networks google_plus" target="_blank"><i class="ico ico icon-google_plus"></i></a>'
                                ];
                                foreach ($social as $key => $link) {
                                    if ($link) {
                                        echo '<a href="' . $link . $links[$key];
                                    }
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                    $iteration++;
                endwhile; ?>
            </div>
            <p class="description">Proin iaculis purus consequat sem cure digni ssim donec porttitora entum
                suscipit
                aenean rhoncus posuere odio in tincidunt proin iaculis.</p>
        </div>
    </section>
<?php
endif;
wp_reset_postdata();
$workers = new WP_Query([
    'post_type' => 'partner',
//    'order' => 'ASC',
    'posts_per_page' => -1
]);
if ($workers->have_posts()) :
    $iteration = 0; ?>
    <section class="logos">
        <div class="wrapper">
            <div class="brands">
                <?php
                while ($workers->have_posts()):
                    $workers->the_post();
                    $link = get_field('partner_link');
                    ?>
                    <div class="brand wow bounceInLeft"
                         data-wow-duration="<?php $duration = 0.3 * (($iteration % 4) + 1) ?>s"
                         data-wow-delay="<?php echo 1.2 - $duration; ?>s">
                        <?php
                        $isThumb = has_post_thumbnail();
                        if ($link):
                            if ($isThumb):?>
                                <a href="<?php echo $link; ?>" target="_blank">
                                    <?php the_post_thumbnail('gwt-partner-image'); ?>
                                </a>
                            <?php else: ?>
                                <a class="noImage" href="<?php echo $link; ?>" target="_blank">
                                    <?php the_title(); ?>
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="noImage">
                                <?php
                                if ($isThumb)
                                    the_post_thumbnail('gwt-partner-image');
                                else
                                    the_title();
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php
                    $iteration++;
                endwhile; ?>
            </div>
        </div>
    </section>
<?php
endif;
wp_reset_postdata();
?>
<footer id="contact">
    <div class="about">
        <div class="wrapper">
            <h2><?php $title = get_option('first_title_contact'); echo $title ? $title : 'Contact us';?></h2>
            <h4><?php $title = get_option('second_title_contact'); echo $title ? $title : '';?></h4>
            <form action="<?php echo $ajax; ?>" method="post" id="contact_form">
                <?php
                $name = get_option('contact_name');
                $e_mail = get_option('contact_e_mail');
                $subject = get_option('contact_subject');
                $message = get_option('contact_message');
                $btn_text = get_option('contact_btn_text');
                ?>
                <div class="fields">
                    <div class="inputs">
                        <input type="text" name="name" placeholder="<?php echo $name ? $name : 'YOUR NAME'?> *" autocomplete="name">
                        <input type="email" name="email" placeholder="<?php echo $e_mail ? $e_mail : 'YOUR E-MAIL'?> *" autocomplete="email">
                        <input type="text" name="subject" placeholder="<?php echo $subject ? $subject : 'SUBJECT'?> *">
                    </div>
                    <textarea class="inputs" name="message" placeholder="<?php echo $message ? $message : 'YOUR MESSAGE'?> *"></textarea>
                </div>
                <input type="submit" value="<?php echo $btn_text ? $btn_text : 'send message'?>" class="button send">
                <?php unset($name, $e_mail, $subject, $message, $btn_text); ?>
                <img src="<?php echo GWT_IMG_DIR . '/img_loader.gif' ?>" alt="" id="img-loader">
                <div class="information"></div>
            </form>
        </div>
    </div>
    <div class="social">
        <div class="wrapper">
            <h5>
                <?php
                $copy = get_option('copy');
                if ($copy)
                    echo $copy;
                else
                    echo '&copy; Copyright' .
                        date(' Y ') .
                        str_replace('https://', '', str_replace('http://', '', home_url()));
                ?>
            </h5>
            <div class="links">
                <?php
                    $social_footer = [
                        'tw' => '<i class="ico ico icon-twitter-brands"></i>',
                        'fb' => '<i class="ico ico icon-facebook-f-brands"></i>',
                        'ps' => '<i class="ico ico icon-pinterest-p-brands"></i>',
                        'gp' => '<i class="ico ico icon-google_plus"></i>'
                    ];
                    foreach ($social_footer as $name => $icon) {
                        $network = get_option($name);
                        if($network) {
                            echo '<a href="' . $network . '" target="_blank" class="networks ' . $name .'">' . $icon . '</a>';
                        }
                    }
                ?>
            </div>
        </div>
    </div>
</footer>
<div class="scroll-to-top">
    <?php
    $btn_to_top = get_option('btn_up_text');
    echo ($btn_to_top) ? $btn_to_top : 'Вверх';
    ?>
</div>
<?php wp_footer(); ?>
</body>
</html>