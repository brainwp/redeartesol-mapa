jQuery(document).ready(function($){

var _map_id = 'map';
var _map = $('#' + _map_id);

if (!_map.length)
    return false;

request = {
    QueryString : function(item){
        var svalue = location.search.match(new RegExp("[\?\&]" + item + "=([^\&]*)(\&?)","i"));
        return svalue ? svalue[1] : svalue;
    }
}

var options;

var embed = maptheme.embed;

if (embed == true) {
    options = {
        center: new google.maps.LatLng(maptheme.lat, maptheme.lng),
        zoom: 5,
        maxZoom: 17,
        streetViewControl: false,
        scrollwheel: false,
    };
} else {
    options = {
        center: new google.maps.LatLng(maptheme.lat, maptheme.lng),
        zoom: 4,
        maxZoom: 17,
        streetViewControl: false,
    };
}

var map = new google.maps.Map(document.getElementById(_map_id), options);
var hovercard = new google.maps.InfoWindow({});

var markers = [];
var marker_options = {
    styles: [{
        anchorIcon: [ 45, 39 ],
        //fontFamily: Oswald,
        fontWeight: 'bold',
        width: 45,
        height: 39,
        textSize: 14,
        url: maptheme.imgbase + 'marker.png',
    }]
};

google.maps.event.addDomListener(window, 'load', function(e) {

    for (u in maptheme.users) {
        var user = maptheme.users[u];
        var image = new google.maps.MarkerImage(
            maptheme.imgbase + 'marker.png',
            null,
            new google.maps.Point(0,0),
            new google.maps.Point(45, 39)
        );
        var marker = new google.maps.Marker({
            position: new google.maps.LatLng(user.lat, user.lng),
            map: map,
            icon: image
        });
        marker.set('user', user);
        markers.push(marker);
        google.maps.event.addListener(marker, 'click', function(){
            hovercard.setContent('<div id="loading" style="color:#444">Buscando...</div>');
            hovercard.open(map, this);
            var query_user = this.user;
            var data = {
                'action': 'get_user_info',
                'id': query_user.ID     
            };
            // We can also pass the url value separately from ajaxurl for front end AJAX implementation
            $.post(maptheme.ajax_url, data, function(response) {
                hovercard.setContent(response);
            });
        });
    }
    var markerCluster = new MarkerClusterer(map, markers, marker_options);
});

});
