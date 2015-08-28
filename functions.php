<?php

add_action( 'wp_enqueue_scripts', 'enqueue_scripts' );
function enqueue_scripts() {

    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'google-maps-v3', 'http://maps.google.com/maps/api/js?sensor=false' );
    wp_enqueue_script( 'maptheme', get_stylesheet_directory_uri() . '/js/map.js' );
    wp_enqueue_script( 'markerclusterer', get_stylesheet_directory_uri() . '/js/markerclusterer.min.js' );

    if ( !defined( 'GEOUSER_INITIAL_LAT' ) || !GEOUSER_INITIAL_LAT
        || !defined( 'GEOUSER_INITIAL_LNG' ) || !GEOUSER_INITIAL_LNG ) {
        // Brazil
        define( 'GEOUSER_INITIAL_LAT', -15 );
        define( 'GEOUSER_INITIAL_LNG', -55 );
    }

    $params['ajax_url'] = admin_url( 'admin-ajax.php' );
    $params['lat'] = GEOUSER_INITIAL_LAT;
    $params['lng'] = GEOUSER_INITIAL_LNG;
    $params['imgbase'] = get_stylesheet_directory_uri() . '/img/';
    $users = get_map_users();
    $posts = get_map_posts();
    $params['users'] = array_merge($users,$posts);
    if(isset($_GET['embed'])){
        $params['embed'] = true;
    }
    wp_localize_script( 'maptheme', 'maptheme', $params );
}

function get_map_users() {

    global $wpdb;

    if ( $users = get_transient( 'map_users' ) ){
        if(!isset($_GET['type_pin']) && !isset($_GET['filter_type']) && !isset($_GET['membros_perfil']) && !isset($_GET['user_category']) && !isset($_GET['user_state'])) {
            return $users;
        }

        $users_filter = array();
        foreach($users as $key => $user){
            $add = false;
            if(isset($_GET['type_pin']) && !empty($_GET['type_pin'])){
                $type = get_user_meta( $user['ID'], 'type_pin', true );
                if($type && in_array($type, $_GET['type_pin'])){
                    $add = true;
                }
                else{
                    $add = false;
                }
            }
            if(isset($_GET['filter_type']) && !empty($_GET['filter_type'])){
                $type = get_user_meta( $user['ID'], 'user_type', true );
                if($type && $type == $_GET['filter_type']){
                    $add = true;
                }
                else{
                    $add = false;
                }
            }
            if(isset($_GET['membros_perfil']) && !empty($_GET['membros_perfil'])){
                $type = get_user_meta( $user['ID'], 'membros_perfil', true );
                if($type && $type == $_GET['membros_perfil']){
                    $add = true;
                }
                else{
                    $add = false;
                }
            }
            if(isset($_GET['user_category']) && !empty($_GET['user_category'])){
                $type = get_user_meta( $user['ID'], 'user_category', true );
                if($type && $type == $_GET['user_category']){
                    $add = true;
                }
                else{
                    $add = false;
                }
            }
            if(isset($_GET['user_state']) && !empty($_GET['user_state'])){
                $type = get_user_meta( $user['ID'], 'user_state', true );
                if($type && $type == $_GET['user_state']){
                    $add = true;
                }
                else{
                    $add = false;
                }
            }
            if($add == true){
                $users_filter[] = $user;
            }
        }
        return $users_filter;
    }

    $query = $wpdb->get_results( "
        SELECT user_id, user_email, display_name, meta_value
        FROM {$wpdb->users}, {$wpdb->usermeta}
        WHERE 1=1
            AND user_id = ID
            AND meta_key = 'location'
    " );

    $users = array();
    foreach( $query as $q ) {
        $loc = unserialize( $q->meta_value );
        if ( empty( $loc[0] ) || empty( $loc[1] ) )
            continue;
        $users[] = array(
            'ID' => $q->user_id,
            'display_name' => $q->display_name,
            'gravatar' => md5( $q->user_email ),
            'lat'  => $loc[0],
            'lng'  => $loc[1],
            'icon' => get_template_directory_uri() . '/img/pins/' . get_user_meta( $q->user_id, 'type_pin', true ) . '.png',
        );
    }

    set_transient( 'map_users', $users, 3600 * 24 );

    return $users;

}
function get_map_posts(){
    if(isset($_GET['type_pin']) && !in_array('projetos', $_GET['type_pin']))
        return array();

    $url = get_template_directory_uri();
    if(get_option('wpmu_main_site')){
        $main_id = get_option('wpmu_main_site');
    }
    else{
        update_option('wpmu_main_site', 1);
        $main_id = 1;
    }
    switch_to_blog($main_id);
    // WP_Query arguments
    $args = array (
        'post_type' => array( 'projetos' ),
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key'     => 'project_map',
                'compare' => 'EXISTS',
            ),
        ),
    );

    if(isset($_GET['user_state']) && !empty($_GET['user_state'])){
        $args['membros_state'] = $_GET['user_state'];
    }
    if(isset($_GET['user_category']) && !empty($_GET['user_category'])){
        $args['membros_category'] = $_GET['user_category'];
    }
    if(isset($_GET['filter_type']) && !empty($_GET['filter_type'])){
        $args['tipos'] = $_GET['filter_type'];
    }
    // The Query
    $query = new WP_Query( $args );
    $posts = array();
    // The Loop
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $id = get_the_ID();
            $loc = get_post_meta( get_the_ID(), 'project_map', true);
            $posts[] = array(
                'ID' => $id,
                'is_post' => true,
                'display_name' => get_the_title(),
                'lat'  => $loc['lat'],
                'lng'  => $loc['lng'],
                'icon' => $url . '/img/pins/projetos.png',
            );

        }
        // Restore original Post Data
        wp_reset_postdata();
    }
    restore_current_blog();
    return $posts;
}
function remove_pins($html){
    return null;
}
add_filter('geouser_map_pins','remove_pins');
if ( isset( $_GET['embed'] ) )
    add_filter('show_admin_bar', '__return_false');

