<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <?php if ( isset( $_GET['embed'] ) ) : ?>
            <base target="_parent">
        <?php endif;?>
        <meta charset="<?php bloginfo( 'charset' ); ?>" />
        <meta name="google" value="notranslate"><!--  this avoids problems with hash change and the google chrome translate bar -->
        <title><?php
            global $page, $paged;
            wp_title( '|', true, 'right' );
            bloginfo( 'name' );
            $site_description = get_bloginfo( 'description', 'display' );
            ?></title>
        <link rel="profile" href="http://gmpg.org/xfn/11" />
        <link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
        <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
        <?php wp_head(); ?>
    </head>

    <body <?php body_class(); ?>>
        <div id="loading-bg"></div><!-- #loading-bg -->
        <div id="map"></div>

        <div id="blog-title">
            <a href="<?php echo home_url(); ?>">
                <?php if(show_alt_logo()):?>
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/header-projetos.png" />
                <?php else:?>
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/header.png" />
                <?php endif;?>
            </a>
        </div>
        <?php wp_nav_menu( array( 'container_class' => 'map-menu-top', 'theme_location' => 'mapasdevista_top', 'fallback_cb' => false ) ); ?>

        <?php $menu_positions = get_theme_mod('nav_menu_locations'); ?>
        <?php if (isset($menu_positions['mapasdevista_side']) && $menu_positions['mapasdevista_side'] != '0'): ?>
            <div id="toggle-side-menu">
                <?php mapasdevista_image("side-menu.png", array("id" => "toggle-side-menu-icon")); ?>
            </div>
        <?php endif; ?>
            <?php $current_link = array('type_pin' => array());?>
            <?php if(isset($_GET['type_pin']) && is_array($_GET['type_pin'])):?>
                <?php $current_link['type_pin'] = $_GET['type_pin'];?>
            <?php endif;?>
            <?php if(isset($_GET['embed'])):?>
                <?php $current_link['embed'] = 'true';?>
            <?php endif;?>
            <div id="legenda" data-url="<?php echo home_url();?>">
                <h3>Filtros</h3>
                <label>
                    <?php $link = $current_link;?>
                    <?php $link['type_pin'][] = 'projetos';?>
                    <?php $link_end = add_query_arg($link,home_url('/'));?>
                    <input type="checkbox" value="<?php echo esc_url($link_end);?>" <?php if(isset($_GET['type_pin']) && in_array('projetos',$_GET['type_pin'])) echo 'checked class="current"';?> data-slug="projetos">
                    <img src="<?php echo get_template_directory_uri();?>/img/projetos-legenda.jpg">
                </label>
                <label>
                    <?php $link = $current_link;?>
                    <?php $link['type_pin'][] = 'associacoes';?>
                    <?php $link_end = add_query_arg($link,home_url('/'));?>
                    <input type="checkbox" value="<?php echo esc_url($link_end);?>" <?php if(isset($_GET['type_pin']) && in_array('associacoes',$_GET['type_pin'])) echo 'checked class="current"';?> data-slug="associacoes">
                    <img src="<?php echo get_template_directory_uri();?>/img/associacoes-legenda.jpg">
                </label>
                <label>
                    <?php $link = $current_link;?>
                    <?php $link['type_pin'][] = 'artesao';?>
                    <?php $link_end = add_query_arg($link,home_url('/'));?>
                    <input type="checkbox" value="<?php echo esc_url($link_end);?>" <?php if(isset($_GET['type_pin']) && in_array('artesao',$_GET['type_pin'])) echo 'checked class="current"';?> data-slug="artesao">
                    <img src="<?php echo get_template_directory_uri();?>/img/artesao-legenda.jpg">
                </label>
                <label>
                    <?php $link = $current_link;?>
                    <?php $link['type_pin'][] = 'lojistas';?>
                    <?php $link_end = add_query_arg($link,home_url('/'));?>
                    <input type="checkbox" value="<?php echo esc_url($link_end);?>" <?php if(isset($_GET['type_pin']) && in_array('lojistas',$_GET['type_pin'])) echo 'checked class="current"';?> data-slug="lojistas">
                    <img src="<?php echo get_template_directory_uri();?>/img/lojistas-legenda.jpg">
                </label>
                <label>
                    <?php $link = $current_link;?>
                    <?php $link['type_pin'][] = 'agentes';?>
                    <?php $link_end = add_query_arg($link,home_url('/'));?>
                    <input type="checkbox" value="<?php echo esc_url($link_end);?>" <?php if(isset($_GET['type_pin']) && in_array('agentes',$_GET['type_pin'])) echo 'checked class="current"';?> data-slug="agentes">
                    <img src="<?php echo get_template_directory_uri();?>/img/agentes-legenda.jpg">
                </label>
                <?php $clean_url = home_url();?>
                <?php if ( isset( $_GET['embed'] ) ) : ?>
                    <?php $clean_url = home_url( '/?embed' );?>
                <?php endif;?>
                <a href="<?php echo $clean_url;?>" class="btn-reset">Limpar</a>
            </div>
        <div id="posts-loader">
            <span id="posts-loader-loaded">0</span>/<span id="posts-loader-total">0</span> <span><?php _e('users', 'mapasdevista'); ?></span>
        </div>

        <?php wp_nav_menu( array( 'container_class' => 'map-menu-side', 'theme_location' => 'mapasdevista_side', 'fallback_cb' => false ) ); ?>
