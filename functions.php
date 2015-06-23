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
    $params['users'] = get_map_users();
    if(isset($_GET['embed'])){
        $params['embed'] = true;
    }
    wp_localize_script( 'maptheme', 'maptheme', $params );

}

function get_map_users() {

    global $wpdb;

    if ( $users = get_transient( 'map_users' ) ){
        if(!isset($_GET['type_pin']) && !isset($_GET['filter_type'])){
            return $users;
        }

        $users_filter = array();
        foreach($users as $key => $user){
            $add = false;
            if(isset($_GET['type_pin']) && !empty($_GET['type_pin'])){
                $type = get_user_meta( $user['ID'], 'type_pin', true );
                if($type && $type == $_GET['type_pin']){
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
            'lat' => $loc[0],
            'lng' => $loc[1]
        );
    }

    set_transient( 'map_users', $users, 3600 * 24 );

    return $users;

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
function esc_artetype($key){
    $types = array(
        'associacao'   => __('Associação','odin'),
        'individual'   => __('Artesão individual','odin'),
        'indigena'     => __('Indígena','odin'),
        'mestre'       => __('Meste','odin'),
    );

    return esc_textarea($types[$key]);
}
?>