//add user fields
require_once get_template_directory() . '/core/classes/class-user-meta.php';
require get_template_directory() . '/inc/user-fields.php';

//ajax mapa

function get_user_info_ajax() {
    if(empty($_POST['id']))
        die();
    $user_data = get_userdata( $_POST['id'] );
    echo '<div class="hovercard" style="width:400px; color:#444;">';
    if(get_user_meta($_POST['id'],'rede-avatar',true)){
        echo '<div class="col" style="float:left; width:85px">';
        echo '<span class="thumbnail">';
        echo wp_get_attachment_image( get_user_meta($_POST['id'],'rede-avatar',true), 'thumbnail', 0, array('class' => 'photouser'));
        echo '</span>';
        echo '</div>';
    }
    echo '<div class="col" style="float:right; width: 215px">';
    echo '<span class="display-name" style="display:block; font-weight:bold; font-size:1.2em;">'; 
    echo $user_data->display_name;
    echo '</span>';
    echo '<div style="width:100%;height:5px;clear:both"></div>';
    if(!empty($user_data->description)){
        echo esc_textarea($user_data->description);
    }
    echo '<div style="width:100%;height:5px;clear:both"></div>';
    if(get_user_meta($_POST['id'],'endereco',true)){
        echo '<b>Endereco:</b> ' . esc_textarea(get_user_meta($_POST['id'],'endereco',true));
    }
    echo '<div style="width:100%;height:5px;clear:both"></div>';
    if(!empty($user_data->user_email)){
        echo '<b>E-mail:</b> ' . esc_textarea($user_data->user_email);
    }
    echo '<div style="width:100%;height:5px;clear:both"></div>';
    if(get_user_meta($_POST['id'],'telefone',true)){
        echo '<b>Telefone:</b> ' . esc_textarea(get_user_meta($_POST['id'],'telefone',true));
    }
    echo '<div style="width:100%;height:5px;clear:both"></div>';
    if(get_user_meta($_POST['id'],'arte_type',true)){
        echo '<b>Tipo de artesão:</b> ' . esc_artetype(get_user_meta($_POST['id'],'arte_type',true));
    }
    echo '<div style="width:100%;height:5px;clear:both"></div>';
    if(get_user_meta($_POST['id'],'link-leia',true)){
        $link = esc_url(get_user_meta($_POST['id'],'link-leia',true));
        echo '<a href="'.$link.'">';
        echo 'Leia mais';
        echo '</a>';
    }
    echo '</div>';
    echo '</div>';
    die();
}
add_action( 'wp_ajax_nopriv_get_user_info', 'get_user_info_ajax' );
add_action( 'wp_ajax_get_user_info', 'get_user_info_ajax' );


