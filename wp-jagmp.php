<?php
/*
Plugin Name: WP JAGMP (Wired Media)
Plugin URI:
Description: Google map shortcode with postcode geolocator and widget
Version: 1.0
Author: Wired Media (carl)
Author URI: http://wiredmedia.co.uk
License: GPLv2
*/

namespace JAGMP;

define('JAGMP_OPTION', 'jagmp_store');

require_once dirname(__FILE__) . '/upgrade.php';
require_once dirname(__FILE__) . '/api.php';
require_once dirname(__FILE__) . '/widget.php';

class Plugin {

    public function __construct() {
        add_action( 'admin_init', array(&$this, 'check_php_version'));
        add_action( 'init', array(&$this, 'upgrade_plugin'));
        add_shortcode( 'googlemap', array(&$this, 'shortcode'));

        // attached it to both logged in and non logged in ajax functions
        add_action('wp_ajax_jagmp_store_map_geolocation', array(&$this, 'store_map_geolocation'));
        add_action('wp_ajax_nopriv_jagmp_store_map_geolocation', array(&$this, 'store_map_geolocation'));
    }

    public function upgrade_plugin(){
        new Upgrade();
    }

    public function check_php_version(){
        if( version_compare(PHP_VERSION, '5.3', '<') ) {
            $plugin = plugin_basename( __FILE__ );
            if( is_plugin_active($plugin) ) {
                deactivate_plugins($plugin);
                add_action('admin_notices', function(){
                $plugin_data = get_plugin_data( __FILE__, false );
                echo '<div class="error">
                <p>Sorry <strong>'. $plugin_data['Name'] .'</strong> requires PHP 5.2 or higher! your PHP version is '. PHP_VERSION .'. The plugin was not activated.</p>
                </div>';
                });
            }
        }
    }

    public function shortcode($atts){

        extract( shortcode_atts( array(
            'lat' => null,
            'lng' => null,
            'postcode' => null,
            'zoom' => 16,
            'maptype' => 'roadmap',
            'id' => 'mapcanvas',
            'class' => 'mapcanvas',
            'marker' => '',
            'disableui' => ''
        ), $atts ) );



        // load up js
        wp_register_script( 'googlemaps', 'http://maps.googleapis.com/maps/api/js?sensor=false', false, '', true );
        wp_register_script( 'jagmp', plugins_url('js/map.js', __FILE__), array('googlemaps', 'jquery'), '', true );
        wp_enqueue_script( 'googlemaps' );
        wp_enqueue_script( 'jagmp' );

        // print map vars for use in js
        wp_localize_script(
            'jagmp', 'jagmp' .'_'. $id,
            array(
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
                'lat' => $lat,
                'lng' => $lng,
                'zoom' => $zoom,
                'maptype' => $maptype,
                'marker' => $marker,
                'postcode' => $postcode,
                'disableui' => $disableui
            )
        );

        // return map canvas
        return '<div id="'. $id .'" class="'. $class .' jagmp-gmap"></div>';
    }

    public function store_map_geolocation(){
        $postcode = str_replace(' ', '', strtolower(esc_js( $_POST['postcode'] )));
        $lat = esc_js( $_POST['lat'] );
        $lng = esc_js( $_POST['lng'] );

        $cache = json_decode(get_option(JAGMP_OPTION));

        if(empty($cache)){ // no cache
            $cache = array(
                $postcode => array(
                    'lat' => $lat,
                    'lng' => $lng
                )
            );
        }else{
            $cache->{$postcode}->lat = $lat;
            $cache->{$postcode}->lng = $lng;
        }

        update_option(JAGMP_OPTION, json_encode($cache));

        exit;
    }

}

new Plugin;