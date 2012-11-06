<?php

/* function for getting a postcodes stored geolocation
-------------------------------------------------------------------------------------*/
function JAGMP_make_shortcode($args = null){
    $defaults = array(
        'lat' => null,
        'lng' => null,
        'postcode' => null,
        'zoom' => 16,
        'maptype' => 'roadmap',
        'id' => 'mapcanvas',
        'class' => 'mapcanvas',
        'marker' => '',
        'disableui' => ''
    );
    $params = wp_parse_args( $args, $defaults );

    $shortcode = '[googlemap';
        $shortcode .= JAGMP_get_map_geolocation($params['postcode']);
        $shortcode .= ' postcode="'. $params['postcode'] .'" ';
        $shortcode .= ' zoom="'. $params['zoom'] .'" ';
        $shortcode .= ' maptype="'. $params['maptype'] .'" ';
        $shortcode .= ' id="'. $params['id'] .'" ';
        $shortcode .= ' class="'. $params['class'] .'" ';
        $shortcode .= ' marker="'. $params['marker'] .'" ';
        $shortcode .= ' disableui="'. $params['disableui'] .'" ';
    $shortcode .= ']';

    return $shortcode;
}

function JAGMP_get_map_geolocation($postcode){
    $cache = json_decode(get_option(JAGMP_OPTION));
    $postcode = str_replace(' ', '', strtolower($postcode));

    if(!empty($cache) && isset($cache->{$postcode})){
        return ' lat="'. $cache->{$postcode}->lat .'" lng="'. $cache->{$postcode}->lng .'" ';
    }else{
        return ' lat="" lng="" ';
    }
}