function get_post_info_ajax() {
    if(empty($_POST['id']))
        die();

    if(get_option('wpmu_main_site')){
        $main_id = get_option('wpmu_main_site');
    }
    else{
        update_option('wpmu_main_site', 1);
        $main_id = 1;
    }
    switch_to_blog($main_id);

    $post = get_post( $_POST['id'] );
    setup_postdata( $post ); 
    echo '<div class="hovercard" style="width:400px; color:#444;">';
    if( has_post_thumbnail($post->ID) ) {
        echo '<div class="col" style="float:left; width:85px">';
        echo '<span class="thumbnail">';
        echo wp_get_attachment_image( get_post_thumbnail_id($post->ID), 'thumbnail', 0, array('class' => 'photouser'));
        echo '</span>';
        echo '</div>';
    }
    echo '<div class="col" style="float:right; width: 215px">';
    echo '<span class="display-name" style="display:block; font-weight:bold; font-size:1.2em;">'; 
    echo get_the_title($post->ID);
    echo '</span>';
    echo '<div style="width:100%;height:5px;clear:both"></div>';
    echo get_the_excerpt();
    echo '<div style="width:100%;height:5px;clear:both"></div>';
    if(get_post_meta($post->ID, 'project_local', true)){
        echo '<b>Cidade:</b> ' . esc_textarea(get_post_meta($post->ID, 'project_local', true));
    }
    echo '<div style="width:100%;height:5px;clear:both"></div>';
    if(get_post_meta($post->ID, 'project_type', true)){
        echo '<b>Gênero:</b> ' . esc_textarea(get_post_meta($post->ID,'project_type',true));
    }
    echo '<div style="width:100%;height:5px;clear:both"></div>';
    if(get_user_meta($_POST['id'],'link-leia',true)){
        $link = get_permalink($post->ID);
        echo '<a href="'.$link.'">';
        echo 'Leia mais';
        echo '</a>';
    }
    echo '</div>';
    echo '</div>';
    restore_current_blog();

    die();
}
add_action( 'wp_ajax_nopriv_get_post_info', 'get_post_info_ajax' );
add_action( 'wp_ajax_get_post_info', 'get_post_info_ajax' );

function esc_artetype($key){
    $types = array(
        'associacao'   => __('Associação','odin'),
        'artesao'   => __('Artesão ou mestre','odin'),
        'lojistas'     => __('Lojistas','odin'),
        'aceleradoras' => __('Aceleradoras','odin'),
        'projetos' => __('Projetos','odin'),
    );

    return esc_textarea($types[$key]);
}
function show_alt_logo(){
    if(!isset($_GET['type_pin']))
        return false;
    if(!in_array('projetos', $_GET['type_pin']))
        return false;
    if(in_array('associacoes', $_GET['type_pin']))
        return false;
    if(in_array('artesao', $_GET['type_pin']))
        return false;
    if(in_array('lojistas', $_GET['type_pin']))
        return false;
    if(in_array('agentes', $_GET['type_pin']))
        return false;
    
    return true;
}
?>
