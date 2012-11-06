/*
 * module: googleMaps
 * uses: google map api
 * description:
 ---------------------------------------- */
var JAGMP = (function (module) {

    module.googleMaps = function(){

        $(document).ready(function (){ init() });

        function init(){

            $maps = $('.jagmp-gmap');

            if( $maps.length ){
                $maps.each(function() {
                    map( $(this).attr('id') );
                });
            }
        }

        function map( id ){

            // look for vars outputted on page
            var mapVars = window['jagmp_' + id];

            if(!mapVars){
                return;
            }

            if(mapVars.lat && mapVars.lng ){
                drawMap(mapVars);
            }else if(mapVars.postcode){
                // get the lat and lng from the provided postcode using the google map geocoder
                var geocoder = new google.maps.Geocoder();
                geocoder.geocode(
                    {'address': mapVars.postcode },
                    function(data, status){
                        mapVars.lat = data[0].geometry.location.lat();
                        mapVars.lng = data[0].geometry.location.lng();
                        drawMap(mapVars);

                        // store map in wp options
                        $.post(
                            mapVars.ajaxurl,
                            {
                                action:"jagmp_store_map_geolocation",
                                'cookie' : encodeURIComponent(document.cookie),
                                postcode : mapVars.postcode,
                                lat : mapVars.lat,
                                lng : mapVars.lng
                            },
                            function( data ){

                            },
                            'json'
                        );
                    }
                );
            }

            function drawMap(mapVars){

                var latlng = new google.maps.LatLng( parseFloat(mapVars.lat), parseFloat(mapVars.lng) );

                var mapOptions = {
                    center: latlng,
                    zoom: parseInt(mapVars.zoom),
                    mapTypeId: mapVars.maptype
                }

                if(mapVars.disableui){
                    mapOptions.disableDefaultUI = true;
                }

                var map = new google.maps.Map(document.getElementById(id), mapOptions);

                if( mapVars.marker !== '' ){
                    var marker = new google.maps.Marker({
                        position: latlng,
                        title:mapVars.marker
                    });
                    marker.setMap(map);
                }
            }

        } // END: map

    }();

    return module;

}(JAGMP || {}));